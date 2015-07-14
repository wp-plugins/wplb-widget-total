<?php
 /*
*  wplb_get_taxonomies_for_select
*
*  @description: 
*/
if(!function_exists('wplb_get_taxonomies_for_select')){
    function wplb_get_taxonomies_for_select( $post_types, $simple_value = false )
    {   
        // vars
        if(!empty($post_types)){
            if(is_string($post_types)){
                $post_types = explode(',', $post_types);
            }
        }else{
            $post_types = get_post_types();
        }

        if($post_types)
        {
            foreach($post_types as $post_type)
            {
                $post_type_object = get_post_type_object($post_type);
                $taxonomies = get_object_taxonomies($post_type);
                if($taxonomies)
                {
                    foreach($taxonomies as $taxonomy)
                    {
                        if(!is_taxonomy_hierarchical($taxonomy)) continue;
                        $terms = get_terms($taxonomy, array('hide_empty' => false));
                        if($terms)
                        {
                            foreach($terms as $term)
                            {
                                $value = $post_type_object->name . ':' . $taxonomy . ':' . $term->term_id;
                                
                                if( $simple_value )
                                {
                                    $value = $taxonomy . ':' . $term->term_id;
                                }
                                
                                $choices[$value] = $term->name; 
                            }
                        }
                    }
                }
            }
        }
        
        return $choices;
    }
}
 /*
*  wplb_get_taxonomies_by_post_type
*
*  @description: 
*/
if(!function_exists('wplb_get_taxonomies_by_post_type')){
    function wplb_get_taxonomies_by_post_type( $post_types = array('post'))
    {   
        if(!empty($post_types))
        {
        	if(is_string($post_types)){
        		$post_types = explode(',', $post_types);
        	}

            foreach($post_types as $post_type)
            {
                $post_type_object = get_post_type_object($post_type);
                $taxonomies = get_object_taxonomies($post_type);
                if($taxonomies)
                {
                    foreach($taxonomies as $taxonomy)
                    {
                        if(!is_taxonomy_hierarchical($taxonomy)) continue;
                        $terms = get_terms($taxonomy, array('hide_empty' => false));
                        if($terms)
                        {
                            foreach($terms as $term)
                            {
                            	$returns[$term->term_id] = $term->name;
                               
                            }
                        }
                    }
                }
            }
        }
        
        return $returns;
    }
}


/*
*  get_post_types
*
*  @description: 
*/
if(!function_exists('wplb_get_post_types')){

    function wplb_get_post_types( $post_types = array(), $exclude = array(), $include = array() )
    {
        // get all custom post types
        $post_types = array_merge($post_types, get_post_types());
        
        
        // core include / exclude
        $wplb_includes = array_merge( array(), $include );
        $wplb_excludes = array_merge( array('revision', 'nav_menu_item' ), $exclude );
     
        
        // include
        foreach( $wplb_includes as $p )
        {                   
            if( post_type_exists($p) )
            {                           
                $post_types[ $p ] = $p;
            }
        }
        
        
        // exclude
        foreach( $wplb_excludes as $p )
        {
            unset( $post_types[ $p ] );
        }
        
        
        return $post_types;
        
    }
}
/**
 * Return an image pulled from the media gallery.
 *
 * Supported $args keys are:
 *
 *  - format   - string, default is 'html'
 *  - size     - string, default is 'full'
 *  - num      - integer, default is 0
 *  - attr     - string, default is ''
 *  - fallback - mixed, default is 'first-attached'
 *
 * Applies `wplb_get_image_default_args`, `wplb_pre_get_image` and `wplb_get_image` filters.
 *
 * @since 0.1.0
 *
 * @uses wplb_get_image_id() Pull an attachment ID from a post, if one exists.
 *
 * @param array|string $args Optional. Image query arguments. Default is empty array.
 *
 * @return string|boolean Return image element HTML, URL of image, or false.
 */
