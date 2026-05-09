<?php

echo elgg_view_form('embed/map', [
	'class' => 'elgg-form-embed-map',
		], $vars);
?>
<script type="module">
	import('embed/tab/map');
</script>
