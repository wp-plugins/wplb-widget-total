<?php

    /**
     * WPLB Categories Widget
     *
     * @author Huu Ha <huuhath@gmail.com>
     * @link http://www.wplabels.com
     */

    class WPLB_Widget_Taxonomy extends WPLB_Widgets
    {

        /**
         * The constructor for this widget.
         *
         * @return void
         * @author Huu Ha
         */

        public function __construct()
        {
          $widgets_init = array(
            'id_base' => 'wplb-custom-taxonomy-widget',
            'name'    => $this->plugin_name.__(' Custom Taxonomy','wplb'),
            'options' => array(
              'classname'   => 'wplb-widgets-total wplb-custom-taxonomy-widget',
              'description' => __( 'Display list term or custom post of custom taxonomy', 'wplb' )
              )
            );
          $levels = array();
          for ($i=0; $i <= 10; $i++) { 
            $levels[$i] = $i;
          }
          $defaults = array(

            'title' => array(
              'label' =>  __('Title', 'wplb'),
              'std'   =>  __('Custom Taxonomy', 'wplb'),
              'type'  => 'text',
              ),
            'post_and_term' => array(
              'type' =>'post_to_term' // only need id and type 
            ),
            'hide_empty' => array(
               'label' => __('Hide empty?'),
               'type' =>'checkbox',
               'std' => false
            ),
            'show_count' => array(
               'label' => __('Show Count ?'),
               'type' =>'checkbox',
               'std' => false
            ),
            'hierarchical' => array(
               'label' => __('Hierarchical ?'),
               'type' =>'checkbox',
               'std' => true
            ),
            'level' => array(
              'label'   => __('Level', 'wplb'),
              'type'    => 'select',
              'options' => $levels,
              'std' => '0',
              'desc' => __('0 - to show all levels, 1 - Show only top level (hierarchical must enabled)','wplb')
              ),
            'sortby' => array(
              'label'   => __('Sort by', 'wplb'),
              'type'    => 'select',
              'options' => array(
                'date'          => __('Date','wplb'),
                'ID'            => __('ID','wplb'),
                'title'         => __('Title','wplb'),
                ),
              'std' => 'date',

              ),
            'order' => array(
              'label'   => __('Order', 'wplb'),
              'type'    => 'select',
              'options' => array(
                'DESC' => __('Descending (3, 2, 1)','wplb'),
                'ASC'  => __('Ascending (1, 2, 3)','wplb'),
                ),
              'std' => 'DESC',
              ),
            
            'exclude_cat' => array(
                'type'  => 'text',
                'label' =>__('Exlude Categories ids: ','wplb'),
                'desc'  =>__('These categories (separated by commas), include must be empty', 'wplb'),
                'class' => 'float-left',
             ),
            'include_cat' => array(
                'type'  => 'text',
                'label' =>__('Include Categories ids: ','wplb'),
                'desc'  =>__('These categories (separated by commas)', 'wplb'),
                'class' => 'float-left',
             ),
            'display' => array(
              'label'   => __('Display', 'wplb'),
              'type'    => 'select',
              'options' => array(
                'list' => __('Display as list','wplb'),
                'drop'  => __('Display as dropdown','wplb'),
                'checklist'  => __('Display as check list','wplb'),//Width Pro
                ),
              'std' => 'list',
              ),
            'class' => array(
              'label' => __('Class: ', 'wplb'),
              'type' => 'text'
            )
            
          );

          $widgets_init = apply_filters('wplb_widgets_category_init', $widgets_init);

          $defaults = apply_filters('wplb_widgets_category_default', $defaults);

          parent::__construct($widgets_init, $defaults);
          add_action( 'wp_ajax_wplb_widget_get_term_action', array($this, 'do_wplb_widget_get_term_action') );

        }

        /**
         * Format the output for this widget.
         *
         * @return string
         * @author Huu Ha
         */

        public function output($args, $instance)
        {
          extract( $args );
   
          $html = $before_widget;

          $class = 'wplb-widget-taxonomy ';

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
          $taxonomy = 'category';

          $json_value = json_decode($instance['post_and_term']);
          $json_value = (array)$json_value[0];

          if(isset($json_value['post_type']) && !empty($json_value['post_type'])) 
            $post_type = $json_value['post_type'];

          if(isset($json_value['taxonomy']) && !empty($json_value['taxonomy'])) 
            $taxonomy = $json_value['taxonomy'];

          $term_id = $json_value['term'];

          $tax_args = array(
               'taxonomy' => $taxonomy,
               'title_li' => '',
               'orderby' => $instance['sortby'],
               'order'    => $instance['order'],
               'depth' => $instance['level'],
               'hierarchical'=>$instance['hierarchical'] ? $instance['hierarchical'] : 0,
               'show_count' => $instance['show_count'],
               'hide_empty' => $instance['hide_empty'],
               'exclude' => $instance['exclude_cat'] ? $instance['exclude_cat'] : '',
               'exclude_tree' => $instance['exclude_cat'] ? $instance['exclude_cat'] : '',
               'include' => $instance['include_cat'] ? $instance['include_cat'] : '',
               'echo' => false,
               'show_option_none'   =>  __('Please Select...','wplb'),
               'link_after' => '',
           );
          
          if($term_id != 'all'){
            $tax_args['child_of'] = $term_id;
          }

          $display = $instance['display'];

          $html_tax = '<input type="hidden" name="taxonomy_val" class="taxonomy_val"  value="'.$taxonomy.'">';

          $current_term_object = get_queried_object();

          if( is_category() || is_tax() || is_tag() ){
            $current_term_id = $current_term_object->term_id;
            $tax_args['selected'] = $current_term_id;
          }

          $tax_args['class'] = 'wplb-'.$display.'-taxonomy-widget';

          $tax_args = apply_filters('wplb-widget-taxonomy-tax-args', $tax_args);

          if($display=='drop'){
         
            $tax_args['name'] = $taxonomy;
            $tax_args['id'] = $widget_id.'-'.$taxonomy;
            $html_tax .= wp_dropdown_categories( $tax_args );

          }elseif($display=='checklist'){

            $tax_args['walker'] = new WPLB_Walker_Taxonomy_Checklist();
            $html_tax .= '<ul class="current">';
            $html_tax .= wp_list_categories( $tax_args );
            $html_tax .= '</ul>';
            

          }else{

            $html_tax .= '<ul class="current">';
            $html_tax .= wp_list_categories( $tax_args );
            $html_tax .= '</ul>';

          }

        $html .= $html_tax;

        $html .= '</div>';

        $html .= $after_widget;

        return apply_filters('wplb-widget-taxonomy', $html, $args, $instance) ;

      }

      publiC function do_wplb_widget_get_term_action(){
          $json = array();
          $term_id = $_POST['term_id'];
          $taxonomy = $_POST['taxonomy'];
          $term = get_term_by('id', $term_id,$taxonomy);
          $json['term_url'] = esc_js(get_term_link($term));
          echo json_encode($json);
          wp_die();
        }

    }

