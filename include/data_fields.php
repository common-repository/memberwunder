<?php
  namespace MemberWunder;

  class DataFields
  {
    /**
     * return data fields by post type
     *     key            key of field
     *     type           type of field 
     *                       default system
     *                       system         default field of WP_Post
     *                       meta_featured  featured image
     *                       meta_image     meta image field
     *                       meta           general meta field
     *     prefix         use or not prefix 
     *                       default FALSE
     *     import-export  use this field in import/export or not
     *                       default TRUE
     *     label          name of field
     *                       default not exists
     *     dependent_id   is ID ot this field depender by other WP_Post
     *                       default FALSE
     * 
     * @param $type string
     *
     * @return  array
     * 
     * @since  1.0.35
     * 
     */
    public static function fields( $type = NULL )
    {
      $fields = array(
                      'course'  =>  array(
                                      array( 'key'     =>  'post_type' ),
                                      array( 'key'     =>  'post_status' ),
                                      array( 'key'     =>  'post_title' ),
                                      array( 'key'     =>  'post_content' ),
                                      array( 'key'     =>  'post_name' ),
                                      array( 'key'     =>  'menu_order' ),
                                      array( 
                                            'key'           =>  '_thumbnail_id', 
                                            'type'          =>  'meta_featured',
                                            'label'         =>  __( 'Featured image', TWM_TD )
                                          ),
                                      array( 
                                            'key'           =>  'info_show_on_login', 
                                            'type'          =>  'meta', 
                                            'prefix'        =>  TRUE, 
                                            'label'         =>  __( 'Show this course on login page', TWM_TD ) 
                                          ),
                                      array( 
                                            'key'           =>  'info',  
                                            'type'          =>  'meta', 
                                            'prefix'        =>  TRUE,
                                            'label'         =>  __( 'Course info', TWM_TD )
                                          ),
                                      array( 
                                            'key'           =>  'ds24', 
                                            'type'          =>  'meta', 
                                            'prefix'        =>  TRUE, 
                                            'import-export' =>  FALSE,
                                            'label'         =>  __( 'Payment systems', TWM_TD )
                                          ),
                                        ),
                      'module'  =>  array(
                                      array( 'key'     =>  'post_type' ),
                                      array( 'key'     =>  'post_status' ),
                                      array( 'key'     =>  'post_title' ),
                                      array( 'key'     =>  'post_content' ),
                                      array( 'key'     =>  'post_name' ),
                                      array( 'key'     =>  'menu_order' ),
                                      array( 
                                            'key'           =>  'image', 
                                            'type'          =>  'meta_image' 
                                          ),
                                      array( 
                                            'key'           =>  'before_start', 
                                            'type'          =>  'meta' 
                                          ),
                                      array(  
                                            'key'           =>  'course_id', 
                                            'type'          =>  'meta',
                                            'import-export' =>  FALSE,
                                            'dependent_id'  =>  'course'
                                          ),
                                        ),
                      'lesson'  =>  array(
                                      array( 'key'     =>  'post_type' ),
                                      array( 'key'     =>  'post_status' ),
                                      array( 'key'     =>  'post_title' ),
                                      array( 'key'     =>  'post_content' ),
                                      array( 'key'     =>  'post_name' ),
                                      array( 'key'     =>  'menu_order' ),
                                      array( 
                                            'key'           =>  'quiz', 
                                            'type'          =>  'meta' 
                                          ),
                                      array( 
                                            'key'           =>  'before_start', 
                                            'type'          =>  'meta' 
                                          ),
                                      array( 
                                            'key'           =>  'course_id', 
                                            'type'          =>  'meta',
                                            'import-export' =>  FALSE,
                                            'dependent_id'  =>  'course'
                                          ),
                                      array( 
                                            'key'           =>  'module_id', 
                                            'type'          =>  'meta',
                                            'import-export' =>  FALSE,
                                            'dependent_id'  =>  'module'
                                          ),
                                        )
                        );

      return $type === NULL ? $fields : ( isset( $fields[ $type ] ) ? $fields[ $type ] : array() );
    }

    /**
     * get full key for field
     * 
     * @param  array $field
     * 
     * @return string
     *
     * @since  1.0.35
     * 
     */
    public static function field_key( $field )
    {
      return ( isset( $field['prefix'] ) ? \MemberWunder\Helpers\General::get_meta_prefix() : '' ).$field['key'];
    }
  }