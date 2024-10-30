<?php

namespace MemberWunder\Controller;

class Pages {

    /**
     * nonce key for loading css
     * 
     * @var string
     *
     * @since  1.0.28.10
     * 
     */
    static $css_action = 'twm-css';

    /**
     * @var bool
     */
    private $handled = false;

    /**
     * @var string|null
     */
    protected $single_template = null;

    public function __construct() {
        
    }

    /**
     * Register hooks
     */
    public function hooks() 
    {
        add_filter('pre_handle_404', array($this, 'handlePages'), -10, 1);

        add_action( 'wp_ajax_'.self::$css_action, array( __CLASS__, 'loader_css' ) );
        add_action( 'wp_ajax_nopriv_'.self::$css_action, array( __CLASS__, 'loader_css' ) );
    }

    /**
     * load css to frontend
     * 
     * @since  1.0.28.10
     * 
     */
    public static function loader_css()
    {
        header('Content-Type: text/css');

        if( $_SERVER['HTTP_HOST'] != wp_parse_url( get_bloginfo('url') )['host'] )
            exit();

        echo \MemberWunder\Services\Styles::css_from_less( 'styles', \MemberWunder\Controller\Options\Colors::to_less_variables() );
        exit();
    }

    /**
     * generate link to loading css
     * 
     * @return 1.0.28.10
     * 
     */
    public static function link_to_css()
    {
        return admin_url( 'admin-ajax.php?'.http_build_query( array( 'action' => self::$css_action ) ) );
    }

    /**
     * @return string|null
     */
    public function getSingleTemplate() {
        return $this->single_template;
    }

    /**
     * init MW frontend page
     * 
     * @param bool $result
     * 
     * @return bool
     *
     * @since  1.0.0.0
     * 
     */
    public function handlePages($result) 
    {
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'query-attachments' )
            return $result;

        $user_id = get_current_user_id();
        
        $this->handlePage(-998, 'profile', '/profile/', __('Profile', TWM_TD), TWM_PATH . '/templates/single-page-profile.php');
        $this->handlePage(-997, 'courses', '/courses/', __('Courses', TWM_TD), TWM_PATH . '/templates/single-page-courses.php');

        if ($user_id) {
            $this->handlePage(-999, 'dashboard', '/', __('Dashboard', TWM_TD), TWM_PATH . '/templates/single-page-dashboard.php');

        } else {
            $tpl_login = twmshp_get_option('login_layot_type') == 'advansed' ? TWM_PATH . '/templates/single-page-login-courses.php' : TWM_PATH . '/templates/single-page-login.php';
            $tpl_lostpassword = twmshp_get_option('login_layot_type') == 'advansed' ? TWM_PATH . '/templates/single-page-lostpassword-courses.php' : TWM_PATH . '/templates/single-page-lostpassword.php';

            $this->handlePage(-993, 'login', '/', __('Login', TWM_TD), $tpl_login);
            $this->handlePage(-992, 'lostpassword', '/lostpassword/', __('Lost Password', TWM_TD), $tpl_lostpassword);
            if (twmshp_users_can_register()) {
                $this->handlePage(-991, 'register', '/register/', __('Register', TWM_TD), TWM_PATH . '/templates/single-page-register.php');
            }
            $this->handlePage(-990, 'reset', '/reset/', __('Reset password', TWM_TD), TWM_PATH . '/templates/reset-page.php');
        }

        $this->handlePage(-986, 'impressum', '/impressum/', __('Impressum', TWM_TD), TWM_PATH . '/templates/single-page-impressum.php');
        $this->handlePage(-985, 'agb', '/agb/', __('AGB', TWM_TD), TWM_PATH . '/templates/single-page-agb.php');

        return $result;
    }

    /**
     * create virtual page for MW
     * 
     * @param string $name
     * @param string $page_path
     * @param string $title
     * @param string $template
     *
     * @since  1.0.0.0
     * @since  1.0.21.3 fixed then permalink = plain
     * 
     */
    protected function handlePage($post_id, $name, $page_path, $title, $template) 
    {
        if ($this->handled)
            return;

        global $wp, $wp_query;

        $match = false;

        if ( get_option('permalink_structure') ) 
        {
            if ( isset( $wp->request ) ) 
            {
                $path = trim(strtolower($wp->request), '/');

                $baseurl = twmshp_get_baseurl();
                $baseurl = trim($baseurl, '/');

                $url = $baseurl . $page_path;

                $match = !isset($_GET['preview']) && (
                    $path == trim($url, '/') ||
                    (!empty($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == trim($url, '/')) ||
                    (!$path && !empty($_GET['twm_' . $name]))
                );
            }
        } else {
            if( isset( $wp->query_vars['p'] ) && $wp->query_vars['p'] == trim( twmshp_get_option( 'baseurl' ), '/' ) )
                $match = 1;
            elseif( isset( $_GET['twm_path'] ) && $_GET['twm_path'] === $page_path )
                $match = 1;
            else
                $match = 0;

            $url = home_url( '?'.build_query( $wp->query_vars ) );
        }

        $user_id = get_current_user_id();
        if( $match && !$user_id && in_array( $page_path, array( '/profile/', '/courses/' ) ) ):
            wp_redirect( \MemberWunder\Helpers\View::get_frontend_dashboard_url() );
            exit;
        endif;

        if ($match) 
        {
            do_action('twm_pre_page_' . $name);

            // create a fake virtual page
            $post = new \stdClass;
            $post->post_author = 1;
            $post->post_name = $url;
            $post->guid = get_bloginfo('wpurl') . $url;
            $post->post_title = $title;
            $post->post_content = '';
            $post->ID = $post_id;
            $post->post_type = 'page';
            $post->post_status = 'publish';
            $post->comment_status = 'closed';
            $post->ping_status = 'closed';
            $post->comment_count = 0;
            $post->post_date = current_time('mysql');
            $post->post_date_gmt = current_time('mysql', 1);
            $post->filter = 'raw'; // important

            $wp_post = new \WP_Post($post);

            wp_cache_add($post_id, $wp_post, 'posts' );

            // make $wp_query believe this is a real page too
            unset($wp_query->query['error']);
            $wp_query->query_vars['error'] = '';

            $wp_query->post = $wp_post;
            $wp_query->posts = array( $wp_post );
            $wp_query->queried_object = $wp_post;
            $wp_query->queried_object_id = $post_id;
            $wp_query->found_posts = 1;
            $wp_query->post_count = 1;
            $wp_query->max_num_pages = 1;
            $wp_query->is_page = true;
            $wp_query->is_singular = true;
            $wp_query->is_single = false;
            $wp_query->is_attachment = false;
            $wp_query->is_archive = false;
            $wp_query->is_category = false;
            $wp_query->is_tag = false;
            $wp_query->is_tax = false;
            $wp_query->is_author = false;
            $wp_query->is_date = false;
            $wp_query->is_year = false;
            $wp_query->is_month = false;
            $wp_query->is_day = false;
            $wp_query->is_time = false;
            $wp_query->is_search = false;
            $wp_query->is_feed = false;
            $wp_query->is_comment_feed = false;
            $wp_query->is_trackback = false;
            $wp_query->is_home = false;
            $wp_query->is_embed = false;
            $wp_query->is_404 = false;
            $wp_query->is_paged = false;
            $wp_query->is_admin = false;
            $wp_query->is_preview = false;
            $wp_query->is_robots = false;
            $wp_query->is_posts_page = false;
            $wp_query->is_post_type_archive = false;

            $this->single_template = $template;
            $this->handled = true;
        }
    }
}
