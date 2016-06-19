<?php
$images = glob('stock-images/*.[jJ][pP][gG]');
#print_r($images);

$images = array_map('shortcode', $images);
$images = array_filter($images);
#print_r($images);

echo implode('<br />', $images);

function shortcode($image)
{
	$info = array();
	$size = getimagesize($image, $info);
	if(!$size) return null;

	$name = addslashes(basename($image));
	
	$shortcode = '[stockimage width="%d" height="%d" name="%s"]';
	$tag = sprintf($shortcode, $size[0], $size[1], $name);

	return $tag;
}