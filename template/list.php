<div id="nav">
	<a href="access.php?access=logout">Logout</a> |
	<a href="list.php">Configurations</a>
</div>
<table>
	<caption>Configurations</caption>
	<tr>
		<th class="action" rowspan="2">
			<a href="edit.php"><img src="image/add.png" alt="Add"/></a>
			<a href="list.php?del=all"><img name="delete-all" src="image/delete.png" alt="Delete All"/></a>
		</th>
		<th rowspan="2">MAC Address</th>
		<th rowspan="2">Username</th>
		<th colspan="2">Switch</th>
		<th colspan="3">Gateway</th>
	</tr>
	<tr>
		<th>Host</th>
		<th>Call Limit</th>
		<th>Prefix</th>
		<th>Host</th>
		<th>Port</th>
	</tr>
<?php
foreach ($model as $config) {
	$maxrowspan =  max(1, count($config["switch"]), count($config["gateway"]));
	for ($rowspan = $maxrowspan, $i = 0; $rowspan > 0; $rowspan--, $i++) {
?>
	<tr>
<?php
		if ($i == 0) {
?>
		<th class="action" rowspan="<?php echo $rowspan; ?>"><a href="list.php?del=<?php echo urlencode($config["mac"]); ?>"><img name="delete" src="image/delete.png" alt="Delete"/></a></th>
		<td rowspan="<?php echo $rowspan; ?>"><a href="edit.php?mac=<?php echo urlencode($config["mac"]); ?>"><?php echo $config["mac"]; ?></a></td>
		<td rowspan="<?php echo $rowspan; ?>"><?php echo htmlspecialchars($config["username"]); ?></td>
<?php
		}
?>
<?php
		if ($i == 0 || $i < count($config["switch"])) {
			if (count($config["switch"]) == 0 ||
			    $i == count($config["switch"]) - 1)
				$span = $rowspan;
			else
				$span = 1;
?>
		<td rowspan="<?php echo $span; ?>"><?php echo $config["switch"][$i]["host"]; ?></td>
		<td rowspan="<?php echo $span; ?>"><?php echo $config["switch"][$i]["call-limit"]; ?></td>
<?php
		}

		if ($i == 0 || $i < count($config["gateway"])) {
			if (count($config["gateway"]) == 0 ||
			    $i == count($config["gateway"]) - 1)
				$span = $rowspan;
			else
				$span = 1;
?>
		<td rowspan="<?php echo $span; ?>"><?php echo htmlspecialchars($config["gateway"][$i]["prefix"]); ?></td>
		<td rowspan="<?php echo $span; ?>"><?php echo $config["gateway"][$i]["host"]; ?></td>
		<td rowspan="<?php echo $span; ?>"><?php echo $config["gateway"][$i]["port"]; ?></td>
<?php
		}
?>
	</tr>
<?php
	}
}
?>
</table>
<script>
$(document).ready(function() {
	$('img[name|="delete"]').click(function(event) {
		if ($(this).attr("name") == "delete")
			var msg = "this configuration";
		else
			var msg = "all the configurations";
		if (!confirm("Are you sure to delete " + msg + "?"))
			return false;
	});
});
</script>
