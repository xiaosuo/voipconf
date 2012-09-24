<?php
class ExtParser {
	var $m_buf;

	function ExtParser() {
		$this->m_buf = "";
	}

	/**
	 * Extract a bracket enclosed block
	 * Return the length of the block on success
	 * Return false on error
	 */
	function extractBlock($buf) {
		$matches = array();
		$n = preg_match_all("/[{}]/", $buf, $matches,
				PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		if ($n === 0 || $n === false)
			return false;
		$depth = 0;
		for ($i = 0; $i < $n; $i++) {
			$match = $matches[$i][0][0];
			$offset = $matches[$i][0][1];
			if ($match == "{")
				$depth++;
			else
				$depth--;
			if ($depth == 0)
				return $offset + 1;
		}

		return false;
	}

	/**
	 * Parse a recode
	 * i.e.
	 * _111X.     => {
	 *	Dial(SIP/${EXTEN:3}@192.168.102.1:5060,60);
	 *	Hangup();
	 * }
	 */
	function parseRecord($record) {
		list($prefix, $body) = preg_split("/=>/", $record, 2);

		$prefix = rtrim($prefix);
		$body = ltrim($body);
		if (substr($prefix, 0, 1) != "_" || substr($prefix, -2) != "X.")
			die("ExtParser: invalid prefix");
		$this->handlePrefix(substr($prefix, 1, -2));
		if (substr($body, 0, 1) != "{")
			die("ExtParser: syntax error");
		if (substr($body, -1) != "}")
			die("ExtParser: syntax error");
		$body = trim(substr($body, 1, -1));
		foreach (explode(";", $body) as $func) {
			$func = trim($func);
			if (strlen($func) == 0)
				continue;
			$matches = array();
			if (preg_match("/^([^(]+)\(([^)]*)\)$/", $func,
				       $matches) !== 1)
				die("ExtParser: syntax error");
			$this->handleFunction($matches[1]);
			if ($matches[2] != "") {
				foreach (explode(",", $matches[2]) as $para)
					$this->handleParameter(trim($para));
			}
		}
	}

	/**
	 * Parse a context
	 * i.e.
	 * context user01 {
	 *	_111X.     => {
	 *		Dial(SIP/${EXTEN:3}@192.168.102.1:5060,60);
	 *		Hangup();
	 *	}
	 *	_222X.     => {
	 *		Dial(SIP/${EXTEN:3}@192.168.102.2:5060,60);
	 *		Hangup();
	 *	}
	 * }
	 */
	function parseContext($context) {
		list($keyword, $username, $content) = preg_split("/\s+/",
				$context, 3);
		if ($keyword != "context")
			die("ExtParser: invalid keyword");
		$this->handleUsername($username);
		if (substr($content, 0, 1) != "{")
			die("ExtParser: syntax error");
		if (substr($content, -1) != "}")
			die("ExtParser: syntax error");
		$content = trim(substr($content, 1, -1));
		while (strlen($content) > 0) {
			$len = $this->extractBlock($content);
			if ($len === false)
				die("ExtParser: garbage is found");
			$this->parseRecord(substr($content, 0, $len));
			$content = ltrim(substr($content, $len));
		}
	}

	function feed($str) {
		if (strlen($this->m_buf) == 0)
			$str = ltrim($str);
		$this->m_buf .= $str;
		while (strlen($this->m_buf) > 0) {
			$len = $this->extractBlock($this->m_buf);
			if ($len === false)
				return false;
			$this->parseContext(substr($this->m_buf, 0, $len));
			$this->m_buf = ltrim(substr($this->m_buf, $len));
		}

		return true;
	}

	function load($fn) {
		$this->clear();
		return $this->feed(file_get_contents($fn));
	}

	function clear($fn) {
		$this->m_buf = "";
	}

	function handleUsername($username) {
		echo "Username: $username\n";
	}

	function handlePrefix($prefix) {
		echo "Prefix: $prefix\n";
	}
	
	function handleFunction($func) {
		echo "Func: $func\n";
	}

	function handleParameter($para) {
		echo "Param: $para\n";
	}
}

class ExtUsr extends ExtParser {
	var $m_ctx;
	var $m_username;
	var $m_func;
	var $m_para_idx;
	var $m_prefix;

	function ExtUsr() {
		$this->m_ctx = array();
	}

	function get($username) {
		return $this->m_ctx[$username];
	}

	function addUser($username) {
		$this->m_ctx[$username] = array();
	}

	function add($username, $ctx) {
		$this->m_ctx[$username][$ctx["prefix"]] = $ctx;
	}

	function dump($fn) {
		$fh = fopen($fn, "w");
		if (!$fh)
			return false;
		foreach ($this->m_ctx as $username => $cont) {
			fwrite($fh, "context $username {\n");
			foreach ($cont as $rec) {
				$prefix = $rec["prefix"];
				$plen = strlen($prefix);
				$host = $rec["host"];
				$port = $rec["port"];
				fwrite($fh, <<<EOF
	_${prefix}X.	=> {
		Dial(SIP/\${EXTEN:$plen}@$host:$port,60);
		Hangup();
	}

EOF
				);
			}
			fwrite($fh, "}\n\n");
		}
	}

	function clear() {
		$this->m_ctx = array();
		parent::clear();
	}

	function handleUsername($username) {
		$this->m_ctx[$username] = array();
		$this->m_username = $username;
	}

	function handlePrefix($prefix) {
		$this->m_ctx[$this->m_username][$prefix]["prefix"] = $prefix;
		$this->m_prefix = $prefix;
	}

	function handleFunction($func) {
		$this->m_func = $func;
		$this->m_para_idx = 0;
	}

	function handleParameter($para) {
		if ($this->m_func == "Dial" && $this->m_para_idx == 0) {
			list($unused, $hostport) = explode("@", $para);
			list($host, $port) = explode(":", $hostport);
			$this->m_ctx[$this->m_username][$this->m_prefix]["host"] = $host;
			$this->m_ctx[$this->m_username][$this->m_prefix]["port"] = $port;
		}
		$this->m_para_idx++;
	}
}

class ExtAel extends ExtParser {
	var $m_ctx;

	function ExtAel() {
		$this->m_ctx = array();
	}

	function dump($fn) {
		$fh = fopen($fn, "w");
		if (!$fh)
			return false;
		foreach ($this->m_ctx as $username) {
			fwrite($fh, <<<EOF
context $username {
	_X.	=> {
		Dial(SYNC/$username/\${EXTEN},60);
		Hangup();
	}
}

EOF
			);
		}
		fclose($fh);
	}

	function add($username) {
		if (!in_array($username, $this->m_ctx))
			$this->m_ctx[] = $username;
	}

	function delete($username) {
		if (in_array($username, $this->m_ctx))
			unset($this->m_ctx[$username]);
	}

	function clear() {
		$this->m_ctx = array();
		parent::clear();
	}

	function handleUsername($username) {
		$this->m_ctx[] = $username;
	}

	function handlePrefix($prefix) {
		if ($prefix != "")
			die("ExtAel: syntax error");
	}

	function handleFunction($func) {
	}

	function handleParameter($para) {
	}
}

/*
$pasr = new ExtAel();
$pasr->load("data/extensions.ael");
$pasr->dump("ext.ael");
$pasr = new ExtUsr();
$pasr->load("data/extensions.usr");
$pasr->dump("ext.usr");
*/

?>
