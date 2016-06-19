=== Stock Images ===

Contributors: pbimal
Donate link: http://bimal.org.np/
Tags: stock, images, photograhy, collection
Requires at least: 4.5.0
Tested up to: 4.5.2
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html


Hosted stock images and photography linker.


== Description ==

Import photos and images from your personally hosted stock images.

Adds a shortcode to access your images.
`[stockimage width="450" height="300" name="my-photo.jpg"]`

After installation, edit the URL of your stock image repository at:
Settings > General > Stock Image URL.

All your photos can be pointed from your configured url.

For example, if you pointed to http://www.example.com/stock-images/%s as your URL,
your photos will be accessed as http://www.example.com/stock-images/my-photo.jpg in the above example.
You can drop photos inside stock-images/ path via FTP or similar.

It can be a very creative replacement of your media library, and works better if you are describing a product or something else. You will have a total control of images to be served.


= Setting up a basic stock image hosting =

Create a folder named stock-images inside your FTP account which is accessible from your website. Drop in all your optimized images there. Stop directory listing with an empty index.php file. Optionally configure .htaccess for direct/hot-linking into your images from unauthorised websites or direct image downloaders.

Configure your URL to point into this folder.

Then the pattern `[stockimage width="450" height="300" name="my-photo.jpg"]` will point to your specific image as `<img src='http://www.example.com/stock-images/my-photo.jpg' width='450' height='300'>`.


== Installation ==


= Method #1 =

 * Go to your WP Admin > Plugins > Add New page.
 * Search for "Stock Images".
 * Click install. Click activate.


= Method #2 =

 * Download this plugin as a .zip file.
 * Go to WP Admin > Plugins > Add new > Upload Plugin.
 * Upload the .zip file and activate the plugin.


= Method #3 =

 * Download the file stock-images.zip.
 * Unzip the file on your computer.
 * Upload folder stock-images (you just unziped) to /wp-content/plugins/ directory.
 * Activate the plugin through the WP Admin > Plugins menu.

Click on [how to install a WordPress Plugin](http://goo.gl/Ims8pt) for more details.


== Frequently Asked Questions ==

= I have images of multiple dimensions. How can I classify them? =

You can put them in multiple sub-directories.
Yes. Just update your file name after your base url.
eg. `[stockimage name='sub-directory/image-name.jpg']`

You can use this trick to classify banners, logos, icons or full sized images.

= How can I protect my images? =

This plugin is to consume your hosted images. Providing safety to your hosted images is beyond the scope of the plugin. However, you can use .htaccess and index.php files to prevent directly listing and hot-linking to your images.


= How can I setup my hosted server? =

Actual hosting of your photography is not the scope of this plugin.
But you should upload your photos anywhere accessible via web.
You can use Dropbox or FTP like tools to upload your file on your server.

Consult with your hosting provider regarding:

 - How to upload your files
 - Bandwidth you are using


= Can I upload raw images from my camera? =

Not recommended due to file size and bandwidth.
Rather make suitable thumbnails or at least optimize your images.
Your website should always have least possible content output.

== Screenshots ==

1. Setup


== Changelog ==

= 1.0.0 =
* Initial release.


== Upgrade Notice ==

When this plugin is used and disabled, your blogs will show your `[stockimage]` shortcode as it is with the name of the photo file. This will NOT release any further details.
