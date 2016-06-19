<style>
#screenshot{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;
	color:#fff;
}
</style>

<?php
require_once('class.server.inc.php');

$server = new server();
$server->init();
$images = $server->images('stock-images');
#print_r($images);

echo implode('', $images);

?>
<div>Camera &rarr; FTP: Originals + Rename &rarr; Thumbnails &rarr; Stock Images | <a href="originals/">Generate</a></div>
<div>Uses: <a href="http://cssglobe.com/easiest-tooltip-and-image-preview-using-jquery/">URL preview script</a>, <a href="https://clipboardjs.com/">Clipboard.js</a></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="hover-preview.js"></script>

<script src="https://cdn.rawgit.com/zenorocha/clipboard.js/v1.5.10/dist/clipboard.min.js"></script>
<script>
new Clipboard('a.copy');
</script>