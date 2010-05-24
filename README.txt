=== Plugin Name ===
Contributors: endico
Donate link: http://blog.tafoni.net/donate/
Tags: widget, sidebar, image, images, photo, photos, nature, botany, flower, wildflower
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.1

Add a flower widget to your blog's sidebar. Flowers from the collaborative field guide: Wildflower Field Guide, North America.

== Description ==

Wildflowerizer is a sidebar widget that randomly displays a photo that's been recently added to the [Wildflower Field Guide, North America](http://www.flickr.com/groups/wildflowers/) group at Flickr.


== Installation ==

1. Install the plugin through the 'Plugins' menu in WordPress. If 
installing from a zip file, place the wildflowerizer folder in your
wp-content/plugins/ folder.
1. Activate the plugin on your Manage Plugins page.
1. In the Appearance section, choose Widgets and drag Bug Of The 
Day to your sidebar. The default image size is 240x180 pixels. If
your sidebar isn't that wide, then choose 100x75.

So far the plugin has only been tested with WordPress 2.9.2.

If your theme doesn't support widgets, you can still use the widget by adding this code to your sidebar, as explained in [this post](http://wordpress.org/support/topic/281349?replies=7).

    <?php { ; ?>
       <?php $instance = array("title" => "My Widget", "number" => 9); ?>
       <?php $args = array("title" => "My Widget", "before_title" => "<h2>", "after_title" => "</h2>"); ?>
       <?php $sb = new Wildflowerizer_Widget(); ?>
       <?php $sb->number = $instance['number']; ?>
       <?php $sb->widget($args,$instance); ?>
    <?php } ?>

== Changelog ==

= 1.0 =
* Initial release

= 1.1 =
* Updated phpFlickr and fixed a data visibility problem with the cache. The plugin will now work on sites that aren't installed at the domain's root. Upgrade recommended for all users.

== Credits ==

This plugin uses [phpFlickr](http://phpflickr.com/) 
which is a class written by Dan Coulter 
in PHP to act as a wrapper for the Flickr API. 

