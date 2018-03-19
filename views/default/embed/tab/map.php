<?php

echo elgg_view_form('embed/map', [
	'class' => 'elgg-form-embed-map',
		], $vars);
?>
<script>
	require(['embed/tab/map']);
</script>
