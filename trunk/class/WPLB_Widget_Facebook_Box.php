<?php
/**
* WPLB Facebook Box Widget
*
* @author Huu Ha <huuhath@gmail.com>
* @link http://www.wplabels.com
*/

class WPLB_Widget_Facebook_Box extends WPLB_Widgets
{
  public $fields;
  public $defaults;

  public function __construct()
  {
    $widgets_init = array(
        'id_base' => 'wplb-fb-box-widget',
        'name'    => $this->plugin_name.__('Facebook Like Box','wplb'),
        'options' => array(
            'classname'   => 'wplb-fb-box-widget',
            'description' => __( 'Displays Facebook Page owners to attract and gain Likes from their own website', 'wplb' )
          )
      );

      // Defaults
      $defaults = array(
          'title'                   => array(
              'label' =>  __('Title', 'wplb'),
              'std'   =>  __('Find us on Facebook', 'wplb'),
              'type'  => 'text',
              'desc'  => __('If an item is left blank it will not be output.', 'wplb')
            ),
          'fb_url'   => array(
              'label' => __('Page Url', 'wplb'),
              'type'  => 'text',
              'std'   => 'https://www.facebook.com/RureShopping'
            ),
          'width'  => array(
              'label' => __('Width: ', 'wplb'),
              'type'  => 'text',
              'std'  => '340',
              'class'  => 'float-left',
              'desc'   =>  __('Only input number', 'wplb'),
            ),
          'height'  => array(
              'label' => __('Height: ', 'wplb'),
              'type'  => 'text',
              'std'  => '500',
              'class'  => 'float-left',
              'desc'   =>  __('Only input number', 'wplb'),
            ),
          'adapt_container_width' => array(
              'label' =>  __('Adapt to plugin container width', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>true,
              'desc'  => __('Try to fit inside the container width', 'wplb')
          ),
          'small_header' => array(
              'label' =>  __('Use Small Header', 'wplb'),
              'type'  => 'checkbox',
              'std'   =>false
          ),
          'hide_cover' => array(
              'label' =>  __('Hide Cover Photo', 'wplb'),
              'type'  => 'checkbox',
              'std'   => false,
          ),
          'show_friends' => array(
              'label' =>  __('Show Friend`s Faces', 'wplb'),
              'type'  => 'checkbox',
              'std'   => true
          ),
          'show_posts' => array(
              'label' =>  __('Show posts from the Page`s timeline', 'wplb'),
              'type'  => 'checkbox',
              'std'   => true
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

    $class = 'wplb-widget-fb-like-box ';
    if (!empty($instance['class'])) {
     $class .= $instance['class'] ;
    }
    $html .= '<div class="'.$class.'">';

    if (!empty($instance['title'])) {
      $html .= $before_title;
        $html .= $instance['title'];
      $html .= $after_title;
    }

    if(!empty($instance['fb_url']) && strlen($instance['fb_url']) > 30)
      $pageURL = $instance['fb_url'];
    else
      $pageURL = 'https://www.facebook.com/RureShopping';

    $width        = empty($instance['width']) ? '292' : $instance['width'];
    $height       = empty($instance['height']) ? '255' : $instance['height'];
    
    $streams      = ($instance['show_posts']) ? 'true' : 'false';
    $header       = ($instance['small_header']) ? 'true' : 'false';
    $show_friends = ($instance['show_friends']) ? 'true' : 'false';
    $hide_cover = ($instance['hide_cover']) ? 'true' : 'false';
    
    $html  .= "<div id=\"fb-root\"></div> \n";
    $html .= "<script>(function(d, s, id) {  \n";
    $html .= " var js, fjs = d.getElementsByTagName(s)[0]; \n";
    $html .= "  if (d.getElementById(id)) return; \n";
    $html .= "  js = d.createElement(s); js.id = id; \n";
    $html .= "  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3\"; \n";
    $html .= "  fjs.parentNode.insertBefore(js, fjs); \n";
    $html .= "}(document, 'script', 'facebook-jssdk'));</script> \n";
    $html .= "<div class=\"fb-page\" data-href=\"$pageURL\" data-width=\"$width\" data-height=\"$height\" data-hide-cover=\"$hide_cover\" data-show-facepile=\"$show_friends\" data-small-header=\"$header\" data-show-posts=\"$streams\"><div class=\"fb-xfbml-parse-ignore\"><blockquote cite=\"$pageURL\"><a href=\"$pageURL\">Facebook</a></blockquote></div></div> \n";
   
    $html .= '</div>';

    $html .= $after_widget;

    return apply_filters('wplb-widget-video', $html, $args, $instance) ;
  }

}
