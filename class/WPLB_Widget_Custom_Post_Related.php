<?php
/**
* WPLB List Custom Post
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/

class WPLB_Widget_Custom_Post_Related extends WPLB_Widgets
{

  public $fields;
  public $defaults;

  public function __construct()
  {
    $widgets_init = array(
        'id_base' => 'wplb-post-type-retaled-widget',
        'name'    => $this->plugin_name.__('Post Type Related','wplb'),
        'options' => array(
            'classname'   => 'wplb-list-post-type-related-widget',
            'description' => __( 'Notice: This Widget only display on single page', 'wplb' )
          )
      );

      // Defaults
      $defaults = array(
          'title' => array(
              'label' =>  __('Title', 'wplb'),
              'std'   =>  __('Post Type Related', 'wplb'),
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
              'type'  => 'text',
          )
        );


      $widgets_init = apply_filters('wplb_widgets_list_posts_init', $widgets_init);
      $defaults = apply_filters('wplb_widgets_list_posts_default', $defaults);

      parent::__construct( $widgets_init, $defaults);
      
      

  }

  /**
   * Get and return related posts.
   *
   * @param int $limit (default: 5)
   * @return array Array of post IDs
   */
  public function get_related( $post_type = 'post' , $taxonomy = 'category', $limit = 5 ) {
    // Don't bother if none are set
    if (!is_singular($post_type) ) {

      $related_posts = array();

    } else {

      global $post, $wpdb;

      $post_id = $post->ID;

      // Sanitize
      $exclude_ids = array_map( 'absint', array_merge( array( 0, $post_id ) ) );

      // Related posts are found from taxonomy
      $cats_array = $this->get_related_terms($taxonomy );
      // Generate query
      $query = $this->build_related_query($post_id, $post_type, $taxonomy, $cats_array, $exclude_ids, $limit );

      // Get the posts
      $related_posts = $wpdb->get_col( implode( ' ', $query ) );
    }


    shuffle( $related_posts );

    return $related_posts;
  }

  /**
   * Retrieves related product terms
   *
   * @param string $term
   * @return array
   */
  protected function get_related_terms($taxonomy ) {
    $terms_array = array(0);

    $terms = apply_filters( 'wplb_get_related_' . $taxonomy . '_terms', get_terms( $taxonomy ), $taxonomy );
    foreach ( $terms as $term ) {
      $terms_array[] = $term->term_id;
    }

    return array_map( 'absint', $terms_array );
  }

  /**
   * Builds the related posts query
   *
   * @param array $cats_array
   * @param array $exclude_ids
   * @param int   $limit
   * @return string
   */
  protected function build_related_query($post_id, $post_type, $taxonomy, $cats_array, $exclude_ids, $limit ) {
    global $wpdb;

    $limit = absint( $limit );

    $query           = array();
    $query['fields'] = "SELECT DISTINCT ID FROM {$wpdb->posts} p";
    $query['join']   = " INNER JOIN {$wpdb->postmeta} pm ON ( pm.post_id = p.ID AND pm.meta_key='_visibility' )";
    $query['join']  .= " INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)";
    $query['join']  .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
    $query['join']  .= " INNER JOIN {$wpdb->terms} t ON (t.term_id = tt.term_id)";

    $query['where']  = " WHERE 1=1";
    $query['where'] .= " AND p.post_status = 'publish'";
    $query['where'] .= " AND p.post_type = '".$post_type."'";
    $query['where'] .= " AND p.ID NOT IN ( " . implode( ',', $exclude_ids ) . " )";
    $query['where'] .= " AND pm.meta_value IN ( 'visible', 'catalog' )";

    if ( apply_filters( 'woocommerce_product_related_posts_relate_by_category', true, $post_id ) ) {
      $query['where'] .= " AND ( tt.taxonomy = '".$taxonomy."' AND t.term_id IN ( " . implode( ',', $cats_array ) . " ) )";
      $andor = 'OR';
    } else {
      $andor = 'AND';
    }


    $query['limits'] = " LIMIT {$limit} ";
    $query           = apply_filters( 'woocommerce_product_related_posts_query', $query, $post_id );

    return $query;
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

   $class = 'wplb-widget-post ';
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
   $limit = $instance['posts_num'] ? $instance['posts_num'] : 10;
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
   if(!is_singular($post_type)){
      return;
   }else{
      global $post;
      $post_id = $post->ID;
   }
   $related = $this->get_related($post_type, $taxonomy, $limit);

   $query_args = array(
      'post_type'      => $post_type,
      'ignore_sticky_posts'  => 1,
      'no_found_rows'        => 1,
      'posts_per_page' => $limit,
      'post__not_in'   => array($post_id),
      'post__in'       => $related,
      'orderby'        => $instance['orderby'],
      'order'          => $instance['order'],
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

   if($wplb_query->have_posts()){

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
   }
   $html .='</div>';

   $html .= $after_widget;

   return apply_filters('wplb-widget-custom-post', $html, $args, $instance) ;
  }

}
