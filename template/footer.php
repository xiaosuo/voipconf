<script>
$(document).ready(function() {
	// work around for IE6 and IE7
	$("th,td").each(function(i) {
		if ($(this).children().length == 0)
			$(this).html('<span class="nowrap">' + $(this).html() + '</span>');
	});

	$("#restart").click(function() {
		if (!confirm("Are you sure to restart?"))
			return false;
	});
});
</script>
</body>
</html>
