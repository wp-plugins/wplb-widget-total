<?php

/**
* Google Maps Widget
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/

class WPLB_Widget_Maps extends WPLB_Widgets
{
  public function __construct()
  {
    
    $widgets_init = array(
        'id_base' => 'wplb-maps-widget',
        'name'    => $this->plugin_name.__(' Google Maps','wplb'),
        'options' => array(
            'classname'   => 'wplb-maps-widget',
            'description' => __( 'Display Google Maps on your sidebar', 'wplb' )
          )
      );
    $zoom_args = array();
    for ($i=1; $i <=20 ; $i++) { 
      $zoom_args[$i] = $i;
    }
    $defaults = array(
        
        'title' => array(
          'label' =>  __('Title', 'wplb'),
          'std'   =>  __('Google Maps', 'wplb'),
          'type'  => 'text',
        ),
        'marker' => array(
          'label'     =>  __('Marker Image', 'wplb'),
          'type'      => 'image'
          
        ),
       
        'latitude' => array(
            'label' => __('Latitude','wplb'),
            'std' => '10.770693',
            'type' => 'text'
          ),
        'longitude' => array(
            'label' => __('Longitude','wplb'),
            'std' => '106.619375',
            'type' => 'text'
          ),

        'width' => array(
            'label' => __('Width','wplb'),
            'type' => 'text',
            'std' => '100%',
            'desc' => __('Example: 100%, 200px. Notice:max-width=100%','wplb'),
            'class' => 'float-left'
          ),

        'height' => array(
            'label' => __('Height','wplb'),
            'std' => '200px',
            'type' => 'text',
            'desc' => __('Example: 200px, 10pc','wplb'),
            'class' => 'float-left'
          ),

        'map_type' => array(
          'label'   => __('Map Type', 'wplb'),
          'type'    => 'select',
          'options' => array(
                        'HYBRID'  => 'HYBRID',
                        'ROADMAP'   => 'ROADMAP',
                        'SATELLITE' => 'SATELLITE',
                        'TERRAIN'    => 'TERRAIN'
                    ),
          'std' => 'ROADMAP',
        ),

        'zoom' => array(
             'label'   => __('Zoom', 'wplb'),
             'type'    => 'select',
             'options' => $zoom_args,
             'std' => '14',
           ),

         'map_zoom_control' => array(
              'label' =>  __('Zoom Control', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>true
          ),

        'map_control' => array(
              'label' =>  __('Show Map Control', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>true
          ),

         'map_scroll' => array(
              'label' =>  __('Scroll Wheel', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>true
         ),

         'map_pan_control' => array(
              'label' =>  __('Pan Control', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>false
          ),

         'map_street_view_control' => array(
              'label' =>  __('Street View Control', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>false
          ),
          'class' => array(
            'label' => __('Class','wplb'),
            'type' => 'text',
          )
        
   
    );
    
    $widgets_init = apply_filters('wplb_widgets_maps_init', $widgets_init);
    $defaults = apply_filters('wplb_widgets_maps_default', $defaults);

    parent::__construct( $widgets_init, $defaults);

  }

  public function frontend_enqueue_scripts() {
    parent::frontend_enqueue_scripts();
    if(!is_admin()){   
       wp_enqueue_script('wplb-gmap-api', 'http://maps.google.com/maps/api/js?sensor=false&amp;language=en', array('jquery'), '1.0', true);
       wp_enqueue_script('wplb-gmap3-js', WPLB_WIDGETS_JS_URL . 'gmap3.js', array('jquery','wplb-gmap-api'), '6.0.0', true);
    }
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
      $html = $args['before_widget'];

      $class = 'wplb-maps-widget ';
      if (!empty($instance['class'])) {
        $class .= $instance['class'] ;
      }
      $html .= '<div class="'.$class.'">';

      if (!empty($instance['title'])) {
        $html .= $before_title;
           $html .= $instance['title'];
        $html .= $after_title;
      }
      
      $target ='';
      

      $map_geo = '';

      if(!empty($instance['latitude']) && !empty($instance['longitude'])){
         $map_geo = $instance['latitude'].','.$instance['longitude'];
      }

      
      if( ! empty( $map_geo ) ) {
         $marker = 'latLng:[' . $map_geo . ']';
         $LatLng = $map_geo;
      } elseif( ! empty( $instance['address'] ) ) {
         $marker = 'address: "' . $instance['address'] . '"';
      }
      if(!empty($instance['marker'])){
         $marker_url = $instance['marker'];
      }else{
         $marker_url = WPLB_WIDGETS_IMG_URL . 'markers/pin.png';
      }
     
      ob_start();
      ?>
      <script type="text/javascript">

          jQuery(document).ready(function($){
           
            $('#map-<?php echo $widget_id; ?>').gmap3({
               // getlatlng:{
               //     address:  "Paris, France",
               //     callback: function(results){
               //       if ( !results ) return;
               //       latLng:results[0].geometry.location
               //     }
               //   },
               map:{
                  // address:"Ho Chi Minh, Viet Nam",
                  options:{
                  center: [<?php echo $map_geo; ?>],
                  mapTypeId: google.maps.MapTypeId.<?php echo $instance['map_type']; ?>,
                  mapTypeControl: <?php echo ($instance['map_control']) ? "true" : "false"; ?>,
                  scrollwheel: <?php echo ($instance['map_scroll']) ? "true" : "false"; ?>,
                  zoomControl: <?php echo ($instance['map_zoom_control']) ? "true" : "false"; ?>,
                  panControl: <?php echo ($instance['map_pan_control']) ? "true" : "false"; ?>,
                  streetViewControl:<?php echo ($instance['map_street_view_control']) ? "true" : "false"; ?>,
                  zoom:<?php echo $instance['zoom']; ?>
                }
              }, // end map
              marker:{
                values:[{<?php echo $marker ?>,options : { icon:new google.maps.MarkerImage("<?php echo $marker_url; ?>") }}],
              }, // end marker
              
               //autofit:{}
            });
          });
      </script>

      <?php  
         $style_arr = array();

         $style_arr['max-width'] = '100%';

         if(!empty($instance['width'])){
            $style_arr['width'] = $instance['width'];
         }else{
            $style_arr['width'] = '100%';
         }

         if(!empty($instance['height'])){
            $style_arr['height'] = $instance['height'];
         }else{
            $style_arr['height'] = '200px';
         }
         
         $style = '';
         if ( count( $style_arr ) > 0 ) {
            $style .= 'style="';
            foreach ( $style_arr as $key => $value ) {
               $style .= $key . ':' . $value . ';';
            }
            $style .= '" ';
         }
      ?>
      <div id="map-<?php echo $widget_id; ?>" <?php echo $style; ?>></div>
      <?php
      $html .= ob_get_clean();
       
      $html .= '</div>';

      // Markup WordPress adds after a widget.
      $html .= $args['after_widget'];

      return apply_filters('wplb-widget-maps', $html, $args, $instance) ;

  }

}
