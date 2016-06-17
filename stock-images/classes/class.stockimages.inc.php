<?php
class stockimages
{
	private $whoami = null;
	public $stockimage_api = 'http://www.example.com/stockimage/%s';

	public function init($whoami) {
		$this->whoami=$whoami;
		
		add_shortcode('stockimage', array($this, '_shortcode_stockimage'));
		
		add_action('admin_init', array($this, '_settings_api_init'));
		add_action('plugin_row_meta', array($this, '_plugin_row_meta'), 10, 2);
		
		register_activation_hook($whoami, array($this, 'activate'));
		register_deactivation_hook($whoami, array($this, 'deactivate'));
		register_uninstall_hook($whoami, array(__CLASS__, 'uninstall'));
		
		$stockimage_api_customized = get_option('stockimage_api');
		if($stockimage_api_customized)
		{
			$this->stockimage_api = $stockimage_api_customized;
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

		$src = sprintf($this->stockimage_api, $name);
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
			'stockimage_api',
			'Stock Image URL',
			array($this, '_settings_section_field_callback_function'),
			$section,
			'stockimage_settings_section'
		);

		register_setting($section, 'stockimage_api');
	}

	/**
	 * Ask a question
	 */
	public function _settings_section_callback_function()
	{
		$example = get_site_url().'/stockimage/%s';
		echo "<p>Where is your stock image?</p><p>eg: {$example} | end with %s</p>";
	}
	
	public function _settings_section_field_callback_function()
	{
		$stockimage_api_customized = get_option('stockimage_api');
		if(!$stockimage_api_customized)
		{
			$example = get_site_url().'/stockimage/%s';
			$stockimage_api_customized = $example;
		}
		echo '<input name="stockimage_api" id="stockimage_api" type="text" value="'.addslashes($stockimage_api_customized).'" class="regular-text code" /> Be careful!';
	}
	
	public function _plugin_row_meta($links, $file) {

		if($file == $this->whoami)
		{
			$new_links = array(
				'stockimage_api' => '<strong>Stock Image URL</strong>: '.get_option('stockimage_api'),
			);
			
			$links = array_merge( $links, $new_links );
		}
		
		return $links;
	}
	
	public function activate()
	{
		add_option('stockimage_api', get_site_url().'/stockimage/%s');
	}
	
	public function deactivate()
	{
		$option_name = 'stockimage_api';
		delete_option( $option_name );
		delete_site_option( $option_name );
	}
	
	public static function uninstall()
	{
		$option_name = 'stockimage_api';
		delete_option( $option_name );
		delete_site_option( $option_name );
	}
}
