<?php
/**
* WPLB Video Widget
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/
class WPLB_Widget_Video extends WPLB_Widgets
{
  public $fields;
  public $defaults;

  public function __construct()
  {
    $widgets_init = array(
        'id_base' => 'wplb-video-widget',
        'name'    => $this->plugin_name.__(' Video','wplb'),
        'options' => array(
            'classname'   => 'wplb-video-widget',
            'description' => __( 'Displays Vimeo or Youtobe Video by ID or Embed Code ', 'wplb' )
          )
      );

      // Defaults
      $defaults = array(
          'title'                   => array(
              'label' =>  __('Title', 'wplb'),
              'std'   =>  __('Find Us On Facebook', 'wplb'),
              'type'  => 'text',
            ),
          'embed_code'               => array(
            'label' => __('Embed Code', 'wplb'),
            'type'  => 'textarea',
            'desc' => '',
            ),
          'youtobe_id'               => array(
              'label' => __('Youtobe ID', 'wplb'),
              'type'  => 'text',
              'std'   => '_F9qn54SHW4',
              'desc'   =>  __('if video url : http://www.youtube.com/watch?v=_F9qn54SHW4  Enter above <strong>_F9qn54SHW4</strong>', 'wplb'),
            ),
          'vimeo_id'               => array(
              'label' => __('Vimeo ID', 'wplb'),
              'type'  => 'text',
              'desc'   =>  __('if video url :https://vimeo.com/113525882  Enter above <strong>113525882</strong>', 'wplb'),
            ),
          'class'                   => array(
              'label' =>  __('Class', 'wplb'),
              'type'  => 'text',
            ),
        );
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

  public function output($args, $instance)
  {
    extract( $args );
    $html = $before_widget;

    $class = 'wplb-widget-video ';
    if (!empty($instance['class'])) {
     $class .= $instance['class'] ;
    }
    $html .= '<div class="'.$class.'">';

    if (!empty($instance['title'])) {
      $html .= $before_title;
        $html .= $instance['title'];
      $html .= $after_title;
    }


    if( !empty($instance['embed_code']) ){
      $embed_code = $instance['embed_code'];
      $width = 'width="100%"';
      $height = 'height="210"';
      $embed_code = preg_replace('/width="([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width,$embed_code);
      $embed_code = preg_replace( '/height="([0-9]*)"/' , $height , $embed_code );
        
      $width1 = 'width: 100%';
      $height1 = 'height: 210';
      $embed_code = preg_replace('/width:"([3-9][0-9]{2,}|[1-9][0-9]{3,})"/',$width1,$embed_code);
      $embed_code = preg_replace( '/height: ([0-9]*)/' , $height1 , $embed_code );  
    }

    if ( !empty( $embed_code ) ):
      $html .= $embed_code;

    elseif(!empty($instance['youtobe_id'])):
      $html .= '<iframe width="100%" height="210" src="http://www.youtube.com/embed/'.$instance['youtobe_id'].'?rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';

    elseif(!empty($instance['vimeo_id'])):
      $html .= '<iframe src="http://player.vimeo.com/video/'.$instance['vimeo_id'].'?wmode=opaque" width="100%" height="210" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

    endif ;

    $html .= $after_widget;

    return apply_filters('wplb-widget-video', $html, $args, $instance) ;
  }

}
