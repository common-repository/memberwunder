<?php

namespace MemberWunder\Controller;

class Menu {

    /**
     * Menus list
     * @var array
     */
    private $menus;

    /**
     * Menu items
     * @var array
     */
    private $menu_items;

    /**
     * name of nav menu metabox
     * 
     * @var string
     *
     * @since  1.0.28.1
     * 
     */
    public static $nav_menu_metabox_name = '';

    public function __construct() {
        self::$nav_menu_metabox_name = TWM_TD.'-nav-menus-box';

        $this->menus = array(
            'header' => __('MemberWunder Header', TWM_TD),
            'footer' => __('MemberWunder Footer', TWM_TD),
            'guest' => __('MemberWunder Guest', TWM_TD),
        );
        $this->menu_items = array(
            'header' => array(
                array(
                    'menu-item-title' => __('Dashboard', TWM_TD),
                    'menu-item-url' => '/%memberwunder_dashboard%/',
                    'menu-item-status' => 'publish'
                ),
                array(
                    'menu-item-title' => __('Courses', TWM_TD),
                    'menu-item-url' => '/%memberwunder_courses%/',
                    'menu-item-status' => 'publish'
                ),
            ),
            'footer' => array(
                array(
                    'menu-item-title' => __('Dashboard', TWM_TD),
                    'menu-item-url' => '/%memberwunder_dashboard%/',
                    'menu-item-status' => 'publish'
                ),
                array(
                    'menu-item-title' => __('Courses', TWM_TD),
                    'menu-item-url' => '/%memberwunder_courses%/',
                    'menu-item-status' => 'publish'
                )
            ),
            'guest' => array(
                array(
                    'menu-item-title' => __('Impressum', TWM_TD),
                    'menu-item-url' => '/%memberwunder_impressum%/',
                    'menu-item-status' => 'publish'
                ),
                array(
                    'menu-item-title' => __('AGB', TWM_TD),
                    'menu-item-url' => '/%memberwunder_agb%/',
                    'menu-item-status' => 'publish'
                )
            )
        );
    }

    /**
     * Register hooks
     */
    public function hooks() 
    {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'createMenus'));

        add_filter( 'nav_menu_meta_box_object', function( $object ){
            add_meta_box( self::$nav_menu_metabox_name, __( 'MemberWunder Pages', TWM_TD ), array( $this, 'render' ), 'nav-menus', 'side', 'default' );
            return $object;
          });

        add_filter( 'nav_menu_css_class', function( $classes, $item, $args = array(), $depth = 0 ){
            if( !isset( $args->twm_location ) )
                return $classes;

            $classes[] = $args->twm_location.'__nav-item';

            return $classes;
        }, 10, 4 );

        add_filter( 'nav_menu_link_attributes', function( $atts, $item, $args = array(), $depth = 0 ){
            if( !isset( $args->twm_location ) )
                return $atts;

            $twm_classes = $args->twm_location.'__nav-link';

            $atts['class'] = isset( $atts['class'] ) ? $atts['class'].' '.$twm_classes : $twm_classes;

            return $atts;
        }, 10, 4 );
    }

    /**
     * render settings box for nav menu meta box
     * 
     * @since 1.0.28.1
     * 
     */
    public static function render()
    {
        global $nav_menu_selected_id;

        $pages = array();

        foreach( $this->menu_items as $item_group )
            $pages = array_merge( $pages, $item_group );

        $pages = array_filter(
                                $pages,
                                function ($value, $key) use ($pages) {
                                    return $key === array_search( $value['menu-item-url'], array_column( $pages, 'menu-item-url' ) );
                                },
                                ARRAY_FILTER_USE_BOTH
                              );

        \MemberWunder\Helpers\General::load( TWM_PATH.'/assets/dashboard/views/nav_menu/pages', '', '', true, false, array( 'pages' => $pages, 'nav_menu_selected_id' => $nav_menu_selected_id, 'field_id' => self::$nav_menu_metabox_name ) );
    }

    /**
     * "init" action
     */
    public function init() {
        add_shortcode('memberwunder-menu', array($this, 'shortcode'));
        add_filter('wp_setup_nav_menu_item', array($this, 'setupMenuItem'), 10, 1);
        add_filter('wp_nav_menu_objects', array($this, 'setupMenuItemsClasses'), 10, 3);
    }

    /**
     * Create initial MemberWunder menus
     */
    public function createMenus() {
        foreach ($this->menus as $menu_slug => $menu_name) {
            $menu_exists = wp_get_nav_menu_object($menu_name);

            if (!$menu_exists) {
                $menu_id = wp_create_nav_menu($menu_name);
                foreach ($this->menu_items[$menu_slug] as $item) {
                    wp_update_nav_menu_item($menu_id, 0, $item);
                }
            }
        }
    }

    /**
     * Handle [memberwunder-menu] shortcode
     *
     * @param array $atts
     * @return string
     */
    public function shortcode($atts) 
    {
        $name = null;
        $a = shortcode_atts(array('name' => ''), $atts);

        foreach ($this->menus as $menu_slug => $menu_name) {
         
            if ($a['name'] === $menu_slug) {
                $name = $menu_name;
                break;
            }
        }

        if (!$name) {
            return '';
        }

        return wp_nav_menu(array(
            'menu'              =>  $name,
            'menu_class'        =>  '',
            'fallback_cb'       =>  false,
            'depth'             =>  1,
            'items_wrap'        => '<ul class="'.$a['name'].'__nav-list">%3$s</ul>',
            'echo'              =>  false,
            'container'         =>  false, 
            'twm_location'      =>  $a['name']
        ));
    }

    /**
     * Process macroses in menu item url
     *
     * @param object $menu_item The menu item object.
     * @return object
     */
    public function setupMenuItem($menu_item) {
        if (!is_admin() && $menu_item->url) {
            $menu_item->url = str_replace('/%memberwunder_dashboard%/', twmshp_get_dashboard_url(), $menu_item->url);
            $menu_item->url = str_replace('/%memberwunder_courses%/', twmshp_get_courses_url(), $menu_item->url);
            $menu_item->url = str_replace('/%memberwunder_impressum%/', twmshp_get_impressum_url(), $menu_item->url);
            $menu_item->url = str_replace('/%memberwunder_agb%/', twmshp_get_agb_url(), $menu_item->url);
        }
        return $menu_item;
    }

    /**
     * Add "active" class to current menu item
     *
     * @param array $menu_items The menu items, sorted by each menu item's menu order.
     * @param \stdClass $args An object containing wp_nav_menu() arguments.
     * @return array
     */
    public function setupMenuItemsClasses($menu_items, $args) {
        if (!is_admin() && $menu_items) {
            foreach ($menu_items as $menu_item) {
                if (
                        $menu_item->classes &&
                        in_array('current-menu-item', $menu_item->classes) &&
                        !in_array('active', $menu_item->classes)
                ) {
                    $menu_item->classes[] = 'active';
                }
            }
        }
        return $menu_items;
    }

}
