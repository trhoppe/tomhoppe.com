=== WP-SmugMug ===
Tags: SmugMug, WordPress, Galleries, Albums, Photos
Contributors: atow
Requires at least: 3.1
Tested up to: 3.1.1
Stable tag: 2.0.9

WP-SmugMug integrates your SmugMug galleries into your Posts and Pages on your WordPress blog.

== Description ==

WP-SmugMug integrates your SmugMug galleries into your Posts and Pages on your WordPress blog. SmugMug is a popular online photo sharing website at [http://www.smugmug.com/](http://www.smugmug.com/).

Visit the [WP-SmugMug homepage](http://www.tow.com/projects/wordpress/wp-smugmug/) for more information on how to incorporate the plugin into your blog.

== Installation ==

1. Download the plugin archive and expand it

2. Place the 'wp-smugmug' folder into your wp-content/plugins/ directory. Your folder structure should look like this:

	wp-content/plugins/wp-smugmug/wp-smugmug.php
	wp-content/plugins/wp-smugmug/wp-smugmug.js

3. Go to the Plugins page in your WordPress Administration area and click 'Activate' for WP-SmugMug.

NOTE: If you have installed a previous version of WP-SmugMug in the wp-content/plugins/ directory, please delete it before installing this version of WP-SmugMug.

**Usage**

WP-SmugMug installs a new option in the Post and Page edit subpanel. Fill out the form with information about your SmugMug gallery:

1. RSS URL - the URL to your SmugMug gallery RSS feed. This can be found at the bottom of your gallery page under the Available Feeds link

2. Title (optional) - a title to be displayed above the gallery thumbnails.

3. Description (optional) - a description of the gallery to be displayed below the title and above the gallery thumbnails.

4. Number of photos to retrieve: the number of photos to retrieve from the SmugMug gallery.

5. Start at photo #: the index from which your gallery will begin displaying thumbnails. 

6. Number of photos to display: the number of photos to display from the gallery.

7. Thumbnail size: choose between the tiny 100x100 or regular 150x150 sized thumbnails.

8. Photo size: the size of the photo to display when the viewer clicks on a thumbnail.

9. Link Destination: where the browser will go when the viewer clicks on a thumbnail. Choose from your SmugMug gallery page, the SmugMug Lightbox Page, or to the image file itself. If you have one of the WordPress Lightbox plugins installed, you can also view the image Lightbox-style.

10. Options: Configure additional options for your SmugMug gallery

Click on the Send to Editor button to send the WP-SmugMug shortcode to your post.

**Changing Defaults**

In the WordPress Administration area, click Settings->WP-SmugMug. On this page, you can adjust the default settings shown in the WP-SmugMug options panel when editing a Post or Page. You can also add your own custom CSS by entering CSS styles on this page.

**WP-SmugMug 1.x Compatibility**

WP-SmugMug 2 is compatible with previous versions of WP-SmugMug. For all of the latest features, however, you should use the WP-SmugMug shortcode in the future.

**Version History**

2.0.1 - Specifying tiny thumbnail size now works properly.
2.0.2 - Specifying num attribute to limit the number of displayed images now works properly.
2.0.3 - Fixed incompatibility with html entity decoding on PHP4. Fixed html bugs and added #wp-smugmug html fragment for paged galleries. Disables previous versions of WP-SmugMug.
2.0.4 - Added Next/Prev buttons for WordPress gallery style. Fixed bug with WordPress flushing rules.
2.0.5 - Fix for jQuery interface change
2.0.6 - Fix for new SmugMug image permalink structure
2.0.7 - Fix for new SmugMug RSS feed structure. Removed WordPress paged gallery support. Support for image galleries larger than 100 photos.
2.0.8 - Fix for crashing bug with channel link

== Frequently Asked Questions ==

= I updated a caption on my SmugMug gallery, but WP-SmugMug is still showing the old caption. = 

WP-SmugMug uses WordPress to retrieve feeds. You can adjust how long feeds are cached by setting the MAGPIE_CACHE_AGE value in your wp-config.php file.

== Screenshots ==

1. This is the WP-SmugMug options panel which appears in the Post or Page edit screen.