if(!function_exists('wplb_get_image')){

    function wplb_get_image( $args = array() ) {

        $defaults = array(
            'post_id'  => null,
            'format'   => 'html',
            'size'     => 'full',
            'num'      => 0,
            'attr'     => '',
            'fallback' => 'first-attached',
            'context'  => '',
        );

        /**
         * A filter on the default parameters used by `wplb_get_image()`.
         *
         * @since unknown
         */
        $defaults = apply_filters( 'wplb_get_image_default_args', $defaults, $args );

        $args = wp_parse_args( $args, $defaults );

        //* Allow child theme to short-circuit this function
        $pre = apply_filters( 'wplb_pre_get_image', false, $args, get_post() );
        if ( false !== $pre )
            return $pre;

        //* Check for post image (native WP)
        if ( has_post_thumbnail( $args['post_id'] ) && ( 0 === $args['num'] ) ) {
            $id = get_post_thumbnail_id( $args['post_id'] );
            $html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
            list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
        }
        //* Else if first-attached, pull the first (default) image attachment
        elseif ( 'first-attached' === $args['fallback'] ) {
            $id = wplb_get_image_id( $args['num'], $args['post_id'] );
            $html = wp_get_attachment_image( $id, $args['size'], false, $args['attr'] );
            list( $url ) = wp_get_attachment_image_src( $id, $args['size'], false, $args['attr'] );
        }
        //* Else if fallback array exists
        elseif ( is_array( $args['fallback'] ) ) {
            $id   = 0;
            $html = $args['fallback']['html'];
            $url  = $args['fallback']['url'];
        }
        //* Else, return false (no image)
        else {
            return false;
        }

        //* Source path, relative to the root
        $src = str_replace( home_url(), '', $url );

        //* Determine output
        if ( 'html' === mb_strtolower( $args['format'] ) )
            $output = $html;
        elseif ( 'url' === mb_strtolower( $args['format'] ) )
            $output = $url;
        else
            $output = $src;

        //* Return false if $url is blank
        if ( empty( $url ) ) $output = false;

        //* Return data, filtered
        return apply_filters( 'wplb_get_image', $output, $args, $id, $html, $url, $src );
    }
}
/**
 * Pull an attachment ID from a post, if one exists.
 *
 * @since 0.1.0
 *
 * @param integer $index Optional. Index of which image to return from a post. Default is 0.
 *
 * @return integer|boolean Returns image ID, or false if image with given index does not exist.
 */
if(!function_exists('wplb_get_image_id')){

    function wplb_get_image_id( $index = 0, $post_id = null ) {

        $image_ids = array_keys(
            get_children(
                array(
                    'post_parent'    => $post_id ? $post_id : get_the_ID(),
                    'post_type'      => 'attachment',
                    'post_mime_type' => 'image',
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                )
            )
        );

        if ( isset( $image_ids[ $index ] ) ) {
            return $image_ids[ $index ];
        }

        return false;

    }
}
/*
*  wplb_get_image_sizes
*
*  @description: returns an array holding all the image sizes
*/
if(!function_exists('wplb_get_image_sizes')){
    
    function wplb_get_image_sizes( $sizes = array())
    {
        // find all sizes
        $all_sizes = get_intermediate_image_sizes();
        
        
        // define default sizes
        $sizes = array_merge($sizes, array(
            'thumbnail' =>  __("Thumbnail",'wplb'),
            'medium'    =>  __("Medium",'wplb'),
            'large'     =>  __("Large",'wplb'),
            'full'      =>  __("Full",'wplb')
        ));
        
        
        // add extra registered sizes
        foreach( $all_sizes as $size )
        {
            if( !isset($sizes[ $size ]) )
            {
                $sizes[ $size ] = ucwords( str_replace('-', ' ', $size) );
            }
        }
        
        
        // return array
        return $sizes;
    }
}
/**
 * Get template part 
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */

if(!function_exists('wplb_get_template_part')){

    function wplb_get_template_part($slug, $name = '') {
        if(empty($plugin_path)){
            $plugin_path = dirname( __FILE__ ).'/';
        }
        $template = '';

        // Get default slug-name.php
        if ($name && file_exists( $plugin_path . "templates/{$slug}-{$name}.php" ) ) {
            $template = $plugin_path . "templates/{$slug}-{$name}.php";
        }

        // Get default slug.php
        if ( ! $template) {
            $template = $plugin_path . "templates/{$slug}.php";
        }
      
        if ( $template ) {
            load_template( $template, false );
        }
    }
}


if(!function_exists('wplb_column_class')){
    function wplb_column_class( $i ) {
        switch ( $i ) {
            case 1:
                return '';
            case 2:
                return 'one-half';
            case 3:
                return 'one-third';
            case 4:
                return 'one-fourth';
            case 5:
                return 'one-fifth';
            case 6:
                return 'one-sixth';
            default:
                return '';
        }
    }
}

