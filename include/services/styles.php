<?php
  namespace MemberWunder\Services;

  class Styles
  { 
    /**
     * path to less folder
     * 
     * @var string
     *
     * @since  1.0.28.10
     * 
     */
    static $path_to_less = 'templates/public/less/';

    /**
     * generate css from less
     * 
     * @param  string $file name of less file
     * @param  string $variables for loading to less
     * 
     * @return string
     *
     * @since  1.0.28.10
     * 
     */
    public static function css_from_less( $file, $variables = array() )
    {
      \MemberWunder\Helpers\General::load( 'libraries/lessc.inc' );
      $less = new \MemberWunder\Libraries\lessc();

      if( !empty( $variables ) )
        $less->setVariables( $variables );

      $less->setFormatter("compressed");

      $css = $less->compileFile( self::get_path_to_less( $file ) );

      return $css;
    }

    /**
     * get full path to less
     * 
     * @param  string $file
     * 
     * @return string
     *
     * @since  1.0.36.1
     * 
     */
    public static function get_path_to_less( $file )
    {
      return TWM_PATH.'/'.self::$path_to_less.$file.'.less';
    }
  }