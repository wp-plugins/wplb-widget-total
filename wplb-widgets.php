<?php

    /**
     * Plugin Name: WPLB Widget Total
     * Plugin URI: http://www.wplabels.com
     * Description: Basic sidebar widget functionality. Widgets include: Custom post, Custom taxonomy, Image with upload button, Video (YouTube, Vimeo), Custom Post featured, Socials link, Facebook like box.
     * Author: Huu Ha
     * Author URI: http://www.wplabels.com
     * Version: 1.0.0
     */

function wplb_autoload_class( $class ) {
    $name = explode( 'WPLB_Widget_', $class );
    if ( isset( $name[1] ) ) {
        $class_name = $name[1];
        $filename = dirname( __FILE__ ) . '/class/WPLB_Widget_' . $class_name . '.php';
        if ( file_exists( $filename ) ) {
            require_once $filename;
        }
    }
}
spl_autoload_register( 'wplb_autoload_class' );


// Define plugin URLs, for fast enqueuing scripts and styles
if ( ! defined( 'WPLB_WIDGETS' ) )
  define( 'WPLB_WIDGETS', plugin_dir_url( __FILE__ ) );
  define( 'WPLB_WIDGETS_JS_URL', trailingslashit( WPLB_WIDGETS . 'js' ) );
  define( 'WPLB_WIDGETS_CSS_URL', trailingslashit( WPLB_WIDGETS . 'css' ) );
  define( 'WPLB_WIDGETS_IMG_URL', trailingslashit( WPLB_WIDGETS . 'images' ) );

// Plugin paths, for including files
if ( ! defined( 'WPLB_WIDGETS_DIR' ) )
  define( 'WPLB_WIDGETS_DIR', plugin_dir_path( __FILE__ ) );
  define( 'WPLB_WIDGETS_CLASS_DIR', trailingslashit( WPLB_WIDGETS_DIR . 'class' ) );

