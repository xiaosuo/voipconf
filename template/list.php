<div id="nav">
	<a href="access.php?access=logout">Logout</a> |
	<a href="list.php">List</a>
</div>
<table>
	<caption>The configurations</caption>
	<tr>
		<th class="action">
			<a href="edit.php"><img src="image/add.png" alt="Add"/></a>
			<a href="list.php?del=all"><img src="image/delete.png" alt="Delete All"/></a>
		</th>
		<th>MAC Address</th>
		<th>Username</th>
		<th>Switch</th>
		<th>Gateway</th>
	</tr>
<?php
foreach ($model as $config) {
?>
	<tr>
		<th class="action"><a href="list.php?del=<?php echo urlencode($config["mac"]); ?>"><img src="image/delete.png" alt="Delete"/></a></th>
		<td><a href="edit.php?mac=<?php echo urlencode($config["mac"]); ?>"><?php echo $config["mac"]; ?></a></td>
		<td><?php echo htmlspecialchars($config["username"]); ?></td>
		<td>
			<ul>
<?php
	foreach ($config["switch"] as $switch) {
?>
				<li>
					<?php echo $switch["host"]; ?>(<?php echo $switch["call-limit"]; ?>)
				</li>
<?php
	}
?>
			</ul>
		</td>
		<td>
			<ul>
<?php
	foreach ($config["gateway"] as $gateway) {
?>
				<li>
					<?php echo $gateway["prefix"] ?>-<?php echo $gateway["host"]; ?>:<?php echo $gateway["port"]; ?>
				</li>
<?php
	}
?>
			</ul>
		</td>
	</tr>
<?php
}
?>
</table>
<script>
</script>
