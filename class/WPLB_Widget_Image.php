<?php
/**
* WPLB Image 
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/

class WPLB_Widget_Image extends WPLB_Widgets
{
  public function __construct()
  {
    $widgets_init = array(
      'id_base' => 'wplb-image-widget',
      'name'    => $this->plugin_name.__(' Image Upload','wplb'),
      'options' => array(
          'classname'   => 'wplb-image-widget',
          'description' => __( 'Display image with a title, url, caption ...', 'wplb' )
        )
    );

    $defaults = array(
        
        'title' => array(
          'label' =>  __('Title', 'wplb'),
          'std'   =>  __('Image Widget', 'wplb'),
          'type'  => 'text',
        ),
        'src' => array(
          'label'     =>  __('Image', 'wplb'),
          'type'      => 'image'
          
        ),
        'link' => array(
          'label' =>  __('Link', 'wplb'),
          'type'  => 'text'
        ),
        'fill' => array(
          'label'     =>  __('Fill Width', 'wplb'),
          'type'      => 'checkbox'
          
        ),
        'target' => array(
          'label'   => __('Target', 'wplb'),
          'type'    => 'select',
          'options' => array(
                        '_blank'  => '_blank',
                        '_self'   => '_self',
                        '_parent' => '_parent',
                        '_top'    => '_top'
                    ),
          'std' => '_parent',

        ),
        
        'align' => array(
          'label'   => __('Align', 'wplb'),
          'type'    => 'select',
          'options' => array(
                        'none'  => 'None',
                        'left'   => 'Left',
                        'center' => 'Center',
                        'right'    => 'Right'
                    ),
          'std' => 'center',

        ),
        'alt' => array(
          'label' =>  __('Alternate text', 'wplb'),
          'type'  => 'text',
        ),
        'caption' => array(
          'label' =>  __('Caption', 'wplb'),
          'type'  => 'textarea',
        ),
        'class' => array(
          'label' =>  __('Class', 'wplb'),
          'type'  => 'text',
        )
        
    );

    $widgets_init = apply_filters('wplb_widgets_image_init', $widgets_init);
    $defaults = apply_filters('wplb_widgets_image_default', $defaults);

    parent::__construct( $widgets_init, $defaults);

  }



  /**
   * Format the output for this widget.
   *
   * @return string
   * @author Huu Ha
   */

  public function output($args, $data)
  {

      if (empty($data['src'])) {
          // Return nothing if an image hasn't been selected.
          return ;
      }else{
          $image_url = $data['src'];
      }

      // Markup WordPress adds before a widget.
      $html = $args['before_widget'];

       $class = 'wplb-container wplb-images-widget ';
       if (!empty($instance['class'])) {
         $class .= $instance['class'] ;
       }
       $html .= '<div class="'.$class.'">';

          if (!empty($data['title'])) {
              $html .= $args['before_title'];
                  $html .= $data['title'];
              $html .= $args['after_title'];
          }

          $html .= '<div class="wplb-box-content">';
              
              $img = '<img src="' . $data['src'] . '" ';

              // Alt for image.
              if ( $data['alt'] != '' ) {
                $img .= 'alt="' . esc_attr( $data['alt'] ) . '" title="' .  esc_attr( $data['alt'] ) . '" ';
              }

              //Class
              $class = 'wplb-image';
              // $class .= ' attachment-'.$data['size'];
              $class .= " align{$data['align']}";

              $img .= ' class="'.$class.'"';

              // Compile the style param.
              $style = array();

              $style['max-width'] = '100%';

              if ($data['fill'] == true) {
                $style['height'] = 'auto';
                $style['width']  = '100%';
              }
             
              if ( count( $style ) > 0 ) {
                $img .= 'style="';
                foreach ( $style as $key => $value ) {
                  $img .= $key . ':' . $value . ';';
                }
                $img .= '" ';
              }

              $img .= '>';
              
              // Linked?
              if ( $data['link'] != '' ) {
                $a = '<a href="' . esc_attr( $data['link'] ) . '"';
                $a .= ' target="' . esc_attr( $data['target'] ) . '"';
                $a .= ' alt="' . esc_attr( $data['alt'] ) . '"';
                $a .= ' title="' . esc_attr( $data['alt'] ) . '"';
                $a .= '>';
                $img = $a . $img . '</a>';
              }

              //Caption
              if($data['caption']){
                $img .= '<p>'.esc_attr($data['caption']).'</p>';
              }
              $html .= $img;

          $html .= '</div>';

      $html .= '</div>';

      // Markup WordPress adds after a widget.
      $html .= $args['after_widget'];

      return apply_filters('wplb-widget-image', $html, $args, $data) ;

  }

}