include_once ('wplb-function.php');   

    
    // Initialize the widgets.
    add_action('widgets_init', 'wplb_register_widgets');
    function wplb_register_widgets(){
       
        $widgets_init = array(
            'WPLB_Widget_Image',
            'WPLB_Widget_Taxonomy',
            'WPLB_Widget_Custom_Post',
            'WPLB_Widget_Socials',
            'WPLB_Widget_Video',
            'WPLB_Widget_Featured',
            'WPLB_Widget_Maps',
            'WPLB_Widget_Facebook_Box'
        );
        $widgets_init = apply_filters('wplb_widgets_init', $widgets_init);
        
        foreach ($widgets_init as $widget_class_name) {

            if(class_exists($widget_class_name))
                register_widget($widget_class_name);
        }
    }

    /**
     * WPLB Sidebar Widget
     *
     * This class serves as the base class for any WPLB Sidebar Widget. This
     * class is generic enough that it can be used for future widgets.
     *
     * @author Huu Ha
     * @link http://www.wplabels.com
     */

    class WPLB_Widgets extends WP_Widget
    {
        // Hold the fields.
        protected $fields = array();

        protected $defaults = array();
        /**
         * @var string
         */
        public $plugin_name = ':WPLB-';

        /**
         * @var string
         */
        public $version = '1.0.0';

      
        /**
         * The constructor for this widget.
         *
         * @return void
         * @author Huu Ha <huuhath@gmail.com>
         */

        public function __construct($widgets_init = array(), $defaults = array())
        {
            global $pagenow;
            $widgets_init = wp_parse_args($widgets_init, array(
                          'id_base' => 'wplb-widget-default',
                          'name'    => $this->plugin_name.__(' WPLB Widget Default','wplb'),
                          'options' => array(
                              'classname'   => 'wplb-widget-default',
                              'description' => __( 'This Is A WPLB Widget Default', 'wplb' )
                            )
                    ));

            // Initialize the widget.
            parent::__construct($widgets_init['id_base'], $widgets_init['name'], $widgets_init['options']);

            $this->defaults = $defaults;

            $this->fields = $this->get_fields();


            //admin
            if (is_admin() && $pagenow == 'widgets.php') {
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
                
            }
            if(!is_admin()){
              add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
            }

            add_action( 'wp_ajax_wplb_widget_ajax_action', array($this, 'do_wplb_widget_ajax_action') );
            add_action( 'wp_ajax_nopriv_wplb_widget_ajax_action', array($this, 'do_wplb_widget_ajax_action') );

            add_action( 'wp_ajax_wplb_widget_ajax_frondend_action', array($this, 'do_wplb_widget_ajax_frondend_action') );
            
        }
       
        /**
         * Create all fields
         *
         * @since 0.1
         */
        public function get_fields() {

          $fields = array();

          $defaults = wp_parse_args($this->defaults,array(
              'title'       => array(
                    'label' =>  __('Title', 'wplb'),
                    'type'  => 'text',
                    'desc'  => __('If an item is left blank it will not be output.', 'wplb')
                  )
            )
          );
     
          //create id fields
          foreach ($defaults as $key => $value) {
              $fields[$key] = '';

              if(!empty($value['std'])){
                  $fields[$key] = $value['std'];
              }

              if(isset($value['fields']) && is_array($value['fields'])){
                foreach ($value['fields'] as $child_key => $child_value) {
                  $fields[$key.'_'.$child_key] = '';
                  if(!empty($child_value['std'])){
                    $fields[$key.'_'.$child_key] = $child_value['std'];
                  }
                }
              }

         
          }
           
          return $fields;

        }
        /**
         * Enqueue the scripts and styles.
         *
         * @return void
         * @author Huu Ha <huuhath@gmail.com>
         */

        public function frontend_enqueue_scripts() {
          wp_enqueue_style('wplb-widget-admin-style',WPLB_WIDGETS_CSS_URL . 'frontend-style.css');
          wp_register_script( 'wplb-ajax-frontend-js', WPLB_WIDGETS_JS_URL .'script.js', array( 'jquery' ), '');
          wp_enqueue_script( 'wplb-ajax-frontend-js' );
          wp_localize_script( 'wplb-ajax-frontend-js', 'wplb_js_var', array(
                'ajaxurl'   => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce( 'wplb_frontend_nonce' ),
                'site_url'  => site_url(),
                'theme_url' => get_bloginfo('stylesheet_directory')
              ) 
          );
        }
        /**
         * Enqueue the admin scripts.
         *
         * @return void
         * @author Huu Ha <huuhath@gmail.com>
         */

        public function admin_enqueue_scripts(){
             // STYLES
            wp_enqueue_style('wplb-widget-admin-style',WPLB_WIDGETS_CSS_URL . 'admin-style.css');
            wp_register_script( 'wplb-admin-js', WPLB_WIDGETS_JS_URL .'wplb-scripts.js', array( 'jquery' ), '');
            wp_enqueue_script( 'wplb-admin-js' );
            wp_localize_script( 'wplb-admin-js', 'wplb_var', array(
                  'ajaxurl'   => admin_url('admin-ajax.php'),
                  'nonce'     => wp_create_nonce( 'wplb_nonce' ),
                  'site_url'  => site_url(),
                  'theme_url' => get_bloginfo('stylesheet_directory'),
                  'upload'    => __( 'Upload', 'wplb' ),
                  'remove'    => __( 'Remove', 'wplb' )
                ) 
            );
        }

        public function do_wplb_widget_ajax_frondend_action(){
          $json = array();
          
          echo json_encode($json);
          wp_die();
        }

        public function do_wplb_widget_ajax_action(){

          $do_action = $_POST['do_action'];
          $json = array();
          $tax = '';
          $t = '<option value="all">'.__('All','wplb').'</option>';
          $tax_value = 'all';
          $term_value = 'all';
         
          if($do_action == 'get_by_post_type'){
            $post_type = $_POST['post_type'];
            $taxonomies = get_object_taxonomies( $post_type);
            
            if(!empty($taxonomies) && is_array($taxonomies)){
              // foreach ($taxonomies as $k=>$t) {
              //      $tax_args[$k] = $t->label;
              //   }
               foreach ($taxonomies as $name) {

                  $tax  .= '<option value="'.$name.'">'.$name.'</option>';
                 
                }
                $tax_value = $taxonomies[0];
             }

            $terms = get_terms($tax_value, array('hide_empty'=>false));

            if(!empty($terms) && is_array($terms)){
               foreach ($terms as $term) {
                  $t .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
               }

              $term_first = (array)$terms[0];
              
              $term_value = $term_first['term_id'];
            }

            $json['tax']  = $tax;
            $json['term'] = $t;
            $json['tax_value']  = $tax_value;
            $json['term_value'] = $term_value;
         }

         if($do_action == 'get_by_taxonomy'){

            $taxonomy = $_POST['taxonomy'];

            if($taxonomy != 'all'){
               $terms = get_terms($taxonomy, array('hide_empty'=>false));
               if(!empty($terms)){
                  foreach ($terms as $term) {
                     $t .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
                  }
               }
            }

            $json['term'] = $t;
            $json['term_value'] = $term_value;

         }

            echo json_encode($json);
            
            die; 

        }
        

        /**
         * Hook the update process.
         *
         * @return array
         * @author Huu Ha <huuhath@gmail.com>
         */

        public function update($new_instance, $old_instance)
        {
          // echo '<pre>';
          // print_r($new_instance);
          // print_r($old_instance);
          // print_r($this->fields);
          // echo '</pre>';
          // die;
            return WPLB_Widgets::update_instance(
                $new_instance,
                $old_instance,
                $this->fields
            );

        }

        /**
         * Hook the widget output process.
         *
         * @return void
         * @author Huu Ha <huuhath@gmail.com>
         */

        public function widget($args, $instance)
        {

            // Get a copy of the fields.
            $data = $this->fields;
            
            foreach ($data as $k => $v) {
                // Copy instance values to data array.
                $data[$k] = $instance[$k];
            }

            // Output the HTML.
            echo $this->output($args, $data);

        }

        /* ======================================== */
        /* Placeholder Methods
        /* ======================================== */

        public function form($instance) {
            
            $widget_id = (isset($this->id) ? $this->id : '0');

            $html = '<div class="wplb-wrapper-widget">';
            
            $instance = wp_parse_args(
                (array) $instance,
                $this->fields
            );
            
            foreach ($this->defaults as $key => $options) {
                $class_name = $options['type'];
                $options['name'] = $key;
                $options['id'] = $widget_id;

                if(isset($options['class'])){
                    $options['class'] = 'wplb-widget '.$options['class'];
                }else{
                     $options['class'] = 'wplb-widget';
                }

                $html .= call_user_func( array( 'WPLB_Widgets', $class_name ), $instance, $options );
            }

            $html .= '</div>';

            echo $html;
        }

        protected function output($args, $data) {
            $html = $args['before_widget'];
                $html .= $args['before_title'];
                    $html .= $this->title($args, $data['title']);
                $html .= $args['after_title'];
            $html .= $args['after_widget'];

            return $html;
        }

        /**
         * Update an instance using key/value pairs.
         *
         * @return array
         * @author Huu Ha <huuhath@gmail.com>
         */

        protected function update_instance($new_instance, $old_instance, $fields)
        {

            $instance = $old_instance;

            foreach ($fields as $k => $v) {
                $instance[$k] = $new_instance[$k];
            }

            return $instance;

        }

        /**
         * Output a text field. Used by the admin interface.
         *
         * @param $options
         * - name: The unique slug of the element. Ex: name
         * - label: The title of the element. Ex: Title
         * @return string
         * @author Huu Ha <huuhath@gmail.com>
         */

        protected function text($instance, $options)
        {
     
            if (!is_array($options)) {
                return '';
            }

            $options = self::normalize($options);
           
            $field_name = $options['name'];

            $class = $options['class'] . ' wplb-' . $options['type'] .' ' . $options['name'];

            $desc = !empty($options['desc'])?'<div class="help">' . $options['desc'] . '</div>':'';

            $output = '';

            $output .= '<p class="' . $class . '">';
                $output .= '<label for="' . $this->get_field_id($field_name) . '"> ' . $options['label'] . ' </label>';
                $output .= '<input type="text" name="' . $this->get_field_name($field_name) . '" id="' . $this->get_field_id($field_name) . '" value="' . esc_attr($instance[$field_name]) . '" />';
                $output .= $desc;
            $output .= '</p>';

            return $output;

        }

        protected function post_to_term($instance, $options)
        {   
           
            if(!isset($options['class']) || empty($options['class'])){
                $options['class'] = 'hidden';
            }else{
                $options['class'] = $options['class'].' hidden';
            }
            $options['class'] = apply_filters('wplb_widgets_post_type_post_to_term_default_values', $options['class']);
            
            $field_name = $options['name'];

            $opt['post_type'] = wplb_get_post_types();

            
            $json = array(
                'post_type' => array(
                      'label'     => __('Post Type','wplb'),
                      'value'       => 'post',
                      'opt'       => 'post_type',
                  ),
                'taxonomy'  => array(
                      'label'     => __('Taxonomy', 'wplb'),
                      'value'   => 'category',
                      'opt'   => 'tax',
                  ),
                'term'      => array(
                      'label'     => __('Term', 'wplb'),
                      'value'   => '1',
                      'opt'   => 'term',
                  )
            );
            
            if (empty($instance[$field_name])) {
              $instance[$field_name] = '[{"post_type":"post","taxonomy":"category","term":"1"}]';
            }

            $json_value = json_decode($instance[$field_name]);

            $json_value = $json_value[0]; 
           
            if($json_value->post_type)
                $json['post_type']['value'] = $json_value->post_type;
            
            if($json_value->taxonomy)
                $json['taxonomy']['value'] = $json_value->taxonomy;
            
            if($json_value->term)
                $json['term']['value'] = $json_value->term;

           
            $tax_args = array('all'=>__('All','wplb'));
            $taxonomies = get_object_taxonomies($json['post_type']['value'],'objects');
            if(!empty($taxonomies) && is_array($taxonomies)){
                foreach ($taxonomies as $k=>$t) {
                   $tax_args[$k] = $t->label;
                }
            }
            $opt['tax'] = $tax_args;


            $term_args = array('all'=>__('All','wplb'));
            $terms = get_terms($json['taxonomy']['value'], array('hide_empty'=>false));
            if(!empty($terms) && is_array($terms)){
                foreach ($terms as $term) {
                   $term_args[$term->term_id] = $term->name;
                }
            }
            $opt['term'] = $term_args;
            
            

            $html = '<div class="wplb-post-term">';
            $html .= $this->textarea($instance, $options);
            foreach ($json as $k => $v) {

                $option = $opt[$v['opt']];
                
                $html .= $this->select(array($k=>$v['value']), array(
                    'name'     =>$k, 
                    'id'     =>$options['id'], 
                    'label'   =>$v['label'], 
                    'type'   =>'select-'.$v['opt'], 
                    'options' =>$option,
                    ))
                ;
            }

            $html .= '</div>';

            return $html;

        }

        protected function select($instance, $options)
        {
            
            if (!is_array($options)) {
                return '';
            }

            $defaults = array(
                'name'    => '',
                'label'   => __('Select','wplb'),
                'class'   => '',
                'desc'    => '',
                'options' =>array(),
                'std'     => '',
                'multiple'    => false,
                'size' => 5
            );
          
            $defaults = apply_filters('wplb_widgets_select_default_values', $defaults);

            $options = wp_parse_args( $options, $defaults );

            $field_name = $options['name'];

            $class = $options['class'] . ' wplb-' . $options['type'] .' ' . $options['name'];

            $desc = !empty($options['desc'])?'<div class="help">' . $options['desc'] . '</div>':'';

            $output = '';
                
            $output .= '<p class="'.$class .'">';
                $output .= '<label for="' . $this->get_field_id($field_name) . '"> ' . $options['label'] . ': </label>';

                $output .= sprintf(
                            '<select data-widget_id="%s" class="wplb-select" name="%s" id="%s" size="%s"%s data-options="%s">',
                            $options['id'] ? $options['id'] : '',
                            $options['multiple'] ? $this->get_field_name($field_name).'[]' : $this->get_field_name($field_name),
                            $this->get_field_id($field_name),
                            $options['multiple'] ? $options['size'] : 0,
                            $options['multiple'] ? ' multiple' : '',
                            esc_attr( json_encode( $options['options'] ) )
                        ); 

                        $option = '<option value="%s"%s>%s</option>';
                        if(!empty($options['options'])){
                            foreach ($options['options'] as $value => $label) {
                            
                                $output .= sprintf(
                                        $option,
                                        $value,
                                        selected( in_array( $value, (array) $instance[$field_name] ), true, false ),
                                        $label
                                    );
                            }
                        }
                   
                $output .= '</select><span class="spinner"></span>';

                $output .= $desc;

            $output .= '</p>';
            

            return $output;

        }

        protected function post_type($instance, $options){
            $defaults = array(
                'name'    => '',
                'label'   => __('Select','wplb'),
                'class'   => '',
                'desc'    => '',
                'options' =>wplb_get_post_types(),
                'std'     => 'post',
                'multiple'    => false,
                'size' => 5
            );

            $defaults = apply_filters('wplb_widgets_post_type_default_values', $defaults);

            $options = wp_parse_args( $options, $defaults );

            return $this->select($instance, $options);
        }
        /**
         * Output a checkbox field. Used by the admin interface.
         *
         * @param $options
         * - name: The unique slug of the element. Ex: name
         * - label: The title of the element. Ex: Title
         * @return string
         * @author Huu Ha <huuhath@gmail.com>
         */

        protected function checkbox($instance, $options)
        {
           
            if (!is_array($options) && empty($options['name'])) {
                return '';
            }

            $options = self::normalize($options);
                           
      
            $field_name = $options['name'];

            $class = $options['class'] . ' wplb-' . $options['type'] .' ' . $options['name'];

            $desc = !empty($options['desc'])?'<div class="help">' . $options['desc'] . '</div>':'';

            $checked = '';

            if($instance[$field_name]=='true'){
                $checked = 'checked="checked"';
            }
           
            $output = '<p class="' . $class . '">';
              $output .= '<label for="' . $this->get_field_id($field_name) . '"> ' . $options['label'] . ': </label>';
              $output .= '<input type="checkbox" name="' . $this->get_field_name($field_name) . '" id="' . $this->get_field_id($field_name) . '"  '.$checked.' value="true" />';
              $output .= $desc;
            $output .= '</p>';


            return $output;

        }
        

        /**
         * Output a text area. Used by the admin interface.
         * @return string
         * @author Huu Ha <huuhath@gmail.com>
         */

        protected function textarea($instance, $options)
        {
            
            $defaults = array(
                'cols' => 30,
                'rows' => 5
            );

            $defaults = apply_filters('wplb_widgets_textarea_default_values', $defaults);

            $options = wp_parse_args( $options, $defaults );

            if (!is_array($options)) {
                return '';
            }

            $options = self::normalize($options);

            $field_name = $options['name'];

            $class = $options['class'] . ' wplb-' . $options['type'] .' ' . $options['name'];

            if (!isset($options['rows'])) {
                $options['rows'] = '5';
            }

            if (!isset($options['cols'])) {
                $options['cols'] = '30';
            }
        
            $desc = !empty($options['desc'])?'<div class="help">' . $options['desc'] . '</div>':'';

            $output = '
                <p class="' . $class . '">
                    <label for="' . $this->get_field_id($field_name) . '">
                        ' . $options['label'] . ':
                    </label>
                    <textarea rows="' . $options['rows'] . '" cols="' . $options['cols'] . '" id="' . $this->get_field_id($field_name) . '" name="' .$this->get_field_name($field_name) . '">' . esc_attr($instance[$field_name]) . '</textarea>
                    '.$desc.'
                </p>';


            return $output;

        }

        /**
         * Media Uploader Using the WordPress Media Library.
         *
         * Parameters:
         *
         * string $_id - A token to identify this field (the name).
         * string $_value - The value of the field, if present.
         * string $_desc - An optional description of the field.
         *
         */

        protected function image($instance, $options) {
        
           if ( function_exists( 'wp_enqueue_media' ) )
                wp_enqueue_media();

            

            $options = self::normalize($options);
            // Gets the unique option name
            $name_field = $options['name'];

            $class = $options['class'] .' wplb-' . $options['type'] ;

            $output = '<div id="'.$options['id'].'" class="section-upload '.$class.'">';

            $value = esc_attr($instance[$name_field]);

            $id =  $this->get_field_id($options['id']) ;

            $desc = !empty($options['desc'])?'<div class="help">' . $options['desc'] . '</div>':'';
          
           
            $output .= '<label for="' . $id . '"> ' . $options['label'] . ': </label>'; 

            $output .= '<input id="' . $id . '" class="src" type="text" name="' . $this->get_field_name($name_field) . '" value="' . $value. '" placeholder="' . __('No file chosen', 'wplb') .'" />' . "\n";
            
            if ( ( $value == '' ) ) {

                $output .= '<div class="upload-button button">Upload</div>';
            } else {
                $output .= '<div class="remove-image button">Remove</div>';
            }
          
            $output .= '<div class="screenshot" id="image-' . $id . '">' . "\n";

            if ( $value != '' ) {
                $image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
                if ( $image ) {
                    $output .= '<img style="max-width:100%" src="' . $value . '" alt="" />' ;
                } 
            }


            if ( $desc != '' ) {
                $output .= '<span class="wplb-metabox-desc">' . $desc . '</span>' . "\n";
            }

            $output .= '</div>';
            $output .= '</div>' . "\n";
            return $output;
        }

        static function normalize( $options )
        {
            $defaults = array(
                'name'  =>'',
                'std'   => '',
                'label' => __('Title','wplb'),
                'class' => 'wplb-input',
                'desc'  => ''
            );

            $defaults = apply_filters('wplb_widgets_default_values', $defaults);

            $options = wp_parse_args( $options, $defaults );

            return $options;

        }
      
        
    }