/**
 * Create HTML Checkbox list of Taxonomy.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
if(!class_exists('WPLB_Walker_Taxonomy_Checklist')){
  class WPLB_Walker_Taxonomy_Checklist extends Walker_Category
  {
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent<ul class='children'>\n";
    }
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
      
      if ( empty( $args['taxonomy'] ) ) {
        $taxonomy = 'category';
      } else {
        $taxonomy = $args['taxonomy'];
      }

      if ( $taxonomy == 'category' ) {
        $name = 'post_category';
      } else {
        $name = 'tax_input[' . $taxonomy . ']';
      }

      $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
      $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

      $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];
      $selected = empty( $args['selected'] ) ? '' : $args['selected'];
      $type = empty( $args['multiple_select'] ) ? 'radio' : 'checkbox';
      if($type=='radio'){
        $checked = checked($category->term_id,$selected , false, false );
      }
      if($type=='checkbox'){
        $checked = checked( in_array( $category->term_id, $args['selected_cats'] ), fasle, false );
      }

      $count = '';

      if ( ! empty( $args['show_count'] ) ) {
        $count = ' (' . number_format_i18n( $category->count ) . ')';
      }
  /** This filter is documented in wp-includes/category-template.php */
      if ( ! empty( $args['list_only'] ) ) {
        $aria_cheched = 'false';
        $inner_class = 'category';

        if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
          $inner_class .= ' selected';
          $aria_cheched = 'true';
        }

        $output .= "\n" . '<li' . $class . '>' .
          '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
          ' tabindex="0" role="radio" aria-checked="' . $aria_cheched . '">' .
          esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
      } else {
        $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
          '<label class="checklist"><input class="iterm_radio" data-taxonomy="'.$taxonomy.'" value="' . $category->term_id . '" type="'.$type.'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
          $checked .
          disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
          esc_html( apply_filters( 'the_category', $category->name ) ) . $count . '</label>';
      }
    }

    public function end_el( &$output, $category, $depth = 0, $args = array() ) {
      $output .= "</li>\n";
    }
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent</ul>\n";
    }

  }
}

