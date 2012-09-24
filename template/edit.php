<div id="nav">
	<a href="access.php?access=logout">Logout</a> | 
	<a href="list.php">List</a> |
	<a id="apply" href="#">Apply</a>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">

<table>
<caption>Generic</caption>
<tr>
	<th>Username</th>
	<td><input type="text" name="username" value="<?php echo htmlspecialchars($model["username"]); ?>"/></td>
</tr>
<tr>
	<th>Password</th>
	<td><input type="text" name="password" value="<?php echo htmlspecialchars($model["password"]); ?>"/></td>
</tr>
<tr>
	<th>MAC Address</th>
	<td><input type="text" name="mac" value="<?php echo htmlspecialchars($model["mac"]); ?>"/></td>
</tr>
</table>

<table>
<caption>Switch</caption>
<tr>
	<th class="action">
		<img name="add-switch" src="image/add.png" alt="Add a Switch"/>
		<img name="delete-all" src="image/delete.png" alt="Delete All Switches"/>
	</th>
	<th>Host</th>
	<th>Call Limit</th>
</tr>
<?php
foreach ($model["switch"] as $i => $switch) {
?>
<tr>
	<th class="action">
		<img name="delete" src="image/delete.png" alt="Delete a Switch"/>
	</th>
	<td><input type="text" name="switch[<?php echo $i; ?>][host]" value="<?php echo htmlspecialchars($switch["host"]); ?>"/></td>
	<td><input type="text" name="switch[<?php echo $i; ?>][call-limit]" value="<?php echo htmlspecialchars($switch["call-limit"]); ?>"/></td>
</tr>
<?php
}
?>
</table>

<table>
<caption>Gateway</caption>
<tr>
	<th class="action">
		<img name="add-gateway" src="image/add.png" alt="Add a Gateway"/>
		<img name="delete-all" src="image/delete.png" alt="Delete All Gateways"/>
	</th>
	<th>Host</th>
	<th>Port</th>
	<th>Prefix</th>
</tr>
<?php
foreach ($model["gateway"] as $i => $gateway) {
?>
<tr>
	<th class="action">
		<img name="delete" src="image/delete.png" alt="Delete a Gateway"/>
	</th>
	<td><input type="text" name="gateway[<?php echo $i; ?>][host]" value="<?php echo htmlspecialchars($gateway["host"]); ?>"/></td>
	<td><input type="text" name="gateway[<?php echo $i; ?>][port]" value="<?php echo htmlspecialchars($gateway["port"]); ?>"/></td>
	<td><input type="text" name="gateway[<?php echo $i; ?>][prefix]" value="<?php echo htmlspecialchars($gateway["prefix"]); ?>"/></td>
</tr>
<?php
}
?>
</table>

</form>

<script>
var switch_id = <?php echo count($model["switch"]); ?>;
var gateway_id = <?php echo count($model["gateway"]); ?>;;

$(document).ready(function() {
	var mac = $('input[name="mac"]');
	/* FIXME: if MAC is invalid, we shoud not disable it */
	if (mac.attr('value') != "")
		mac.attr('disabled', 'disabled');
	$('input[name="<?php echo($model["focus"] == "" ? "username" : $model["focus"]); ?>"]').focus().select();
	$('img[name="delete"]').each(function(i) {
		$(this).click(function() {
			$(this).parent().parent().remove();
		});
	});
	$('img[name="delete-all"]').each(function(i) {
		$(this).click(function() {
			$(this).parent().parent().siblings().remove();
		});
	});
	$('img[name="add-switch"]').click(function() {
		var entry = $(
'<tr>' +
'<th class="action"><img name="delete" src="image/delete.png" alt="Delete a Switch"/></th>' +
'<td><input type="text" name="switch[' + switch_id + '][host]"/></td>' +
'<td><input type="text" name="switch[' + switch_id + '][call-limit]"/></td>' +
'</tr>');
		entry.find('img[name="delete"]').each(function(i) {
			$(this).click(function() {
				$(this).parent().parent().remove();
			});
		});
		$(this).parent().parent().parent().append(entry);
		entry.find('input').first().focus();
		switch_id++;
	});
	$('img[name="add-gateway"]').click(function() {
		var entry = $(
'<tr>' +
'<th class="action"><img name="delete" src="image/delete.png" alt="Delete a Gateway"/></th>' +
'<td><input type="text" name="gateway[' + gateway_id + '][host]"/></td>' +
'<td><input type="text" name="gateway[' + gateway_id + '][port]"/></td>' +
'<td><input type="text" name="gateway[' + gateway_id + '][prefix]"/></td>' +
'</tr>');
		entry.find('img[name="delete"]').each(function(i) {
			$(this).click(function() {
				$(this).parent().parent().remove();
			});
		});
		$(this).parent().parent().parent().append(entry);
		entry.find('input').first().focus();
		gateway_id++;
	});
	$('#apply').click(function() {
		$('form').submit();
	});
<?php
	if ($model["message"] != "") {
?>
	alert("<?php echo $model["message"]; ?>");
<?php
	}
?>
});
</script>
