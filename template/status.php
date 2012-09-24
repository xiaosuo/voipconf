<div id="nav">
	<a href="access.php?access=logout">Logout</a> |
	<a href="restart.php">Restart</a> |
	<a href="status.php">Status</a> |
	<a href="list.php">Configurations</a>
</div>

<table>
<caption>Summary</caption>
<tr>
	<th>Active Channels</th>
	<td><?php echo $model["active-channels"]; ?></td>
</tr>
<tr>
	<th>Active Calls</th>
	<td><?php echo $model["active-calls"]; ?></td>
</tr>
<tr>
	<th>Calls Processed</th>
	<td><?php echo $model["call-processed"]; ?></td>
</tr>
</table>

<table>
<caption>Channels</caption>
<tr>
	<th>Channel</th><th>Location</th><th>State</th><th>Application(Data)</th>
</tr>
<?php
foreach ($model["channel"] as $chan) {
?>
<tr>
	<td><?php echo $chan["name"]; ?></td>
	<td><?php echo $chan["location"]; ?></td>
	<td><?php echo $chan["state"]; ?></td>
	<td><?php echo $chan["application"]; ?></td>
</tr>
<?php
}
?>
</table>
