<?php
class cropnail
{
	/**
	 * Resize the output image to these dimensions
	 */
	private $resize_width = 0;
	private $resize_height = 0;
	
	private $transparent = false;

	/**
	 * @var integer Image output quality in Percentage
	 */
	private $quality = 75;

	/**
	 * Possible positions for resizes.
	 * Can once only one from horizontally or vertically only.
	 * Both of them do not apply.
	 * Match the values with that of the database as well.
	 * Do not set to zero (default, and unused).
	 */
	const CROPNAIL_VERTICAL_LEFT = 1;
	const CROPNAIL_VERTICAL_CENTER = 2;
	const CROPNAIL_VERTICAL_RIGHT = 3;
	const CROPNAIL_HORIZONTAL_TOP = 4;
	const CROPNAIL_HORIZONTAL_CENTER = 5;
	const CROPNAIL_HORIZONTAL_BOTTOM = 6;
	const CROPNAIL_BOTTOM_RIGHT = 7;
	const CROPNAIL_BOTTOM_LEFT = 8;
	const CROPNAIL_TOP_RIGHT = 9;
	const CROPNAIL_TOP_LEFT = 10;

	/**
	 * A dynamically/programatically chosen cropnail position from one of the above constants.
	 * Or, optionall use the hard coded x/y postions without mathematical calulations.
	 */
	private $position_id = 0;
	private $cropnail_x = 0;
	private $cropnail_y = 0;

	/**
	 * Record cropnail points for post-operation usage
	 */
	private $source_x1 = 0;
	private $source_x2 = 0;
	private $source_y1 = 0;
	private $source_y2 = 0;
	
	private $mime = 'image/png'; # gif, jpeg, png

	/**
	 * List of processing activities
	 */
	private $debug = array();

	/**
	 * Continue to other images during mass processing
	 * even if an error was found at some point
	 */
	private $allow_continue;

	/**
	 * Set the target dimension at the time of initialization
	 */
	public function __construct($width = 0, $height = 0, $quality=75)
	{
		$this->resizeTo($width, $height);
		$this->qualityTo($quality);

		$this->debug = array();
	}
	
	public function resizeTo($width = 0, $height = 0)
	{
		$this->resize_width = abs((int)$width);
		$this->resize_height = abs((int)$height);

		/**
		 * Are the target dimensions ok?
		 */
		$this->allow_continue = $this->resize_width >= 1 && $this->resize_height >= 1;
	}
	
	public function qualityTo($quality=0)
	{
		$this->quality = abs((int)$quality);
	}

