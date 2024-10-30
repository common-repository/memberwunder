<?php
namespace MemberWunder;

class Application 
  {

    protected $_meta_prefix = 'twm_';

    /**
     * @var Controller\Pages
     */
    protected $controllerPages;

    /**
     * @var Controller\Profile
     */
    protected $controllerProfile;

    /**
     * @var Controller\Menu
     */
    protected $controllerMenu;

    /**
     * @var Controller\Options
     */
    protected $controllerOptions;

    /**
     * @var Controller\Register
     */
    protected $controllerRegister;

    /**
     * @var Controller\Reset
     */
    protected $controllerReset;
    protected $smtp;

    protected function _controllers() {
        $this->controllerPages = new Controller\Pages();
        $this->controllerProfile = new Controller\Profile();
        $this->controllerMenu = new Controller\Menu();
        $this->controllerOptions = new Controller\Options();
        $this->controllerRegister = new Controller\Register();
        $this->controllerReset = new Controller\Reset();
        $this->smtp = new \MemberWunder\Services\Smtp();
    }

    protected function _hooks() {
        $this->controllerPages->hooks();
        $this->controllerProfile->hooks();
        $this->controllerMenu->hooks();
        $this->controllerOptions->hooks();
        $this->controllerRegister->hooks();
        $this->controllerReset->hooks();
        $this->smtp->hooks();

        add_image_size('twm_block_small', 400, 300);
        add_image_size('twm_social_icon', 75, 75, true);
        add_image_size('twm_advantage_icon', 100, 100, true);
        add_image_size('twm_avatar', 300);

        add_filter('image_size_names_choose', array($this, 'additionalSizes'));
        add_filter('upload_mimes', array($this, 'uploadMimes'));

        //register custom post type
        add_action('init', array($this, 'init'));
        //initMetabox
        add_action('add_meta_boxes', array($this, 'initPostTypeMetaboxes'));
        //assets
        add_action( 'wp_enqueue_scripts', array($this, 'initFrontendAssets') ); 
        add_action( 'admin_init', array($this, 'initAssets') );
        add_action( 'admin_notices', array($this, 'adminNotices'));
        //save course
        add_action('save_post', array($this, 'saveCourse'));

        add_action('template_redirect', array($this, 'templateRedirect'));

        add_action('twm_baseurl_change', array( '\MemberWunder\Controller\Install', 'rewriteRules' ), 10, 0);
        add_action('update_option_permalink_structure', array( '\MemberWunder\Controller\Install', 'rewriteRules' ), 10, 0);

        if (!is_multisite()) {
            add_action('twm_allow_registration_change', array($this, 'installRegistrationSetting'), 10, 0);
        }

        add_action( 'wp_head', array( __CLASS__, 'initCustomCss' ) );
        add_filter( 'body_class', array( __CLASS__, 'custom_body_classes' ) );
        add_filter( TWM_TD.'_body_class', array( __CLASS__, 'custom_body_classes' ) );
        add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar' ), 200 );
        add_action( 'init', function(){
          if( !is_admin() || defined( 'DOING_AJAX' ) )
            return;

          $constraints = twmshp_get_option( 'disallow_visit' );
          
          if( empty( $constraints ) )
            return; 
          
          $role = wp_get_current_user()->roles[0];
          
          if( !in_array( $role, $constraints ) )
            return;

          wp_redirect( Helpers\View::get_frontend_dashboard_url() );
          exit();
        }, 5 );

        add_action( 'init', function(){
          new \MemberWunder\Services\Notice( 
              'buy_pro',
              array( 
                  'text'    =>  __( 'Thanks for installing the free version of MemberWunder. Did you know that MemberWunder is even more powerful in the licensed version? You can monetarize your content, limit it to exclusive customers and start to build an online business. Click on "<strong>Learn more</strong>" to find out more about MemberWunder.', TWM_TD ), 
                  'link'    =>  array( 
                                    'href' => 'https://www.memberwunder.com', 
                                    'text' => __( 'Learn more', TWM_TD ) 
                                    ),
                  'is_ajax' =>  FALSE,
                  'dismiss' =>  FALSE
                  ),
              array( 'mw', 'dashboard' ) 
                );
        });

        /** ADDED COLUMN TO twmembership */
        add_filter('manage_edit-'.TWM_COURSE_TYPE.'_columns', function($columns){
            if( isset( $columns['date'] ) )
              unset( $columns['date'] );
            return array_merge( $columns, array( 'date' => __( 'Date', TWM_TD ), TWM_COURSE_TYPE.'-order' => __( 'Order', TWM_TD ) ) );
        });
        add_action('manage_' . TWM_COURSE_TYPE . '_posts_custom_column', function ($column_name, $post_id) {
            if ($column_name === TWM_COURSE_TYPE . '-order')
                echo (int)get_post_field('menu_order', $post_id, true);    
        }, 10, 2);
        add_filter( 'manage_edit-'.TWM_COURSE_TYPE.'_sortable_columns', function( $columns ) {
            return array_merge( $columns, array( TWM_COURSE_TYPE.'-order' => TWM_COURSE_TYPE.'-order' ) );
        });
        add_action( 'pre_get_posts', function( $query ) {
            if( ! is_admin() )
                return;
         
            $orderby = $query->get( 'orderby');
         
            if( TWM_COURSE_TYPE.'-order' == $orderby )
              $query->set( 'orderby', array( 'menu_order' => $query->get( 'order' ), 'date' => 'ASC' ) );
        });
        /** END ADDED COLUMN TO twmembership  */

        \MemberWunder\Helpers\General::load_by_action( 'controller/user/general', '\MemberWunder\Controller\User\General', 'init', 'init' );

        /* ADD COUNTERS */
        add_action( 'twshp_after_body_start', function(){
          echo twmshp_get_option( 'header_analytics_code' );
        });

        add_action( 'twshp_before_body_end', function(){
          echo twmshp_get_option( 'footer_analytics_code' );
        });
        /* END ADD COUNTERS */

        add_filter( "plugin_action_links_" . TWM_BASENAME, function( $links ){
          $link = '<a href="'.admin_url( 'options-general.php?page=memberwunder' ).'">'.__( 'Settings', TWM_TD ).'</a>';
          return array_merge( array( $link ), $links );
        });

        add_filter( 'plugin_row_meta', function( $links, $file ){ 
          if( strpos( $file, TWM_BASENAME ) === false )
            return $links; 
        
          $link  = array( 'get_pro' => sprintf( '<a href="https://www.memberwunder.com" target="_blank"><span class="tw_get_memberwunder">%s</span></a>', __( 'Get MemberWunder PRO', TWM_TD ) ) );
        
          return array_merge( $links, $link );
        }, 5, 2 );
    }   

    /**
     * get meta prefix
     * 
     * @return string
     *
     * @since  1.0.34
     * 
     */
    public function get_meta_prefix()
    {
      return $this->_meta_prefix;
    }

    /**
     * add nodes to admin bar
     * 
     * @param  object $wp_admin_bar
     * 
     * @since 1.0.0.0
     * 
     */
    public static function admin_bar( $wp_admin_bar )
    {
      if( current_user_can( 'manage_options' ) )
      {
        $wp_admin_bar->add_node( array(
                                        'id'      =>  TWM_TD,
                                        'title'   =>  sprintf( '<span class="ab-icon"></span><span class="ab-label">%s</span>', __( 'MemberWunder', TWM_TD ) ),
                                        'href'    =>  admin_url( 'options-general.php?page=memberwunder' ),
                                        'meta'    =>  array( 'class' => 'twm-adminbar' )
                                      ) 
        );

        $wp_admin_bar->add_node( array(
                                        'id'      =>  TWM_TD.'-frontend',
                                        'title'   =>  __( 'Visit MemberWunder', TWM_TD ),
                                        'href'    =>  Helpers\View::get_frontend_dashboard_url(),
                                        'parent'  =>  TWM_TD,
                                        'meta'    =>  array( 'class' => 'twm-adminbar-frontend', 'target' => '_blank' )
                                      ) 
        );
      }else{
        $wp_admin_bar->add_node( array(
                                        'id'      =>  TWM_TD,
                                        'title'   =>  sprintf( '<span class="ab-icon"></span><span class="ab-label">%s</span>', __( 'Visit MemberWunder', TWM_TD ) ),
                                        'href'    =>  Helpers\View::get_frontend_dashboard_url(),
                                        'meta'    =>  array( 'class' => 'twm-adminbar', 'target' => '_blank' )
                                      ) 
        );
      } 
    }

    /**
     * added custom classes to body
     * 
     * @param  array $classes
     * 
     * @return array
     *
     * @since 1.0.15.4
     * 
     */
    public static function custom_body_classes( $classes )
    {   
        $custom_classes = array();

        if( twmshp_get_option( 'hide_color_overlay' ) )
          $custom_classes[] = 'theme-without-image-overlay';

        $template = twmshp_get_template();
        $custom_classes[] = substr( $template, 0, 5 ) === 'theme' ? 'theme__sm-'.substr( $template, 5 ) : '';

        return array_merge( $classes, $custom_classes );
    }

    /**
     * init custom CSS from options
     * 
     * @since 1.0.15.2
     * 
     */
    public static function initCustomCss()
    {
        $css = twmshp_get_option( 'custom_css' );
        if( !empty( $css ) )
            echo '<style type="text/css">'.$css.'</style>';
    }

    public function run() {
        $this->_controllers();
        $this->_hooks();
    }

    /**
     * @param array $sizes
     * @return array
     */
    public function additionalSizes($sizes) {
        $sizes['twm_social_icon'] = __('Social icon', TWM_TD);
        $sizes['twm_advantage_icon'] = __('Advantage icon', TWM_TD);
        return $sizes;
    }

    /**
     * @param array $mimes
     * @return array
     */
    public function uploadMimes($mimes) 
    {
        if (!isset($mimes['ico'])) {
            $mimes['ico'] = 'image/x-icon';
        }
        return $mimes;
    }

    /**
     * @param string $post_link
     * @param \WP_Post $post
     * @param bool $leavename
     * @param bool $sample
     * @return string
     */
    public function postTypeLink($post_link, $post, $leavename, $sample) {
        if (
            get_option('permalink_structure') &&
            ($post->post_type === TWM_COURSE_TYPE || $post->post_type === TWM_MODULE_TYPE || $post->post_type === TWM_LESSONS_TYPE)
        ) {
            $slug = $post->post_name;
            $draft_or_pending = get_post_status($post) && in_array(get_post_status($post), array('draft', 'pending', 'auto-draft', 'future'));
            $post_type = get_post_type_object($post->post_type);

            if (!empty($post_link) && (!$draft_or_pending || $sample)) {
                if ($leavename) {
                    $slug = '%' . $post->post_type . '%';
                }

                if ($post->post_type === TWM_COURSE_TYPE) {
                    $post_link = twmshp_get_baseurl() . 'courses/' . $slug . '/';
                } elseif ($post->post_type === TWM_MODULE_TYPE) {
                    $course = twmshp_get_course_by_post($post);
                    if($course) {
                        $post_link = twmshp_get_baseurl() . 'courses/' . $course->post_name . '/' . $slug . '/';
                    }
                } elseif ($post->post_type === TWM_LESSONS_TYPE) {
                    $course = twmshp_get_course_by_post($post);
                    if($course) {
                        $module = twmshp_get_module_by_post($post);
                        if($module) {
                            $post_link = twmshp_get_baseurl() . 'courses/' . $course->post_name . '/' . $module->post_name . '/' . $slug . '/';
                        }
                    }
                }

                $post_link = home_url(user_trailingslashit($post_link));
            } else {
                if ($post_type->query_var && (isset($post->post_status) && !$draft_or_pending)) {
                    $post_link = add_query_arg($post_type->query_var, $slug, '');
                } else {
                    $post_link = add_query_arg(array('post_type' => $post->post_type, 'p' => $post->ID), '');
                }
                $post_link = home_url($post_link);
            }
        }
        return $post_link;
    }

    public function init() {
        add_filter('post_type_link', array($this, 'postTypeLink'), 10, 4);

        $this->initPostType();
        $this->initTemplates();
        \MemberWunder\Controller\Install::rewriteRules( false );
    }

    public function installRegistrationSetting() {
        update_option('users_can_register', twmshp_get_option('allow_registration'));
    }

    /**
     * Add custom post type
     */
    function initPostType() {
        $labels = array(
            'name' => __('MemberWunder', TWM_TD),
            'singular_name' => __('MemberWunder', TWM_TD),
            'add_new' => __('Add course', TWM_TD),
            'add_new_item' => __('Add new course', TWM_TD),
            'edit_item' => __('Edit course', TWM_TD),
            'new_item' => __('New course', TWM_TD),
            'view_item' => __('View course', TWM_TD),
            'search_items' => __('Search course', TWM_TD),
            'not_found' => __('No course found', TWM_TD),
            'not_found_in_trash' => __('No course found in Trash', TWM_TD),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => false,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'taxonomies' => $this->_getTaxArr(),
            'menu_position' => null,
            'supports' => array(
                'title',
                'thumbnail',
                'custom-fields',
                'editor',
                'page-attributes'
            ),
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-welcome-learn-more'
        );
        register_post_type(TWM_COURSE_TYPE, $args);

        remove_post_type_support(TWM_COURSE_TYPE, 'comments');
        if (function_exists('remove_meta_box')) {
            remove_meta_box('commentstatusdiv', TWM_COURSE_TYPE, 'normal'); //removes comments status
            remove_meta_box('commentsdiv', TWM_COURSE_TYPE, 'normal'); //removes comments
        }

        $args = array(
            'labels' => array('name' => 'Modules'),
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'show_ui' => false,
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'taxonomies' => $this->_getTaxArr(),
            'menu_position' => null,
            'supports' => array(
                'title',
                'comments',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'editor',
                'page-attributes'
            ),
            'show_in_rest' => true,
        );
        register_post_type(TWM_MODULE_TYPE, $args);

        $args = array(
            'labels' => array('name' => 'Lessons'),
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'show_ui' => false,
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'taxonomies' => $this->_getTaxArr(),
            'menu_position' => null,
            'supports' => array(
                'title',
                'comments',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'editor',
                'page-attributes'
            ),
            'show_in_rest' => true,
        );
        register_post_type(TWM_LESSONS_TYPE, $args);
    }

    public function initTemplates() {
        add_filter('template_include', array($this, 'template_include'), 1000, 1);
    }

    public function template_include($template) 
    {
        global $wp;
        global $wp_query;

        if ( is_single() || is_page() ) 
        {
            $object = get_queried_object();
            
            if (!empty($object->post_type)) 
            {
              $user_id = get_current_user_id();
              
              if ( $object->post_type === TWM_COURSE_TYPE )
              { 
                if( isset($_REQUEST['mw-start']) && $user_id && !twshp_check_curs_is_started( $object->ID ) )
                  twshp_set_curs_is_started( $object->ID );
        
                if( !$user_id )
                  return TWM_PATH . '/templates/single-course-no-authorized.php';
                elseif( $user_id && !twshp_check_curs_is_started( $object->ID ) )
                  return TWM_PATH . '/templates/single-course-free-start.php';
                else
                  return TWM_PATH . '/templates/single-course.php';
              }

              if ($object->post_type === TWM_LESSONS_TYPE) 
              {
                $curs = twmshp_get_course_by_post( get_the_ID() );
                if(twshp_check_curs_is_free( $curs->ID ) && ( ( !$user_id && twshp_check_curs_is_free( $curs->ID ) ) || !twshp_check_curs_is_started( $curs->ID ) )) {
                    if(!current_user_can('administrator')) {
                        wp_redirect( get_permalink( $curs->ID ) );
                    }
                }

                $action = empty($wp_query->query_vars['twm_action']) ? (empty($_GET['twm_action']) ? null : (string) $_GET['twm_action']) : (string) $wp_query->query_vars['twm_action'];
                
                if ($action === 'quiz'){
                  return TWM_PATH . '/templates/single-lesson-quiz.php';
                }
                return TWM_PATH . '/templates/single-lesson.php';
              }
            }
        }
        $single_template = $this->controllerPages->getSingleTemplate();
        if ($single_template) {
            return $single_template;
        }
        return $template;
    }

    public function templateRedirect() {
        if (is_singular()) {
            
            /** @var \WP_Post $object */
            $object = get_queried_object();
            if (!empty($object->post_type) && $object->post_type === TWM_MODULE_TYPE) {                
                $this->templateRedirectModule($object);
            }
            if (!empty($object->post_type) && $object->post_type === TWM_LESSONS_TYPE) {
                $this->templateRedirectLesson($object);
                
            }
        }
    }

    /**
     * @param \WP_Post $module
     */
    protected function templateRedirectModule($module) {
        $course_id = get_post_meta($module->ID, 'course_id', true);
        if ($course_id) {
            if ( current_user_can('administrator') || self::isCourseUser($course_id) ) {
                // redirect from module page to its first lesson page
                $lessons = twmshp_get_lessons_by_module($module->ID);
                if ($lessons) {
                    $link = get_permalink(reset($lessons));
                } else {
                    $course = twmshp_get_course_by_post($module);
                    if ($course) {
                        $link = get_permalink($course);
                    } else {
                        $link = twmshp_get_dashboard_url();
                    }
                }
            } else {
                $link = twmshp_get_dashboard_url();
            }
        } else {
            $link = twmshp_get_dashboard_url();
        }
        //die($link);
        wp_redirect($link);
        exit;
    }

    protected static function isCourseUser( $course_id )
    {
      $ids = getFreeStartedCoursesIds();
      
      return in_array( $course_id, $ids );
    }

    /**
     * @param \WP_Post $lesson
     */
    protected function templateRedirectLesson($lesson) {

        $link = null;
        $course_id = get_post_meta($lesson->ID, 'course_id', true);
        if ($course_id) {

        } else {
            $link = twmshp_get_dashboard_url();
        }
        if ($link) {
            wp_redirect($link);
            exit;
        }

        $quiz_send = (isset($_POST['quiz_nonce']) && wp_verify_nonce($_POST['quiz_nonce'], 'send_' . $lesson->ID));
        if ($quiz_send) {
            $userAnswers = isset($_POST['answers']) ? (array) $_POST['answers'] : array();
            $quiz = get_post_meta(get_the_ID(), 'quiz', true);
            if ($quiz) {
                $mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();

                $taken_quiz = $mapperLesson->getTakenLessonQuiz(get_the_ID());
                if (!$taken_quiz || $mapperLesson->canTakeLessonQuiz(get_the_ID(), $taken_quiz->modified_at)) {
                    $quizThreshold = empty($quiz['threshold']) ? 0 : (int) $quiz['threshold'];
                    $quizQuestions = empty($quiz['questions']) ? array() : (array) $quiz['questions'];

                    $percent = 100;
                    if ($quizQuestions) {
                        $countTotal = count($quizQuestions);
                        $countCorrect = 0;
                        foreach ($quizQuestions as $questionIndex => $question) {
                            $questionAnswersCorrect = empty($question['answers_correct']) ? array() : (array) $question['answers_correct'];
                            $userQuestionAnswers = empty($userAnswers[$questionIndex]) ? array() : (array) $userAnswers[$questionIndex];
                            if (
                                    count($userQuestionAnswers) === count($questionAnswersCorrect) &&
                                    !array_diff($questionAnswersCorrect, $userQuestionAnswers)
                            ) {
                                $countCorrect++;
                            }
                        }

                        $percent = ceil(100 * $countCorrect / $countTotal);
                    }

                    $done = $percent >= $quizThreshold ? 1 : 0;
                    $mapperLesson->markLessonQuizAsTaken($lesson->ID, $percent, $done);
                }
            }
            $link = twmshp_get_lesson_quiz_url($lesson);
            wp_redirect($link);
            exit;
        }
    }

    protected function _getTaxArr() {
        return array(
                /*
                  'ort',
                  'branche',
                  'eintragstyp',

                 */
        );
    }

    /**
     * init assets for frontend
     * 
     * @since 1.0.34
     * 
     */
    public function initFrontendAssets()
    {
      wp_enqueue_style( TWM_COURSE_TYPE . '-memberwunder', TWM_ASSETS_URL.'/css/custom/memberwunder.css', false, TWM_VERSION );
    }

    public function initAssets() 
    {
      /**
       * @since  1.0.28.12 renamed general to options.js, loaded only on option page and MW post type
       */
      global $pagenow;
      
      if( ( $pagenow == 'options-general.php' && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == \MemberWunder\Controller\Options::ADMIN_PAGE ) || ( \MemberWunder\Helpers\General::is_post_type( array( TWM_COURSE_TYPE ), $pagenow ) ) )
      {
        wp_enqueue_script('jquery-ui-core', array('jquery'));
        wp_enqueue_script('jquery-ui-sortable', array('jquery'));
        wp_enqueue_script('jquery-ui-accordion', array('jquery'));
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
        wp_enqueue_script('jquery-ui-tabs', array('jquery'));


        wp_enqueue_style('jquery-ui-datepicker');

        wp_enqueue_style('jquery-ui-css-twmshp', TWM_ASSETS_URL.'/css/vendor/jquery-ui.css', false, "1.12.1", false);
        wp_enqueue_style('tooltipster', TWM_ASSETS_URL.'/css/vendor/tooltipster.bundle.min.css' );


        wp_enqueue_style(TWM_COURSE_TYPE . '-common-style', TWM_ASSETS_URL.'/css/custom/common.css',false,TWM_VERSION);

        wp_enqueue_style(TWM_COURSE_TYPE . '-vendor-colorpicker', TWM_ASSETS_URL.'/css/vendor/colorpicker.css');

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('tiny_mce');

        wp_register_script(TWM_COURSE_TYPE . '-common', TWM_ASSETS_URL.'/js/common.js', array('jquery', 'jquery-ui-tabs', 'media-upload', 'thickbox'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-common');

        wp_register_script(TWM_COURSE_TYPE . '-vendor-tooltip', TWM_ASSETS_URL.'/js/vendor/tooltipster.bundle.min.js', array('jquery'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-vendor-tooltip');

        wp_register_script(TWM_COURSE_TYPE . '-vendor-colorpicker', TWM_ASSETS_URL.'/js/vendor/colorpicker.js', array('jquery'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-vendor-colorpicker');

        wp_register_script(TWM_COURSE_TYPE . '-custom-colorpicker', TWM_ASSETS_URL.'/js/custom/colorpicker.js', array('jquery'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-colorpicker');

        wp_register_script(TWM_COURSE_TYPE . '-custom-datapicker', TWM_ASSETS_URL.'/js/custom/datapicker.js', array('jquery', 'jquery-ui-datepicker'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-datapicker');

        wp_register_script(TWM_COURSE_TYPE . '-custom-tooltip', TWM_ASSETS_URL.'/js/custom/tooltip.js', array('jquery', TWM_COURSE_TYPE . '-vendor-tooltip'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-tooltip');

        wp_register_script(TWM_COURSE_TYPE . '-custom-ace-editor', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js', array( 'jquery' ), '1.2.9');
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-ace-editor');

        wp_register_script(TWM_COURSE_TYPE . '-custom-ace-theme', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/theme-chrome.js', array( 'jquery', TWM_COURSE_TYPE . '-custom-ace-editor' ), '1.2.9');
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-ace-theme');

        wp_register_script(TWM_COURSE_TYPE . '-custom-ace-css', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/mode-css.js', array( 'jquery', TWM_COURSE_TYPE . '-custom-ace-theme' ), '1.2.9');
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-ace-css');

        wp_register_script(TWM_COURSE_TYPE . '-custom-ace-snipets', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/snippets/css.js', array( 'jquery', TWM_COURSE_TYPE . '-custom-ace-css' ), '1.2.9');
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-ace-snipets');

        wp_register_script(TWM_COURSE_TYPE . '-custom-ace-worker', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/worker-css.js', array( 'jquery', TWM_COURSE_TYPE . '-custom-ace-snipets' ), '1.2.9');
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-ace-worker');
        
        wp_register_script(TWM_COURSE_TYPE . '-custom-options', TWM_ASSETS_URL.'/js/custom/options.js', array( 'jquery', TWM_COURSE_TYPE . '-custom-ace-worker' ), TWM_VERSION);
        $data = array( 'general' => array( 'pro_only' => __( 'This function will be available in MemberWunder PRO.', TWM_TD ) ), 'import' => array( 'label' => __( 'Import courses', TWM_TD ), 'link' => admin_url( 'options-general.php?page=memberwunder#import' ) ) );

        wp_localize_script( TWM_COURSE_TYPE . '-custom-options', 'memberwunder', $data );
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-options');
        
        wp_register_script(TWM_COURSE_TYPE . '-custom-test-email', TWM_ASSETS_URL.'/js/custom/test-email.js', array('jquery'), TWM_VERSION);
        wp_enqueue_script(TWM_COURSE_TYPE . '-custom-test-email');
      }

      wp_register_script( TWM_COURSE_TYPE . '-memberwunder', TWM_ASSETS_URL.'/js/custom/memberwunder.js', array( 'jquery' ), TWM_VERSION );
      wp_enqueue_script( TWM_COURSE_TYPE . '-memberwunder');

      wp_enqueue_style( TWM_COURSE_TYPE . '-memberwunder', TWM_ASSETS_URL.'/css/custom/memberwunder.css', false, TWM_VERSION );
    }

    /**
     * initPostTypeMetaboxes 
     */
    function initPostTypeMetaboxes() {
        $types = array(TWM_COURSE_TYPE);
        foreach ($types as $type) {
            add_meta_box(TWM_COURSE_TYPE . '_info', __('Course info', TWM_TD), array($this, 'initPostTypeMetaboxesRenderInfo'), $type, 'normal', 'core');
            add_meta_box(TWM_COURSE_TYPE . '_ml', __('Modules & Lessons', TWM_TD), array($this, 'initPostTypeMetaboxesRenderML'), $type, 'normal', 'core');
            add_meta_box(TWM_COURSE_TYPE . '_ds24', __('Payment systems', TWM_TD), array($this, 'initPostTypeMetaboxesRenderPayment'), $type, 'normal', 'core');
        }
    }

    /**
     * Render metabox
     */
    function initPostTypeMetaboxesRenderML($post) 
    {
        wp_nonce_field(plugin_basename(__FILE__), TWM_COURSE_TYPE . '_nonce');

        if ($post instanceof \WP_Post)
            $template_vars = $this->_getCourseData($post->ID);

        twm_get_template_part( 'meta_boxes/modules-lessons', array( 'template_vars' => $template_vars ) );
    }

    function initPostTypeMetaboxesRenderInfo($post) 
    {
        $template_vars = twmshp_get_course_info($post->ID);
        $template_vars[ 'show_on_login' ] = get_post_meta( $post->ID, $this->_meta_prefix . 'info_show_on_login', true );

        twm_get_template_part( 'meta_boxes/course-info', array( 'template_vars' => $template_vars ) );
    }

    function initPostTypeMetaboxesRenderPayment($post) 
    {
        twm_get_template_part( 'meta_boxes/payment' );
    }

    public function saveCourse($post_id) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (empty($_POST[TWM_COURSE_TYPE . '_nonce']) || !wp_verify_nonce($_POST[TWM_COURSE_TYPE . '_nonce'], plugin_basename(__FILE__))) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $obj = get_post($post_id);

        if ($obj->post_type != TWM_COURSE_TYPE) {
            return;
        }

        $data = array();
        $midArr = array();
        $lidArr = array();

        if (!empty($_POST['module_title'])) {
            foreach ($_POST['module_title'] as $module_id => $module_title) {
                if (trim($module_title) == '') {
                    continue;
                }
                $data[$module_id] = array(
                    'id'            => $module_id,
                    'title'         => $module_title,
                    'image'         => '',
                    'description'   => '',
                    'lessons'       => array(),
                    'before_start'  => 0
                );
            }

            foreach (array_keys($data) as $module_id) {
                if (!empty($_POST['module_description']) && array_key_exists($module_id, $_POST['module_description'])) {
                    $data[$module_id]['description'] = $_POST['module_description'][$module_id];
                }
                if (!empty($_POST['module_image']) && array_key_exists($module_id, $_POST['module_image'])) {
                    $data[$module_id]['image'] = $_POST['module_image'][$module_id];
                }

                $data[$module_id]['before_start'] = isset( $_POST['module_before_start'][ $module_id ] ) ? (int)$_POST['module_before_start'][ $module_id ] * 86400 : 0;

                if (!empty($_POST['lessons_title']) && array_key_exists($module_id, $_POST['lessons_title'])) {
                    if (!empty($_POST['lessons_title'][$module_id])) {
                        foreach ($_POST['lessons_title'][$module_id] as $lesson_id => $lesson_title) {
                            $data[$module_id]['lessons'][$lesson_id] = array(
                                'id'            => $lesson_id,
                                'title'         => $lesson_title,
                                'content'       => '',
                                'before_start' => isset( $_POST['lesson_before_start'][ $module_id ][ $lesson_id ] ) ? (int)$_POST['lesson_before_start'][ $module_id ][ $lesson_id ] * 86400 : 0 
                            );
                        }
                    }
                }

                if (!empty($_POST['lessons_quiz']) && array_key_exists($module_id, $_POST['lessons_quiz'])) {
                    if (!empty($_POST['lessons_quiz'][$module_id])) {
                        foreach ($_POST['lessons_quiz'][$module_id] as $lesson_id => $quiz) {
                            if (isset($data[$module_id]['lessons'][$lesson_id])) {
                                $quiz['threshold'] = empty($quiz['threshold']) ? 0 : min(absint((int) $quiz['threshold']), 100);
                                if (!empty($quiz['questions'])) {
                                    $quiz['questions'] = array_values((array) $quiz['questions']);
                                    foreach ($quiz['questions'] as $i => $question) {
                                        if (!empty($question['answers'])) {
                                            $answers = array();
                                            $answers_correct = array();
                                            $j = 0;
                                            foreach ($question['answers'] as $key => $answer) {
                                                $answers[] = $answer;
                                                if (!empty($question['answers_correct']) && in_array((string) $key, $question['answers_correct'])) {
                                                    $answers_correct[] = $j;
                                                }
                                                $j++;
                                            }
                                            $quiz['questions'][$i]['answers'] = $answers;
                                            $quiz['questions'][$i]['answers_correct'] = $answers_correct;
                                        } else {
                                            $quiz['questions'][$i]['answers'] = array();
                                            $quiz['questions'][$i]['answers_correct'] = array();
                                        }
                                    }
                                } else {
                                    $quiz['questions'] = array();
                                }
                                $data[$module_id]['lessons'][$lesson_id]['quiz'] = $quiz;
                            }
                        }
                    }
                }

                if (!empty($data[$module_id]['lessons'])) {
                    foreach (array_keys($data[$module_id]['lessons']) as $lesson_id) {
                        if (!empty($_POST['lessons_content']) && array_key_exists($module_id, $_POST['lessons_content']) && array_key_exists($lesson_id, $_POST['lessons_content'][$module_id])) {
                            $data[$module_id]['lessons'][$lesson_id]['content'] = $_POST['lessons_content'][$module_id][$lesson_id];
                        }
                    }
                }
            }
        }


        if (!empty($data)) {
            $menu_order = 100;
            foreach ($data as $module_id => $module) {

                //modules
                if (substr($module_id, 0, 1) == '_') {
                    //new                   
                    $args = array(
                        'post_title' => wp_strip_all_tags($module['title']),
                        'post_content' => $module['description'],
                        'post_status' => 'publish',
                        'post_type' => TWM_MODULE_TYPE,
                        'menu_order' => $menu_order,
                    );
                    $mid = wp_insert_post($args);
                    $data[$module_id]['id'] = $mid;
                } else {
                    //update
                    $mid = $module_id;
                    $args = array(
                        'ID' => $module_id,
                        'post_title' => wp_strip_all_tags($module['title']),
                        'post_content' => $module['description'],
                        'menu_order' => $menu_order,
                    );
                    wp_update_post($args);
                }

                $midArr[] = $mid;
                update_post_meta( $mid, 'image', $module['image']);
                update_post_meta( $mid, 'course_id', $post_id);
                update_post_meta( $mid, 'before_start', $module['before_start'] );

                //lessons
                foreach ($module['lessons'] as $lesson_id => $lesson) {
                    if (substr($lesson_id, 0, 1) == '_') {
                        //new                   
                        $args = array(
                            'post_title' => wp_strip_all_tags($lesson['title']),
                            'post_content' => $lesson['content'],
                            'post_status' => 'publish',
                            'post_type' => TWM_LESSONS_TYPE,
                            'menu_order' => $menu_order,
                        );
                        $lid = wp_insert_post($args);
                        $data[$module_id]['lessons'][$lesson_id]['id'] = $lid;
                    } else {
                        $lid = $lesson_id;
                        //update
                        $args = array(
                            'ID' => $lesson_id,
                            'post_title' => wp_strip_all_tags($lesson['title']),
                            'post_content' => $lesson['content'],
                            'menu_order' => $menu_order,
                        );
                        wp_update_post($args);
                    }
                    $lidArr[] = $lid;
                    update_post_meta( $lid, 'quiz', empty($lesson['quiz']) ? array() : (array) $lesson['quiz'] );
                    update_post_meta( $lid, 'course_id', $post_id );
                    update_post_meta( $lid, 'module_id', $mid );
                    update_post_meta( $lid, 'before_start', $lesson['before_start'] );

                    $menu_order = $menu_order + 1;
                }
                $menu_order = $menu_order + 1;
            }
        }

        // remove other lessons
        $allLessons = $this->_getLessonsByCourse($post_id);
        if (!empty($allLessons)) {
            foreach ($allLessons as $lesson) {
                if (!in_array($lesson->ID, $lidArr)) {
                    wp_delete_post($lesson->ID, true);
                }
            }
        }

        // remove other modules
        $allModules = twmshp_get_modules_by_course($post_id);
        if (!empty($allModules)) {
            foreach ($allModules as $module) {
                if (!in_array($module->ID, $midArr)) {
                    wp_delete_post($module->ID, true);
                }
            }
        }

        if( isset( $_POST['cinfo']['show_on_login'] ) )
        {
          update_post_meta( $post_id, $this->_meta_prefix . 'info_show_on_login', $_POST['cinfo']['show_on_login'] );
          unset( $_POST['cinfo']['show_on_login'] );
        }else
          update_post_meta( $post_id, $this->_meta_prefix . 'info_show_on_login', 0 );

        // save course info meta
        update_post_meta($post_id, $this->_meta_prefix . 'info', $_POST['cinfo']);
    }

    public function adminNotices() {
        $transient = 'twm_errors_' . get_current_user_id();
        if ($error = get_transient($transient)) {
            /** @var \WP_Error $error */
            $errorData = $error->get_error_data();
            $className = isset($errorData['type']) && $errorData['type'] === 'warning' ? 'notice notice-warning' : 'error';
            echo '<div class="' . esc_attr($className) . '"><p>' . $error->get_error_message() . '</p></div>';
            delete_transient($transient);
        }
    }

    public function disableCache() {
        if (
            !defined('DONOTCACHEPAGE') && (
                isset($_REQUEST['twm_path']) ||
                (!empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], twmshp_get_baseurl()) === 0) ||
                isset($_REQUEST[TWM_COURSE_TYPE]) ||
                isset($_REQUEST[TWM_MODULE_TYPE]) ||
                isset($_REQUEST[TWM_LESSONS_TYPE])
            )
        ) {
            global $cache_enabled;
            $cache_enabled = false;
            define('DONOTCACHEPAGE', 1);
        }
    }

    protected function _getCourseData($course_id) {
        $data = get_post($course_id);
        if (!empty($data)) {
            $modules = twmshp_get_modules_by_course($course_id);
            if (!empty($modules)) {
                foreach ($modules as $index => $module) {
                    $modules[$index]->lessons = twmshp_get_lessons_by_module($module->ID);
                }
            }
            $data->modules = $modules;
        }

        return $data;
    }

    protected function _getLessonsByCourse($course_id) {
        $args = array(
            'numberposts' => -1,
            'no_found_rows' => true,
            'orderby' => 'menu_order',
            'order' => 'asc',
            'post_type' => TWM_LESSONS_TYPE,
            'meta_query' => array(
                array(
                    'key' => 'course_id',
                    'value' => (int) $course_id,
                )
            )
        );
        $data = get_posts($args);

        return $data;
    }

    protected function _getAttachIdByUrl($image_url) {
        global $wpdb;
        $id = null;
        $sql = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url);
        $attachment = $wpdb->get_col($sql);
        if (!empty($attachment)) {
            $id = $attachment[0];
        }
        return $id;
    }

}
