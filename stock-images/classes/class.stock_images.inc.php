<?php
class stock_images
{
	private $whoami = null;

	/**
	 * Where did you host your stock images?
	 * @see https://github.com/bimalpoudel/stock-images/tree/master/server
	 */
	public $stock_images_api = 'http://www.example.com/stock-images/%s';

	public function init($whoami) {
		$this->whoami=$whoami;
		
		add_shortcode('stockimage', array($this, '_shortcode_stockimage'));
		
		add_action('admin_init', array($this, '_settings_api_init'));
		add_action('plugin_row_meta', array($this, '_plugin_row_meta'), 10, 2);
		
		register_activation_hook($whoami, array($this, 'activate'));
		register_deactivation_hook($whoami, array($this, 'deactivate'));
		register_uninstall_hook($whoami, array(__CLASS__, 'uninstall'));
		
		$stock_images_api = get_option('stock_images_api');
		if($stock_images_api)
		{
			$this->stock_images_api = $stock_images_api;
		}
	}

	/**
	 * [stockimage width="450" height="300" name="default.jpg"]
	 */
	function _shortcode_stockimage($atts, $content = null ) {
		extract(shortcode_atts(array(
			'height' => 100,
			'width' => 200,
			'name' => 'default.jpg',
		), $atts));

		$src = sprintf($this->stock_images_api, $name);
		return "<img src='{$src}' width='{$width}' height='{$height}'>";
	}
	
	public function _settings_api_init()
	{
		$section = 'general'; # general, reading, writing

		add_settings_section(
			'stockimage_settings_section',
			'Stock Image URL',
			array($this, '_settings_section_callback_function'),
			$section
		);

		add_settings_field(
			'stock_images_api',
			'Stock Image URL',
			array($this, '_settings_section_field_callback_function'),
			$section,
			'stockimage_settings_section'
		);

		register_setting($section, 'stock_images_api');
	}

	/**
	 * Ask a question
	 */
	public function _settings_section_callback_function()
	{
		$example = $this->_sample_stock_image_api_url();
		echo "<p>Where are your stock images (URL)?</p>
		<p>eg: {$example} | end with %s</p>";
	}
	
	public function _settings_section_field_callback_function()
	{
		$stock_images_api = get_option('stock_images_api');
		if(!$stock_images_api)
		{
			$stock_images_api = $this->_sample_stock_image_api_url();
		}
		echo '<input name="stock_images_api" id="stock_images_api" type="text" value="'.addslashes($stock_images_api).'" class="regular-text code" /> Be careful!';
	}
	
	public function _plugin_row_meta($links, $file) {

		if($file == $this->whoami)
		{
			$new_links = array(
				'stock_images_api' => '<strong>Stock Image URL</strong>: '.get_option('stock_images_api'),
			);
			
			$links = array_merge($links, $new_links);
		}
		
		return $links;
	}
	
	public function activate()
	{
		add_option('stock_images_api', $this->_sample_stock_image_api_url());
	}
	
	public function deactivate()
	{
		$option_name = 'stock_images_api';
		delete_option( $option_name );
		delete_site_option( $option_name );
	}
	
	public static function uninstall()
	{
		$option_name = 'stock_images_api';
		delete_option( $option_name );
		delete_site_option( $option_name );
	}
	
	private function _sample_stock_image_api_url()
	{
		$sample_stock_image_api_url = get_site_url().'/stock-images/%s?rand=%s';
		return $sample_stock_image_api_url;
	}
}
