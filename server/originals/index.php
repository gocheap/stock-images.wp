<?php
header('Content-Type: text/plain');

set_time_limit(0);

/**
 * Generate thumbnails
 */
require_once('../class.cropnail.inc.php');

$images = glob('*.[jJ][pP][gG]');
foreach($images as $image)
{
	$thumbnail = "../stock-images/{$image}";
	
	if(is_file($thumbnail))
	{
		echo "\r\n", $thumbnail, ': ', filesize($thumbnail), ' Bytes';
		continue;
	}
	
	/**
	 * Create thumbnail
	 */
	$cropnail = new cropnail();
	$cropnail->resizeTo(200, 150);
	$cropnail->qualityTo(75);
	$cropnail->resize($image, $thumbnail);
}