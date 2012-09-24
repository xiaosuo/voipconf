<?php
/**
 * Ini manipulation
 */

class Ini {
	var $m_ini;

	function Ini() {
		$this->m_ini = Array();
	}

	function load($fn) {
		$this->m_ini = Array();
		$fh = fopen($fn, "r");
		if (!$fh)
			return false;
		$section = "";
		while (($line = fgets($fh)) !== false) {
			$line = trim($line);

			// ignore blank lines
			if (strlen($line) == 0)
				continue;

			if (substr($line, 0, 1) == "[") {
				if (substr($line, -1) != "]") {
					fclose($fh);
					return false;
				}
				$section = substr($line, 1, -1);
				continue;
			}

			list($name, $value) = explode("=", $line, 2);
			$name = trim($name);
			// TODO: comma separated values?
			$value = trim($value);
			if (!$this->add($section, $name, $value)) {
				fclose($fh);
				return false;
			}
		}
		fclose($fh);

		return true;
	}

	function validate($section, $name) {
		return $section != "" && $name != "";
	}

	function get($section, $name) {
		if (!$this->validate($section, $name))
			return false;

		return $this->m_ini[$section][$name];
	}

	function set($section, $name, $value) {
		if (!$this->validate($section, $name))
			return false;

		$this->m_ini[$section][$name] = $value;

		return true;
	}

	function add($section, $name, $value) {
		if (!$this->validate($section, $name))
			return false;

		if (isset($this->m_ini[$section][$name])) {
			if (!is_array($this->m_ini[$section][$name])) {
				$ovalue = $this->m_ini[$section][$name];
				$this->m_ini[$section][$name] = array();
				$this->m_ini[$section][$name][] = $ovalue;
			}
			$this->m_ini[$section][$name][] = $value;
		} else {
			$this->m_ini[$section][$name] = $value;
		}

		return true;
	}

	function dump($fn) {
		$fh = fopen($fn, "w");
		if (!$fh)
			return false;
		foreach (array_keys($this->m_ini) as $section) {
			fwrite($fh, "[$section]\n");
			foreach (array_keys($this->m_ini[$section]) as $name) {
				if (is_array($this->m_ini[$section][$name])) {
					foreach ($this->m_ini[$section][$name] as $value)
						fwrite($fh, "$name=$value\n");
				} else {
					fwrite($fh, "$name=" . $this->m_ini[$section][$name] . "\n");
				}
			}
			fwrite($fh, "\n");
		}
		fclose($fh);
	}

	function sections() {
		return array_keys($this->m_ini);
	}

	function deleteSection($section) {
		if ($section != "")
			unset($this->m_ini[$section]);
	}
}

/*
$ini = new Ini();
$ini->load("data/sip.conf");
var_dump($ini->get("general", "allow"));
var_dump($ini->get("general", "bindport"));
$ini->dump("sip.conf");
*/

?>
