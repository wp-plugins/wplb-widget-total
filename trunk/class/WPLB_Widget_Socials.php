<?php

/**
* Socials Link Widget
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/

class WPLB_Widget_Socials extends WPLB_Widgets
{
  public function __construct()
  {
    
    $widgets_init = array(
        'id_base' => 'wplb-socials-widget',
        'name'    => $this->plugin_name.__(' Socials Link','wplb'),
        'options' => array(
            'classname'   => 'wplb-socials-widget',
            'description' => __( 'Display Socials Link', 'wplb' )
          )
      );

    $defaults = array(
        
        'title' => array(
          'label' =>  __('Title', 'wplb'),
          'std'   =>  __('Socials', 'wplb'),
          'type'  => 'text',
        ),
        'fb_url' => array(
            'label' => __('Facebook url','wplb'),
            'type' => 'text',
            'std' => 'http://facebook.com',
          ),
        'google_url' => array(
            'label' => __('Youtube url','wplb'),
            'std' => 'http://youtube.com',
            'type' => 'text'
          ),
        'tw_url' => array(
            'label' => __('Twitter url','wplb'),
            'std' => 'http://twitter.com',
            'type' => 'text'
          ),
        'pinterest_url' => array(
              'label' => __('Pinterest','wplb'),
              'type' => 'text',
              'std' => 'http://pinterest.com'
          ),
        'size' => array(
          'label'   => __('Size', 'wplb'),
          'type'    => 'select',
          'options' => array(
                        '32'  => '32px',
                        '16'   => '16px',
                    ),
          'std' => '32',
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
        'class' => array(
          'label'=>__('Class', 'wplb'),
          'type'=> 'text'
        )
   
    );
    
    $widgets_init = apply_filters('wplb_widgets_socials_init', $widgets_init);
    $defaults = apply_filters('wplb_widgets_socials_default', $defaults);

    parent::__construct( $widgets_init, $defaults);

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

      $class = 'wplb-socials-widget ';
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

         if (!empty($instance['target'])) {
                $target = 'target="'.$instance['target'].'"';
        }

        // Compile the style param.
        $style_arr = array();
        $ext = '';
        if($instance['size']=='16'){
            $ext = '_16';
        }
          $style_arr['height'] = $instance['size'].'px';
          $style_arr['width']  = $instance['size'].'px';
        $style = '';

       if ( count( $style_arr ) > 0 ) {
          $style .= 'style="';
          foreach ( $style_arr as $key => $value ) {
            $style .= $key . ':' . $value . ';';
          }
          $style .= '" ';
        }

        // Facebook
        if ( !empty($instance['fb_url']) && $instance['fb_url'] != ' ' ) {
          $html .= '<a  title="Facebook" href="'.$instance['fb_url'].'" '.$target.'><i class="socials-icon icon-facebook"></i><img  '.$style.'  src="'.WPLB_WIDGETS_IMG_URL.'facebook'.$ext.'.png" ></a>';
        }
        //  // YouTube
        if ( !empty($instance['google_url']) && $instance['google_url'] != ' ' ) {
          $html .= '<a  title="Youtube" href="'.$instance['google_url'].'" '.$target.'><i class="socials-icon icon-youtube"></i><img   '.$style.' src="'.WPLB_WIDGETS_IMG_URL.'google_plus'.$ext.'.png" ></a>';
        }
        // Twitter
        if ( !empty($instance['tw_url']) && $instance['tw_url'] != ' ') {
          $html .= '<a  title="Twitter" href="'.$instance['tw_url'].'" '.$target.'><i class="socials-icon icon-twitter"></i><img   '.$style.' src="'.WPLB_WIDGETS_IMG_URL.'tweets'.$ext.'.png"></a>';
        } 

        // Pinterest
        if ( !empty($instance['pinterest_url']) && $instance['pinterest_url'] != ' ') {
          $html .= '<a  title="Pinterest" href="'.$instance['pinterest_url'].'" '.$target.'><i class="socials-icon icon-twitter"></i><img   '.$style.' src="'.WPLB_WIDGETS_IMG_URL.'pinterest'.$ext.'.png"></a>';
        } 
       
      $html .= '</div>';

      // Markup WordPress adds after a widget.
      $html .= $args['after_widget'];

      return apply_filters('wplb-widget-socials', $html, $args, $instance) ;

  }

}
