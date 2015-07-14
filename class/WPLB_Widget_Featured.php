<?php
/**
 * WPLB List Featured
 *
 * @author Huu Ha <huuhath@gmail.com>
 * @link http://www.wplabels.com
 */

class WPLB_Widget_Featured extends WPLB_Widgets
{
  public $fields;
  public $defaults;
  public function __construct()
  {
    $widgets_init = array(
        'id_base' => 'wplb-widget-featured',
        'name'    => $this->plugin_name.__('Featured Custom Post','wplb'),
        'options' => array(
            'classname'   => 'wplb-widget-featured',
            'description' => __( 'Display ultimate display custom post featured', 'wplb' )
          )
      );

      // Defaults
      $defaults = array(
          'title' => array(
              'label' =>  __('Title', 'wplb'),
              'std'   =>  __('List Custom Post', 'wplb'),
              'type'  => 'text',
          ),

          'post_and_term' => array(
              'type' =>'post_to_term' // only need id and type 
          ),
          
          'posts_num' => array(
              'label' => __('Number to show', 'wplb'),
              'type'  => 'text',
              'std'   => '3',
              'class'   => 'float-left',
          ),

          'posts_offset' => array(
              'label' => __('Offset', 'wplb'),
              'type'  => 'text',
              'std'   => '0',
              'class'   => 'float-left',
          ),

          'orderby' => array(
              'label'   => __('Order by', 'wplb'),
              'type'    => 'select',
              'options' => array(
                  'date'          => __('Date','wplb'),
                  'ID'            => __('ID','wplb'),
                  'title'         => __('Title','wplb'),
                  'parent'        => __('Parent','wplb'),
                  'rand'          => __('Random','wplb'),
                  'comment_count' => __('Comment Count','wplb')
              ),
              'std' => 'date',
          ),

          'order' => array(
              'label'   => __('Order by', 'wplb'),
              'type'    => 'select',
              'options' => array(
                  'DESC' => __('Descending (3, 2, 1)','wplb'),
                  'ASC'  => __('Ascending (1, 2, 3)','wplb'),
              ),
              'std' => 'DESC',
          
          ),
          'exclude' => array(
              'label' =>  __('Exlude ', 'wplb'),
              'type'  => 'text',
              'class' =>'float-left',
              'desc'  => __('These post (separated by commas)','wplb')
          ),

          'show_title' => array(
              'label'     =>  __('Show Title?', 'wplb'),
              'type'      => 'checkbox'
          
          ),

          'show_image' => array(
              'label' =>  __('Show Image?', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>true
          ),
          'show_meta_date' => array(
              'label' =>  __('Show meta date ?', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>false
          ),
          'show_meta_cat' => array(
              'label' =>  __('Show meta category ?', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>false
          ),

          'image_size' => array(
              'label'   => __('Thumbnail Size', 'wplb'),
              'type'    => 'select',
              'options' => wplb_get_image_sizes(),
              'std'     => 'thumbnail',
          ),
          
          'show_content' => array(
              'label'   => __('Content', 'wplb'),
              'type'    => 'select',
              'options' => array(
                  ''              => __('None','wplb'),
                  'content'       => __('Full Content','wplb'),
                  'excerpt'       => __('Expert','wplb'),
                  'content-limit' => __('Content limit','wplb')
              ),
          ),

          'content_limit' => array(
              'label' => __('Content Limit','wplb'),
              'type' => 'text'
          ),

          'more_text' => array(
              'label' => __('More text','wplb'),
              'type'  => 'text',
              'std'   => __('More','wplb')
          ),
          'show_gravatar' => array(
                'label'         =>  __('Show Gravata?', 'wplb'),
                'type'          => 'checkbox',
          ),
          'gravatar_size' => array(
            'label'         => __('Gravata Size', 'wplb'),
            'type'          => 'select',
            'options' => array(
                    '26'  => 'Small (26px)',
                    '65'  => 'Medium (65px)',
                    '85'  => 'Lage (85px)',
                    '125' => 'Extra lage (125px)'
                ),
                'std'     => '26',
            ),
          'class' => array(
              'label' => __('Class','wplb'),
              'type'  => 'text'
          ),
      );

      $featured = new Wplb_Featured();

      $widgets_init = apply_filters('wplb_widgets_video_init', $widgets_init);
      $defaults = apply_filters('wplb_widgets_video_default', $defaults);

      parent::__construct( $widgets_init, $defaults);

  }

 
  /**
   * Format the output for this widget.
   *
   * @return string
   * @author Huu Ha
   */
public function output($args, $instance) {
   extract( $args );
   $html = $before_widget;
 
   $class = 'wplb-widget-post-featured ';
   if (!empty($instance['class'])) {
     $class .= $instance['class'] ;
   }
   $html .= '<div class="'.$class.'">';

   if (!empty($instance['title'])) {
      $html .= $before_title;
         $html .= $instance['title'];
      $html .= $after_title;
    }

  

   $post_type = 'post';
   $taxonomy = '';
   $term_id = '';

   if(!empty($instance['post_and_term'])){
      $json_value = json_decode($instance['post_and_term']);
      $json_value = (array)$json_value[0];

      if(isset($json_value['post_type']) && !empty($json_value['post_type'])) 
         $post_type = $json_value['post_type'];

      if(isset($json_value['taxonomy']) && !empty($json_value['taxonomy'])) 
         $taxonomy = $json_value['taxonomy'];

      if(isset($json_value['term']) && !empty($json_value['term'])) 
         $term_id = $json_value['term'];

   }
  
   $query_args = array(
      'post_type'      => $post_type,
      'posts_per_page' => $instance['posts_num'] ? $instance['posts_num'] : 10,
      'post__not_in'   => $instance['exclude'] ? $instance['exclude'] : '',
      'orderby'        => $instance['orderby'],
      'order'          => $instance['order'],
      'featured'  => 'yes'
   );

  
   if(($taxonomy != 'all') && ($term_id !='all')){
      $query_args['tax_query'] =  array(
         array(
            'taxonomy'         => $taxonomy,
            'field'            => 'id',
            'terms'            => $term_id,
         ),
        
      );

   }
 
   $wplb_query = new WP_Query($query_args);
  
   $counter = 0;

   $html .= '<ul class="wplb-listing">';

      while ($wplb_query->have_posts()) {
         $wplb_query->the_post();

         $image = wplb_get_image( array(
            'format'  => 'html',
            'size'    => $instance['image_size'],
            'context' => 'featured-post-widget'
         ) );
        
        $html .= '<li class="wplb-item">';

          if(isset($instance['show_image']) && !empty($image)){
            $html .= '<a href="'.get_permalink().'">';
               $html .= '<div class="wplb-thumbnail">';
                  $html .= $image;
               $html .= '</div>';
            $html .= '</a>';
          }

          if ( $instance['show_title'] )
            $html .= '<h3 class="wplb-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

          $html .= '<p class="wplb-meta">';

          if ( ! empty( $instance['show_gravatar'] ) ) {
            $html .= '<span class="wplb-gravatar">';
            $html .= get_avatar( get_the_author_meta( 'ID' ), $instance['gravatar_size'] );
            $html .= '</span>';
          }

          if ( $instance['show_meta_date'] || $instance['show_meta_cat'] ){
            $html .= '<span class="wplb-post-meta">';

               if ( $instance['show_meta_date'])
                  $html .= wplb_get_time().' ';

               if ( $instance['show_meta_cat'])
                  $html .get_the_category_list( ', ' );

            $html .= '</span>';
          }

          $html .= '</p>';
          if(!empty($instance['show_content'])){
            $html .= '<p class="wplb-entry-content">';
               if ( 'excerpt' == $instance['show_content'] ) {
                 $html .= get_the_excerpt();
               }
               elseif ( 'content-limit' == $instance['show_content'] ) {
                 $html .= wplb_get_the_content_limit( (int) $instance['content_limit'], esc_html( $instance['more_text'] ) );
               }
               else {

                 global $more;

                 $orig_more = $more;
                 $more = 0;

                 $html .= get_the_content( esc_html( $instance['more_text'] ) );

                 $more = $orig_more;

               }
            $html .= '</p>';
          }

         $html .= '</li>';

      }

   $html .= '</ul>';
   
   wp_reset_query();

   $html .= '</div>';

   $html .= $after_widget;

   return apply_filters('wplb-widget-featured', $html, $args, $instance) ;
  }
}


class Wplb_Featured
{
   var $db = NULL;
   public $post_types = array();
   
  function __construct() {

     add_filter('query_vars', array(&$this, 'query_vars' ));

     add_action('pre_get_posts', array(&$this, 'pre_get_posts'));

     add_action('admin_init', array(&$this,'admin_init'));

     add_action('wp_ajax_toggle-featured', array(&$this,'do_toggle_featured'));
   }
 
  public function query_vars($public_query_vars) {
       $public_query_vars[] = 'featured';
       return $public_query_vars;
   }

  public function pre_get_posts($query) {
       if (!is_admin()) {
           if ($query->get('featured') == 'yes') {
               $query->set('meta_key', 'wplb_featured');
               $query->set('meta_value', 'yes');
           }
       }
       return $query;
   }

  public function admin_init() {

     add_filter('current_screen', array(&$this, 'my_current_screen'));

     add_action('admin_head-edit.php', array(&$this, 'script_enqueue'));

     add_filter('pre_get_posts', array(&$this,'admin_pre_get_posts') , 1);

     $this->post_types = apply_filters('wplb_post_type_feature', wplb_get_post_types());

     ksort($this->post_types);
     foreach ($this->post_types as $key => $val) {

        add_filter('manage_edit-' . $key . '_columns', array(&$this, 'manage_posts_columns' ));

        add_action('manage_' . $key . '_posts_custom_column', array(&$this, 'manage_posts_custom_column' ) , 10, 2);

     }
  }
     
  public function script_enqueue(){
     wp_enqueue_script('wplb-featured-js',WPLB_WIDGETS_JS_URL.'featured.js');
  }

  public function my_current_screen($screen) {
       if (defined('DOING_AJAX') && DOING_AJAX) {
           return $screen;
       }
       $this->post_types = apply_filters('wplb_post_type_feature', wplb_get_post_types(array(), array('attachment')));
     
       foreach ($this->post_types as $key => $val) {
           add_filter('views_edit-' . $key, array(&$this,
               'add_views_link'
           ));
       }
       return $screen;
  }

  public function add_views_link($views) {
       $post_type = ((isset($_GET['post_type']) && $_GET['post_type'] != "" && $_GET['post_type'] != "product") ? $_GET['post_type'] : 'post');
       $count = $this->total_featured($post_type);
       $class = (isset($_GET['post_status']) && $_GET['post_status'] == 'featured') ? "current" : '';
       $views['featured'] = "<a class=\"" . $class . "\" id=\"featured-post-filter\" href=\"edit.php?&post_status=featured&post_type={$post_type}\">Featured <span class=\"count\">({$count})</span></a>";
       return $views;
  }

  public function total_featured($post_type = "post") {
       $rowQ = new WP_Query(array(
           'post_type' => $post_type,
           'meta_query' => array(
               array(
                   'key' => 'wplb_featured',
                   'value' => 'yes'
               )
           ) ,
           'posts_per_page' => 1
       ));
       wp_reset_postdata();
       wp_reset_query();
       $rows = $rowQ->found_posts;
       unset($rowQ);
       return $rows;
   }

  public function manage_posts_columns($columns) {
       global $current_user;
       get_currentuserinfo();
       if (current_user_can('edit_posts', $user_id)) {
           $columns['featured'] = __('Featured');
       }
       return $columns;
  }


  public function manage_posts_custom_column($column_name, $post_id) {
       
       if ($column_name == 'featured') {
          if(isset($_GET['post_type']) && $_GET['post_type'] = "product"){
            return;
          } 
           $is_featured = get_post_meta($post_id, 'wplb_featured', true);
           $class = "dashicons";
           $text = "";
           if ($is_featured == "yes") {
               $class.= " dashicons-star-filled";
               $text = "";
           } else {
               $class.= " dashicons-star-empty";
           }
           echo '<a href="#" class="featured-toggle '.$class.'" data-post-id="'.$post_id.'">'.$text.'</a>';
       
       }
   }
  
  public function do_toggle_featured() {
       header('Content-Type: application/json');
       $post_id     = $_POST['post_id'];
       $is_featured = get_post_meta($post_id, 'wplb_featured', true);
       $newStatus   = $is_featured == 'yes' ? 'no' : 'yes';

       delete_post_meta($post_id, 'wplb_featured');

       add_post_meta($post_id, 'wplb_featured', $newStatus);

       echo json_encode(array(
           'ID' => $post_id,
           'new_status' => $newStatus,
           'total_featured' => $this->total_featured(get_post_type($post_id))
       ));
       die();
   }
  public function admin_pre_get_posts($query) {
       global $wp_query;
       if (is_admin() && isset($_GET['post_status']) && ($_GET['post_status'] == 'featured')) {
           $query->set('meta_key', 'wplb_featured');
           $query->set('meta_value', 'yes');
       }
       return $query;
   }
   
}

