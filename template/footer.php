<script>
$(document).ready(function() {
	$('button').each(function(i) {
		$(this).click(function() {
			var href = window.location.href;
			href = href.substr(0, href.lastIndexOf("/") + 1) + $(this).attr("href");
			window.location.assign(href);
		});
	});
});
</script>
</body>
</html>
