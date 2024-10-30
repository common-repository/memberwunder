<?php
  namespace MemberWunder\Helpers;

  class General
  {
    /**
     * load by path
     * if class and function not empty, call function
     * 
     * @param  string  $path
     * @param  string  $class
     * @param  string  $function
     * @param  string  $full_path
     * @param  boolean $one_time
     * @param  array   $variables
     *
     * @since  1.0.21.3
     * 
     */
    public static function load( $path, $class = '', $function = '', $full_path = false, $one_time = true, $variables = array() )
    {
        if( !empty( $variables ) )
            extract( $variables );

        $path = ( $full_path ? $path : TWM_PATH . DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR . $path ).'.php';
        if( $one_time )
            require_once( $path );
        else
            require( $path );

        if( !empty( $function ) && !empty( $class ) )
            call_user_func( array( $class, $function ) );
    }

    /**
     * load by path and custom action
     * 
     * @param  string $path
     * @param  string $class
     * @param  string $function
     * @param  string $action
     * @param  string $priority
     * @param  string $args
     *
     * @since 1.0.21.5
     * 
     */
    public static function load_by_action( $path, $class, $function, $action, $priority = 10, $args = 2 )
    {
        self::load( $path );
        add_action( $action, array( $class, $function ), $priority, $args );
    }

    /**
    * get meta prefix
    * 
    * @return string
    *
    * @since  1.0.34
    * 
    */
    public static function get_meta_prefix()
    {
        $a = new \MemberWunder\Application();
        $prefix = $a->get_meta_prefix();

        unset( $a );

        return $prefix;
    }

    /**
     * get upload path to MW plugin
     * 
     * @return string
     *
     * @since  1.0.34
     * 
     */
    public static function get_upload_path()
    {
        $folder = wp_upload_dir();

        return $folder['basedir'].'/memberwunder/';
    }

    /**
     * check is element directory
     *
     * @param  string $element
     * @param  string $path
     * 
     * @return boolean
     *
     * @since  1.0.34
     * 
     */
    public static function is_dir( $element, $path )
    {
        return is_dir( $path.$element ) && $element != '.' && $element != '..';
    }

    /**
     * Check access to write in WP upload
     * 
     * @return boolean
     *
     * @since  1.0.34
     * 
     */
    public static function is_upload_enable_to_write()
    {
        $folder = wp_upload_dir();
        
        return wp_is_writable( $folder['basedir'] ) && wp_is_writable( $folder['path'] );
    }

    /**
     * Check if a string is serialized
     * 
     * @param  string  $string
     * 
     * @return boolean        
     *
     * @since  1.0.34
     * 
     */
    public static function is_serialized( $string )
    {
        $result = @unserialize( $string );

        return ( $string === 'b:0;' || $result !== false ) ? TRUE : FALSE;
    }

    /**
     * Move file from path to WordPress media (upload folder)
     * 
     * @param  string $image_path 
     * 
     * @return string
     *
     * @since  1.0.34
     * 
     */
    public static function upload_image_to_media( $image_path )
    {
        $image_data = file_get_contents( $image_path );
        $filename = basename( $image_path );

        $uploaded_file = wp_upload_bits( $filename, NULL, $image_data );

        if( $uploaded_file['error'] )
          return '';

        $wp_filetype = wp_check_filetype( $uploaded_file['file'], null );

        $attachment = array(
                              'post_mime_type'  => $wp_filetype['type'],
                              'guid'            => $uploaded_file['url'],
                              'post_title'      => sanitize_file_name( $filename ),
                              'post_content'    => '',
                              'post_status'     => 'inherit'
                            );

        $attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

    /**
     * remove directory and all files in it
     * 
     * @param  string $path
     * 
     * @since 1.0.34
     * 
     */
    public static function remove_folder( $path )
    {
        if( !is_dir( $path ) )
            return;

        $path_handle = opendir( $path );
        if( !$path_handle )
            return;

        while( $file = readdir( $path_handle ) ) 
        {
            if( $file == "." || $file == ".." )
                continue; 

            $sub_path = $path."/".$file;
            if( !is_dir( $sub_path ) )
                unlink( $sub_path );
            else
                self::remove_folder( $sub_path );
            
            unset( $sub_path );
        }

        closedir( $path_handle );
        rmdir( $path );
    }

    /**
     * generate admin ajax link
     * 
     * @param  array $args 
     * 
     * @return string
     *
     * @since  1.0.34
     * 
     */
    public static function admin_ajax_link( $args = array() )
    {
        $args = http_build_query( $args );
        return admin_url( 'admin-ajax.php'.( !empty( $args ) ? '?'.$args : '' ) );
    }

    /**
     * set ajax response
     * 
     * @param  string $type
     * @param  string $message
     * 
     * @return string
     *
     * @since  1.0.36
     * 
     */
    public static function ajax_response( $type, $message )  
    {
        echo json_encode( array( 'status' => $type, 'message' => $message ) );
        exit();
    }

    /**
     * check page for custom posy type
     * 
     * @param  array  $types
     * @param  string  $hook
     * 
     * @return boolean 
     *
     * @since 1.0.0.0
     * 
     */
    public static function is_post_type( $types, $hook )
    {
        $id = '';
        if( $hook == 'post.php' )
            if( isset( $_GET['post'] ) )
                $id = (int)$_GET['post'];
            elseif( isset( $_POST['post_ID'] ) )
                $id = (int)$_POST['post_ID'];
        
      return ( $hook == 'post-new.php' && isset($_GET['post_type']) && in_array( $_GET['post_type'], $types ) ) || ( $hook == 'post.php' && in_array( get_post( $id )->post_type, $types ) )  || ( $hook == 'edit.php' && isset( $_REQUEST['post_type'] ) && in_array( $_REQUEST['post_type'], $types ) );
    }
  }