<?php
  namespace MemberWunder\Helpers;

  class View
  {
    /**
     * generate full path to template file
     * 
     * @param  string $path
     * 
     * @return string
     *
     * @since  1.0.21.3
     * 
     */
    public static function get_full_template_path( $path )
    {
        return TWM_PATH . DIRECTORY_SEPARATOR . 'templates/' . $path . '.php'; 
    }

    /**
     * generate url for frontend dashboard
     * 
     * @param  string $path 
     * 
     * @return string
     *
     * @since  1.0.21.3
     * 
     */
    public static function get_frontend_dashboard_url( $path = '' )
    {
        return ( get_option('permalink_structure') ? get_bloginfo( 'url' ).twmshp_get_option( 'baseurl' ) : get_bloginfo( 'url' ).'?p='.trim( twmshp_get_option( 'baseurl' ), '/' ) ) . $path;
    }

    /**
     * get template part
     * 
     * @param  string  $path 
     * @param  array   $variables
     * @param  boolean $dashboard
     * 
     * @since  1.0.21.5
     * 
     */
    public static function get_template_part( $path, $variables = array(), $dashboard = false )
    {
        \MemberWunder\Helpers\General::load( TWM_PATH.'/assets/'.( $dashboard ? 'dashboard' : 'frontend' ).'/views/template/'.$path, '', '', true, false, $variables );
    }

    /**
     * get image(html tag) for system image
     *
     * @param  string $path
     * @param  array  $variables
     * 
     * @param  boolean $is_dashboard
     * @param  boolean $has_full_path 
     * @param  string $alt
     * @param  string $ext
     * @param  boolean $only_path
     *
     * @return string
     *
     * @since 1.0.36
     *
     */
    public static function system_image( $path, $variables = array() )
    {
        extract( shortcode_atts( 
                                    array(
                                        'is_dashboard'      => false,
                                        'attr'              => array( 'alt' => '' ),
                                        'ext'               =>  'png',
                                        'only_path'         =>  false,
                                        'has_full_path'     =>  false
                                        ), 
                                    $variables 
                                )
                );

        $string = '';
        foreach( $attr as $key => $value )
            $string .= ' '.$key.'="'.$value.'"';

        $path = $has_full_path ? $path : TWM_ASSETS_URL.( $is_dashboard ? '/dashboard' : '/frontend' ).'/images/'.$path.'.'.$ext;

        return $only_path ? $path : '<img src="'.$path.'" '.$string.'/>';
    }
  }
