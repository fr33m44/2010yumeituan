<?php
###############################################################
# Website Thumbnail Image Generator 1.1
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
###############################################################
#
# REQUIREMENTS:
# PHP 4.0.6 and GD 2.0.1 or later
# May not work with GIFs if GD2 library installed on your server
# does not support GIF functions in full
#
# Parameters that can be passed via url (if not passed will be used values which are set below):
# url - url of the target website
# x - max width
# y - max height
# q - quality (applicable only to JPG, 1 to 100, 100 - best)
#
# Sample usage:
# 1. webthumb.php?url=http://www.microsoft.com
# 2. Set maximum thumbnail size to 150
#    webthumb.php?url=http://www.thumbnails.com&x=150&y=150
###############################################################

// Folder to save all thumbnails. 
// Must end with slash!!!
$thumbnails_folder = 'c:/';

// thumbnails expiration time in minutes
$cache_expire_time = 60;

// quality (for jpeg only)
$image_quality = 100;

// resulting image type (1 = GIF, 2 = JPG, 3 = PNG)
$image_type = 3;

// maximum thumb side size
$max_x = 100;
$max_y = 100;

// If not equal 0 then cut original image size before resizing (in pixels).
// Long page will have bad thumbnail, its better to cut page length first.
$cut_x = 0;
$cut_y = 1024;

###############################################################################
# END OF SETTINGS. DO NOT EDIT BELOW
###############################################################################

if (isset($_REQUEST['url'])) {
  $website_url = $_REQUEST['url'];
}
else {
  die("Site URL must be specified.");
}

if ($image_type == 1) $output_format = 'gif';
if ($image_type == 2) $output_format = 'jpg';
if ($image_type == 3) $output_format = 'png';

$website_url_md5 = md5($website_url);

$cached_filename = $thumbnails_folder . $website_url_md5 . '.' . $output_format;

// See if we have cached website screenshot image (to minimize server load)
if (!file_exists($cached_filename) 
|| filemtime ($cached_filename) + $cache_expire_time * 60 < time() ) {

  // Get website image and save it on the server.
  @exec('IECapt.exe ' . escapeshellarg($website_url) . ' ' . escapeshellarg($cached_filename));

} // if (!file_exists

if (!file_exists($cached_filename)) {
  die("Thumbnail Generation Error. Thumbnail not created.");
}

// create class instance
include("image.class.php");
$img = new Zubrag_image;

// get parameters
$img->image_type   = $image_type;
$img->quality      = isset($_REQUEST['q']) ? intval($_REQUEST['q']) : $image_quality;
$img->max_x        = isset($_REQUEST['x']) ? intval($_REQUEST['x']) : $max_x;
$img->max_y        = isset($_REQUEST['y']) ? intval($_REQUEST['y']) : $max_y;
$img->save_to_file = false;
$img->cut_x        = $cut_x;
$img->cut_y        = $cut_y;

// generate thumbnail and show it
$img->GenerateThumbFile($cached_filename, '');

?>