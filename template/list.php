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
	</tr>
<?php foreach ($model as $config) { ?>
	<tr>
		<th class="action"><a href="list.php?del=<?php echo urlencode($config); ?>"><img src="image/delete.png" alt="Delete"/></a></th>
		<td><a href="edit.php?mac=<?php echo urlencode($config); ?>"><?php echo $config; ?></a></td>
	</tr>
<?php } ?>
</table>
<script>
</script>
