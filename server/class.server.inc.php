<?php
class server
{
	private $counter = 0;
	
	public function init()
	{
		$this->counter = 0;
	}
	
	public function images($path='/tmp')
	{
		$images = glob($path.'/*.[jJ][pP][gG]');
		sort($images);
		#print_r($images);
		
		$images = array_map(array($this, 'shortcode'), $images);
		$images = array_filter($images);
		
		return $images;
	}

	public function shortcode($image)
	{
		$info = array();
		$size = getimagesize($image, $info);
		if(!$size) return null;

		$name = addslashes(basename($image));
		++$this->counter;
		$shortcode = '
<div>
<a href="%s" class="screenshot" rel="%s" title="Preview">Preview</a> - 
<a href="#" class="copy" data-clipboard-target="#shortcode%d">Copy</a> - 
<span id="shortcode%d">[stockimage width="%d" height="%d" name="%s"]</span> - 
%d Bytes
</div>
';
		$tag = sprintf(
			$shortcode,
			$image, $image,
			$this->counter,
			$this->counter, $size[0], $size[1], $name,
			filesize($image)
		);

		return $tag;
	}
}