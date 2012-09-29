<script>
// work around for IE6 and IE7
$(document).ready(function() {
	$("th,td").each(function(i) {
		if ($(this).children().length == 0)
			$(this).html('<span class="nowrap">' + $(this).html() + '</span>');
	});
});
</script>
</body>
</html>
