<?php
    namespace MemberWunder\Controller;

    class Options 
    {
        /**
         * slug for options page
         * 
         * @var string
         *
         * @since  1.0.28.7
         * 
         */
        const ADMIN_PAGE = 'memberwunder';

        /**
         * slug for options in DB
         *
         * @var string
         *
         * @since  1.0.28.7
         * 
         */
        const OPTION_NAME = 'twmembership';

        /**
         * list sections for options page
         * 
         * @var array
         *
         * @since  1.0.0.0
         * 
         */
        protected static $sections;

        /**
         * list sections for show with labels
         * 
         * @var null
         *
         * @since  1.0.34.2
         * 
         */
        protected static $sections_for_show = NULL;

        /**
         * array of values from options
         * 
         * @var null
         *
         * @since 1.0.15.4
         * 
         */
        public static $values = NULL;

        /**
         * array of default values from fields
         * 
         * @var null
         *
         * @since  1.0.34.2
         * 
         */
        protected static $default = NULL;

        /**
         * name of current template
         * 
         * @var null
         *
         * @since 1.0.16.4
         * 
         */
        public static $current_template = NULL;

        /**
         * list of all fields
         * 
         * @var array
         *
         * @since  1.0.34.2
         * 
         */
        protected static $fields = array();

        public function __construct() 
        {
            $sections_path = 'controller/options/sections/';
            $full_sections_path = TWM_PATH.'/include/'.$sections_path;

            $sections = array_diff( scandir( $full_sections_path ), array( '.', '..', 'section.php' ) );

            if( count( $sections ) <= 0 )
                return;

            \MemberWunder\Helpers\General::load( $sections_path.'section' );
            
            foreach( $sections as $section )
            {
                if( is_dir( $full_sections_path.'/'.$section ) )
                    continue;

                $name = str_replace( '.php', '', $section );
                $class = '\MemberWunder\Controller\Options\Sections\\'.ucfirst( $name );

                \MemberWunder\Helpers\General::load( $sections_path.$name );
                
                self::$sections[ $class::get_key() ] = $class;

                add_filter( TWM_HOOK_PREFIX.'_options_fields', array( $class, 'get_fields' ), $class::get_order() );
                add_filter( TWM_HOOK_PREFIX.'_options_sections', array( $class, 'section_filter' ), $class::get_order() );

                unset( $name, $class );
            }

            uasort( self::$sections, function( $a, $b ){
                $a = $a::get_order();
                $b = $b::get_order();

                if ($a == $b) return 0;
                return ($a < $b) ? -1 : 1;
            });

            /**
             * loading options fields 
             * 
             * @since 1.0.34.2
             * 
             */
            self::$fields = apply_filters( TWM_HOOK_PREFIX.'_options_fields', self::$fields );

            foreach( self::$fields as $group )
                foreach( $group as $field )
                    self::$default[ $field['key'] ] = isset( $field['default'] ) ? $field['default'] : '';
        }

        /**
         * init hooks after register controller
         * 
         * @since 1.0.0.0
         *
         */
        public function hooks() 
        {
            add_action( 'admin_menu', array( $this, 'adminMenu' ) );
            add_action( 'admin_init', array( $this, 'adminOptionsPage' ));
            add_action( 'admin_notices', array( $this, 'multisiteNotice' ) );
            add_action( 'update_option_' . self::OPTION_NAME, array( $this, 'adminOptionsUpdate' ), 10, 2);
            add_filter( 'pre_update_option_' . self::OPTION_NAME, array( $this, 'preAdminOptionsUpdate' ), 10, 2 );        
        }

        /**
         * add notices to options page in dashboard
         * 
         * @since 1.0.0.0
         * 
         */
        public function multisiteNotice() 
        {
            if( is_multisite() && !empty( $_GET['page'] ) && $_GET['page'] === self::ADMIN_PAGE ) 
            {
                $registration = get_site_option('registration');
                if( $registration !== 'all' && $registration !== 'user' )
                    echo '<div class="notice notice-warning"><p>' . sprintf( __('You should allow new user registrations in your <a href="%s">Network Settings</a> for proper plugin functioning', TWM_TD ), esc_url( network_admin_url( 'settings.php' ) ) ) . '</p></div>';
            }
        }

        /**
         * return array of existing sections on options page
         * 
         * @return array
         *
         * @since  1.0.0.0
         * @since  1.0.34.2 added filter TWM_HOOK_PREFIX.'_options_sections'
         * 
         */
        public static function getSections() 
        {
            if( !is_null( self::$sections_for_show ) )
                return self::$sections_for_show;

            $sections = array();

            foreach( self::$sections as $key => $handler )
                $sections[ $key ] = $handler::get_label();

            self::$sections_for_show = apply_filters( TWM_HOOK_PREFIX.'_options_sections', $sections );

            return self::$sections_for_show;
        }

        /**
         * init options page in dashboard
         * 
         * @since 1.0.0.0
         * 
         */
        public function adminMenu() 
        {
            add_options_page(
                            __( 'MemberWunder', TWM_TD ), 
                            __( 'MemberWunder', TWM_TD ), 
                            'administrator', 
                            self::ADMIN_PAGE, 
                            array( $this, 'optionsPageWrapper' )
            );
        }

        public function adminOptionsPage() 
        {
            register_setting( self::ADMIN_PAGE, self::OPTION_NAME, array( $this, 'sanitizeOption' ) );

            foreach( self::getSections() as $section_key => $section_label ):
                add_settings_section( $section_key, '', null, self::ADMIN_PAGE );

                if( empty( self::$fields[ $section_key ] ) )
                    continue;

                foreach( self::$fields[ $section_key ] as $field )
                    self::dynamicSettingsField( $section_key, $field['key'], $field['label'], $field['attr'] );
            endforeach;
        }

        /**
         * get all options for OPTION_NAME from DB
         * 
         * @since 1.0.28.9
         * 
         */
        protected static function get_options()
        {
            if( self::$values === NULL )
                self::$values = get_option( self::OPTION_NAME );
        }

        /**
         * get option value from DB
         * 
         * @param  string $name
         * 
         * @return string || Array || Object || NULL
         *
         * @since  1.0.28.9
         * 
         */
        public static function get_option( $name )
        {
            self::get_options();
            
            return apply_filters( TWM_HOOK_PREFIX.'_option_value', isset( self::$values[ $name ] ) ? self::$values[ $name ] : ( isset( self::$default[ $name ] ) ? self::$default[ $name ] : '' ), $name );
        }

        /**
         * init option field
         * 
         * @since 1.0.15.4
         * 
         */
        protected static function dynamicSettingsField( $section, $name, $title, $attr = array() )
        {
            self::get_options();

            if( self::$current_template === NULL )
                self::$current_template = twmshp_get_template();

            $attr = array_merge(
                                array(
                                    'name'          => self::OPTION_NAME.'['.$name.']', 
                                    'label_for'     => $name, 
                                    'id'            => $name,
                                    'value'         => self::get_option( $name )
                                    ), 
                                $attr
                                );

            if( !twm_is_pro() && isset( $attr['for_pro'] ) && $attr['for_pro'] === TRUE )
                $attr['class'] =  ( isset( $attr['class'] ) ? $attr['class'] : '' ).' for_memberwunder_pro';

            add_settings_field(
                                $name, 
                                $title, 
                                array( __CLASS__, 'generateFieldCode' ), 
                                self::ADMIN_PAGE, 
                                $section, 
                                $attr
                            );
        }

        /**
         * generate HTML code for field by type
         * 
         * @param  array  $args array of settings
         * 
         * @since 1.0.15.4
         * @since 1.0.18.9  added wp_enqueue_media
         * 
         */
        public static function generateFieldCode( $args )
        {
            if( isset( $args['type'] ) && in_array( $args['type'], array( 'image', 'social' ) ) )
                wp_enqueue_media();

            twm_get_template_part( 'settings/fields/'.$args['type'], $args );
        }

        /**
         * generate wrapper for options page
         * 
         * @since 1.0.0.0
         * 
         */
        public function optionsPageWrapper() 
        {
            twm_get_template_part( 'settings/wrapper' );
        }

        public function preAdminOptionsUpdate($new_value, $old_value) 
        {
            if($new_value && is_array($new_value) && !empty($new_value['baseurl'])) {
                $u = trim(mb_strtolower($new_value['baseurl'],'utf-8'));
                $url = '';
                $arr = explode('/',trim($u,'/'));
                foreach ($arr as $v) {
                    if(trim(sanitize_title($v))!='') {
                        $url = $url .'/'.trim(sanitize_title($v));
                    }
                }
                $new_value['baseurl'] = '/'.trim($url,'/');
            }

            \MemberWunder\Services\Smtp::action();
            
            return $new_value;
        }

        /**
         * @param mixed $old_value
         * @param mixed $value
         */
        public function adminOptionsUpdate($old_value, $value) 
        {

            if (is_object($old_value)) {
                $old_value = (array) $old_value;
            }

            $old_baseurl = empty($old_value['baseurl']) ? '/' : $old_value['baseurl'];
            $baseurl = empty($value['baseurl']) ? '/' : $value['baseurl'];
            if(!empty($value['baseurl'])) {
                $baseurl = mb_strtolower($baseurl,'utf-8');
                $value['baseurl'] = $baseurl;
            }
            if ($old_baseurl !== $baseurl) {
                do_action('twm_baseurl_change');
            }

            do_action('twm_allow_registration_change');
        }

        /**
         * @param mixed $value
         * @return mixed
         */
        public function sanitizeOption($value) {
            $old_value = get_option(self::OPTION_NAME);
            if (is_array($value)) {
                if (is_array($old_value)) {
                    $value += $old_value;
                }
            } else {
                $value = $old_value;
            }
            return $value;
        }

    }