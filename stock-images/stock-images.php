<?php
/**
 * Plugin Name: Stock Images | Personalized
 * Version: 1.0
 * Plugin URI: http://bimal.org.np/
 * Description: Self host your Stock Images. Protect hotlinking and directory listing yourself on your stock image url. Adds a shortcode to access your images. <code>[stockimage width="450" height="300" name="default.jpg"]</code>. After installation, edit: <a href="options-general.php">Settings > General > Stock Image URL</a>.
 * Author: Bimal Poudel
 * Author URI: http://bimal.org.np/
 * License: GPL3
 */

require_once('classes/class.stock_images.inc.php');

$stock_images = new stock_images();
$stock_images->init(basename(dirname(__FILE__)).'/'.basename(__FILE__));

/**
 * Stock Image URL is setup in WP-Admin > Settings > General
 */