	/**
	 * Produce the cropnailed image file
	 *
	 * @return boolean
	 *    - true:  Image has been resized correctly
	 *    - false: Image has NOT been resized because of several reasons
	 */
	public function resize($original_filename = '', $cropnail_filename = '', $crop_pattern = 0)
	{
		/**
		 * Resizer needs the target dimensions
		 */
		if(!($this->resize_width && $this->resize_height))
		{
			return false;
		}

		/**
		 * Original image file should exist
		 * Target must be defined
		 */
		if(!is_file($original_filename) || !$cropnail_filename)
		{
			return false;
		}

		/**
		 * Find out the source image size
		 */
		$canvas_sizes = getimagesize($original_filename);
		if(!$canvas_sizes)
		{
			$this->debug[] = "Not an image: {$original_filename}";

			return false;
		}
		$canvas_width = $canvas_sizes[0];
		$canvas_height = $canvas_sizes[1];

		/**
		 * Maximum size of the clip propertional to the thumbnail
		 */
		$clip_width = 0;
		$clip_height = 0;

		/**
		 * From where should we crop the clip
		 */
		$this->source_x1 = 0;
		$this->source_y1 = 0;
		$this->source_x2 = 0;
		$this->source_y2 = 0;

		if($canvas_width / $this->resize_width < $canvas_height / $this->resize_height)
		{
			# Coverting full width, and height needs to be re-calculated
			$clip_width = $canvas_width;
			$clip_height = $this->resize_height * $canvas_width / $this->resize_width;
			$this->debug[] = "Section A: Coverting full width, and height needs to be re-calculated";

			# CROPNAIL_HORIZONTAL_CENTER
			$this->source_x1 = 0;
			$this->source_y1 = ($canvas_height - $clip_height) / 2;

			# TOP: Height Recalculation
			#$this->source_y1 = 70; # ($canvas_height-$clip_height)/2;

			# case: 800x600 output from a vertically too tall image
			# Optionally hard coded
			if($this->cropnail_y)
			{
				$this->source_y1 = $this->cropnail_y;
			}
		}
		else
		{
			# Coverting full height, and width needs to be re-calculated
			$clip_width = $this->resize_width * $canvas_height / $this->resize_height;
			$clip_height = $canvas_height;
			$this->debug[] = "Section B: Coverting full height, and width needs to be re-calculated";

			# CROPNAIL_VERTICAL_CENTER
			$this->source_x1 = ($canvas_width - $clip_width) / 2;
			$this->source_y1 = 0;

			# Width Recalculation
			# Optionally hard coded
			if($this->cropnail_x)
			{
				$this->source_x1 = $this->cropnail_x;
			}
		}
		$this->source_x2 = $this->source_x1 + $clip_width;
		$this->source_y2 = $this->source_y1 + $clip_height;

		/**
		 * Validations
		 * Convert to the nearest/highest integer value of the width and height
		 */
		$clip_width = ceil($clip_width);
		$clip_height = ceil($clip_height);
		$this->source_x1 = ceil($this->source_x1);
		$this->source_y1 = ceil($this->source_y1);
		$this->source_x2 = ceil($this->source_x2);
		$this->source_y2 = ceil($this->source_y2);

		/**
		 * Canvas for target image
		 * Produce the croped thumbnail (actual cropnail)
		 */
		$destination = imagecreatetruecolor($this->resize_width, $this->resize_height);
		
		/**
		 * @todo Maintain transparency in destination
		 */
		# imagecolorallocate
		# $transparent_color = imagecolorallocatealpha($destination, 0, 0, 0, 127);
		# imagefill($destination, 0, 0, $transparent_color);

		/**
		 * Support resizing of multiple image types
		 */
		$source = null;
		$imagesize = getimagesize($original_filename);
		$this->mime = $imagesize['mime'];
		switch(strtolower($imagesize['mime']))
		{
			case 'image/gif':
				$source = imagecreatefromgif($original_filename);
				break;
			case 'image/jpeg':
				$source = imagecreatefromjpeg($original_filename);
				break;
			case 'image/png':
				$source = imagecreatefrompng($original_filename);
				$this->transparent = $this->is_transparent_png($original_filename);
				break;
			default:
				/**
				 * Not an image file - there is no worth to attempt resize
				 * We do not actually come here if we limit to png, gif and jpg images
				 * At the moment we do not support other types of images
				 */
				$this->debug[] = "Not an image file: {$original_filename}";

				return false;
		}

		/**
		 * Actually produce the cropnail image and save it to the file (in .jpg format)
		 */
		imagecopyresampled($destination, $source, 0, 0, $this->source_x1, $this->source_y1, $this->resize_width, $this->resize_height, $clip_width, $clip_height);
		$success = false;
		switch($this->mime)
		{
		case 'image/gif':
			$success = imagegif($destination, $cropnail_filename);
			break;
		case 'image/jpeg':
			$success = imagejpeg($destination, $cropnail_filename, $this->quality);
			break;
		case 'image/png':
			# PNG_NO_FILTER or PNG_ALL_FILTERS
			# @todo Quality = 0 - 9, Convert the % value
			$quality = floor($this->quality/10);
			$filters = null; # @todo
			$success = imagepng($destination, $cropnail_filename, $quality, $filters);
			break;
		default:
			$this->debug[] = "Invalid output type";
			break;
		}
		
		# Some debug information
		$this->debug[] = "Canvas: {$canvas_width} x {$canvas_height}, {$original_filename}";
		$this->debug[] = "Clip: {$clip_width} x {$clip_height}";
		$this->debug[] = "Resize To: {$this->resize_width} x {$this->resize_height}";
		$this->debug[] = "Source (x1,y1), (x2,y2): ({$this->source_x1},{$this->source_y1}), ({$this->source_x2},{$this->source_y2})";

		return $success;
	}
	
	/**
	 * Detect if a PNG Image is transparent
	 * @see http://stackoverflow.com/questions/2057923/how-to-check-a-png-for-grayscale-alpha-color-type
	 * @see http://www.codingforums.com/php/257111-checking-png-files-transparency.html
	 * @see http://perplexed.co.uk/1814_png_optimization_with_gd_library.htm
	 */
	private function is_transparent_png($image_file='/tmp/image.png')
	{
		return ord(file_get_contents($image_file, null, null, 25, 1)) == 6;
	}

	/**
	 * Sets a position from where we should crop the image for the best fit.
	 * It is only for customized position cropping purpose.
	 * Optional to call.
	 * Defaults to center in horizontal and vertical positions.
	 */
	public function set_postion($position_id = 0)
	{
		$position_id = (int)$position_id;
		$this->position_id = $position_id;
	}

	/**
	 * Hack against the position IDs.
	 * Rather use these hard coded values.
	 */
	public function set_xy($x = 0, $y = 0)
	{
		$this->cropnail_x = (int)$x;
		$this->cropnail_y = (int)$y;
	}

	/**
	 * Print the debug message
	 */
	public function debug()
	{
		echo implode("\r\n", $this->debug);
	}

	/**
	 * Retrieve x1, y1, x2, y2
	 * @todo If unused, remove.
	 *
	 * @return string (x1,y1), (x2,y2)
	 */
	public function x1y1x2y2()
	{
		return "({$this->source_x1},{$this->source_y1}), ({$this->source_x2},{$this->source_y2})";
	}
}
