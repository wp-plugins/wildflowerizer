<?php
/*
 * Plugin Name: Wildflowerizer
 * Plugin URI: http://blog.tafoni.net/2010/05/01/4/
 * Description: Add a flower widget to your blog's sidebar. Flowers from the collaborative field guide: Wildflower Field Guide, North America.
 * Version: 1.1
 * Author: Dawn Endico
 * Author URI: http://www.tafoni.net/
 * License:  Released under GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
 */

if ( !class_exists('phpFlickr') ) {
  require_once("phpFlickr.php");
}

/**
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'wildflowerizer_load_widgets' );

/**
 * Register the widget.
 */
function wildflowerizer_load_widgets() {
  register_widget( 'Wildflowerizer_Widget' );
}

/**
 * Wildflowerizer Widget class.
 */
class Wildflowerizer_Widget extends WP_Widget {

  /**
   * Widget setup.
   */
  function Wildflowerizer_Widget() {
    /* Widget settings. */
    $widget_ops = array( 'classname' => 'wildflowerizer', 'description' => __('Add a happy wildflower to your sidebar') );

    /* Create the widget. */
    $this->WP_Widget( 'wildflowerizer-widget', __('Wildflowerizer', 'wildflowerizer'), $widget_ops, $control_ops );
  }

  /**
   * How to display the widget on the screen.
   */
  function widget( $args, $instance ) {
  
    extract( $args );
    /* Our variables from the widget settings. */
    $image_size = $instance['image_size']?$instance['image_size']:"Small";

    $f = new phpFlickr("58be12103b9f93f2b69a63001360df90");
    $f->enableCache('custom', array(array('Wildflowerizer_Widget', 'cache_get'), array('Wildflowerizer_Widget', 'cache_set')));

    $group = $f->urls_lookupGroup("http://www.flickr.com/groups/wildflowers/");
    $wfgna_group_id = $group[id];
    $wfgna_group_name = $group[groupname] ;

    $random = rand(1, 200);
    $results = $f->photos_search(array(
      'group_id' => $wfgna_group_id,
      'sort' => 'date-posted-desc',
      'per_page' => '1',
      'page' => $random,
      ));
  

    /* Before widget (defined by themes). */
    echo $before_widget;

    echo $before_title .  "Wildflowerizer" . $after_title;

    if ($results['photo']) {
       foreach ($results['photo'] as $photo) { 
         // Build image and link tags for each photo
         $photo_info = $f->photos_getInfo($photo[id]);
         $sizes = $f->photos_getSizes($featureID);
         echo "<a href=\"http://www.flickr.com/photos/$photo[owner]/$photo[id]\">\n";
         echo '<img alt="' . htmlentities($photo[title]) . '" ' .
             'title="' .  htmlentities($photo[title]) . '" ' .
             'src="' . $f->buildPhotoURL($photo, $image_size) .
             '" height="' . $sizes[2]['height'] .
             '" width="' . $sizes[2]['width'] . '"' . "/>";
         echo "</a>\n";
         echo "<p><a href=\"http://www.flickr.com/photos/$photo[owner]/$photo[id]\">\n";
         echo htmlspecialchars($photo[title], ENT_QUOTES, "UTF-8") ;
         echo '</a>, by ';
         echo htmlspecialchars($photo_info['owner']['username'], ENT_QUOTES, "UTF-8");
         echo ' in <a href="http://www.flickr.com/groups/wildflowers/">';
         echo htmlspecialchars($wfgna_group_name, ENT_QUOTES, "UTF-8"); 
         echo "</a></p>";
       }
     }

    /* After widget (defined by themes). */
    echo $after_widget;
  }

  /**
   * Update the widget settings.
   */
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['image_size'] = $new_instance['image_size'];
    return $instance;
  }

  /**
   * Displays the widget settings controls on the widget panel.
   * Make use of the get_field_id() and get_field_name() function
   * when creating your form elements. This handles the confusing stuff.
   */
  function form( $instance ) {

    /* Set up some default widget settings. */
    $defaults = array( 'image_size' => 'Small');
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

    <p>
      <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e('Image Size:', 'wildflowerizer'); ?></label> 
      <br>
      <input class="radio" type="radio" value="Small"
             name="<?php echo $this->get_field_name( 'image_size' ); ?>"
             <?php if($instance['image_size'] =="Small") echo " checked" ?>
      > 240 x 180
      <br>
      <input class="radio" type="radio" value="Thumbnail"
             name="<?php echo $this->get_field_name( 'image_size' ); ?>"
             <?php if($instance['image_size'] =="Thumbnail") echo " checked" ?>
      > 100 x 75
    </p>

  <?php
  }
  function cache_get($key) {
           global $wpdb;
           $result = $wpdb->get_row('
                   SELECT
                           *
                   FROM
                           `' . $wpdb->prefix . 'phpflickr_cache`
                   WHERE
                           request = "' . $wpdb->escape($key) . '" AND
                           expiration >= NOW()
           ');
           if ( is_null($result) ) return false;
           return $result->response;
   }

   function cache_set($key, $value, $expire) {
           global $wpdb;
           $query = '
                   INSERT INTO `' . $wpdb->prefix . 'phpflickr_cache`
                           (
                                   request,
                                   response,
                                   expiration
                           )
                   VALUES
                           (
                                   "' . $wpdb->escape($key) . '",
                                   "' . $wpdb->escape($value) . '",
                                   FROM_UNIXTIME(' . (time() + (int) $expire) . ')
                           )
                   ON DUPLICATE KEY UPDATE
                           response = VALUES(response),
                           expiration = VALUES(expiration)
           ';
           $wpdb->query($query);
   }
}

?>
