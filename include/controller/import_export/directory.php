<?php
  namespace MemberWunder\Controller\ImportExport;

  class Directory
  {
    private static $_instance = NULL;

    /**
     * list of folders 
     * 
     * @var array
     *
     * @since  1.0.34
     * 
     */
    protected static $folders = array();

    /**
     * base path 
     * 
     * @var string
     *
     * @since  1.0.34
     * 
     */
    protected static $base_path = '';

    private function __construct( $folder = '', $path = '' )
    {
      self::$base_path = rtrim( $path, '/' );

      if( !empty( $folder ) )
        self::level_up( rtrim( $folder, '/' ) );
    }

    /**
     * get current folder
     * 
     * @param string  $sub_path
     * @param boolean $ltrim remove left slash
     *
     * @since  1.0.34
     * 
     */
    public static function get_current( $sub_path = '', $ltrim = false )
    {
      $path = rtrim( self::$base_path.'/'.implode( '/', self::$folders ), '/' ).'/'.$sub_path;
      return $ltrim ? ltrim( $path, '/' ) : $path;
    }

    /**
     * add folder to folders
     * 
     * @param string $folder
     *
     * @since  1.0.34
     * 
     */
    public static function level_up( $folder )
    {
      self::$folders[] = $folder;
    }

    /**
     * remove folder from folders
     *
     * @param boolean $remove
     * 
     * @since  1.0.34
     * 
     */
    public static function level_down( $remove = false )
    {
      if( $remove )
        \MemberWunder\Helpers\General::remove_folder( self::get_current() );

      unset( self::$folders[ sizeof( self::$folders ) - 1 ] );

      self::$folders = array_values( self::$folders );
    }

    private function __clone() {}

    public static function _instance( $folder = '', $path = '' )
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self( $folder, $path );

      return self::$_instance;
    }

    public static function _destroy()
    {
        self::$_instance = NULL;
    }
  }