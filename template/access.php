<div>
<form name="login" method="post" action="access.php">
<table>
	<caption>Login</caption>
	<tr>
		<th>Username</th>
		<td><input type="text" name="username"/></td>
	</tr>
	<tr>
		<th>Password</th>
		<td><input type="password" name="password"/></td>
	</tr>
	<tr>
		<th colspan="2"><input type="submit" name="submit" value="Login"/></th>
	</tr>
</table>
</form>
</div>
<script>
$(document).ready(function() {
	var content = $('div');
	content.css('position', 'fixed');
	var left = ($(window).width() - parseInt(content.css('width'))) / 2;
	content.css('left', left + "px");
	var top = ($(window).height() - parseInt(content.css('height'))) / 2;
	content.css('top', top + "px");
	$('input[name="username"]').focus().select();
	<?php
		if ($model != "")
			echo "alert(\"$model\");\n";
	?>
});
</script>