/**
 * Return content stripped down and limited content.
 *
 * Strips out tags and shortcodes, limits the output to `$max_char` characters, and appends an ellipsis and more link to the end.
 *
 * @since 0.1.0
 *
 * @param integer $max_characters The maximum number of characters to return.
 * @param string  $more_link_text Optional. Text of the more link. Default is "(more...)".
 * @param bool    $stripteaser    Optional. Strip teaser content before the more text. Default is false.
 *
 * @return string Limited content.
 */
if(!function_exists('wplb_get_the_content_limit')){
    function wplb_get_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

        $content = get_the_content( '', $stripteaser );

        //* Strip tags and shortcodes so the content truncation count is done correctly
        $content = strip_tags( strip_shortcodes( $content ), apply_filters( 'wplb_get_the_content_limit_allowedtags', '<script>,<style>' ) );

        //* Remove inline styles / scripts
        $content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

        //* Truncate $content to $max_char
        $content = wplb_truncate_phrase( $content, $max_characters );

        //* More link?
        if ( $more_link_text ) {
            $link   = apply_filters( 'get_the_content_more_link', sprintf( '&#x02026; <a href="%s" class="more-link">%s</a>', get_permalink(), $more_link_text ), $more_link_text );
            $output = sprintf( '<p>%s %s</p>', $content, $link );
        } else {
            $output = sprintf( '<p>%s</p>', $content );
            $link = '';
        }

        return apply_filters( 'wplb_get_the_content_limit', $output, $content, $link, $max_characters );

    }
}

/**
 * Echo the limited content.
 *
 * @since 0.1.0
 *
 * @uses wplb_get_the_content_limit() Return content stripped down and limited content.
 *
 * @param integer $max_characters The maximum number of characters to return.
 * @param string  $more_link_text Optional. Text of the more link. Default is "(more...)".
 * @param bool    $stripteaser    Optional. Strip teaser content before the more text. Default is false.
 */
if(!function_exists('wplb_the_content_limit')){
    function wplb_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

        $content = wplb_get_the_content_limit( $max_characters, $more_link_text, $stripteaser );
        echo apply_filters( 'wplb_the_content_limit', $content );

    }
}

/**
 * Return a phrase shortened in length to a maximum number of characters.
 *
 * Result will be truncated at the last white space in the original string. In this function the word separator is a
 * single space. Other white space characters (like newlines and tabs) are ignored.
 *
 * If the first `$max_characters` of the string does not contain a space character, an empty string will be returned.
 *
 * @since 1.4.0
 *
 * @param string $text            A string to be shortened.
 * @param integer $max_characters The maximum number of characters to return.
 *
 * @return string Truncated string
 */
if(!function_exists('wplb_truncate_phrase')){
    function wplb_truncate_phrase( $text, $max_characters ) {

        $text = trim( $text );

        if ( mb_strlen( $text ) > $max_characters ) {
            //* Truncate $text to $max_characters + 1
            $text = mb_substr( $text, 0, $max_characters + 1 );

            //* Truncate to the last space in the truncated string
            $text = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );
        }

        return $text;
    }
}

/*-----------------------------------------------------------------------------------*/
# Get the post time
/*-----------------------------------------------------------------------------------*/
if(!function_exists('wplb_get_time')){
    function wplb_get_time($format='modern'){
        global $post ;
        if( $format == 'modern' ){ 
            $to = current_time('timestamp'); //time();
            $from = get_the_time('U') ;
            
            $diff = (int) abs($to - $from);
            if ($diff <= 3600) {
                $mins = round($diff / 60);
                if ($mins <= 1) {
                    $mins = 1;
                }
                $since = sprintf(_n('%s min', '%s mins', $mins), $mins) .' '. __( 'ago' , 'tie' );
            }
            else if (($diff <= 86400) && ($diff > 3600)) {
                $hours = round($diff / 3600);
                if ($hours <= 1) {
                    $hours = 1;
                }
                $since = sprintf(_n('%s hour', '%s hours', $hours), $hours) .' '. __( 'ago' , 'tie' );
            }
            elseif ($diff >= 86400) {
                $days = round($diff / 86400);
                if ($days <= 1) {
                    $days = 1;
                    $since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
                }
                elseif( $days > 29){
                    $since = get_the_time(get_option('date_format'));
                }
                else{
                    $since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
                }
            }
        }else{
            $since = get_the_time(get_option('date_format'));
        }
        return '<span>'.$since.'</span>';
    }
}