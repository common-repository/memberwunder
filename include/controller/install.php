<?php
  namespace MemberWunder\Controller;

  class Install 
  {
    private static $_instance = NULL;

    private function __construct()
    {
      self::db_tables();
      self::rewriteRules();
    }

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }

    /**
     * create tables in WordPress DB
     * 
     * @since 1.0.0.0
     * 
     */
    protected static function db_tables()
    {
      $queries = array();

      $queries[] = 'CREATE TABLE ' . TWM_TABLE_USER_LESSONS . ' (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        blog_id BIGINT(20) UNSIGNED NOT NULL,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        lesson_id BIGINT(20) UNSIGNED NOT NULL,
        created_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY marked_lesson (blog_id, user_id, lesson_id)
      ) DEFAULT CHARSET=utf8;';

      $queries[] = 'CREATE TABLE ' . TWM_TABLE_USER_LESSONS_QUIZES . ' (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        blog_id BIGINT(20) UNSIGNED NOT NULL,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        lesson_id BIGINT(20) UNSIGNED NOT NULL,
        percent TINYINT(3) UNSIGNED NOT NULL,
        done TINYINT(1) UNSIGNED NOT NULL,
        created_at DATETIME DEFAULT NULL,
        modified_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY taken_quiz (blog_id, user_id, lesson_id)
      ) DEFAULT CHARSET=utf8;';

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($queries);

      add_option( TWM_COURSE_TYPE . '_version', TWM_VERSION );
    }

    /**
     * rewrite rules
     * 
     * @param bool $flush
     *
     * @since  1.0.0.0
     * 
     */
    public static function rewriteRules( $flush = true )
    {
      if( !get_option('permalink_structure') )
        return;

      $baseurl = \MemberWunder\Controller\Options::get_option( 'baseurl' );
      $baseurl = trim( empty( $baseurl ) ? '' : $baseurl, '/');
      $baseurl = $baseurl ? '/' . $baseurl . '/' : '/';
      $baseurl = ltrim( $baseurl, '/' );

      add_rewrite_tag('%twm_action%', '([^&]+)');

      add_rewrite_rule('^' . $baseurl . 'courses\/([^/]+)\/([^/]+)/([^/]+)/quiz', 'index.php?post_type=' . TWM_LESSONS_TYPE . '&twm_course=$matches[1]&twm_module=$matches[2]&name=$matches[3]&twm_action=quiz', 'top');
      add_rewrite_rule('^' . $baseurl . 'courses\/([^/]+)\/([^/]+)/([^/]+)', 'index.php?post_type=' . TWM_LESSONS_TYPE . '&twm_course=$matches[1]&twm_module=$matches[2]&name=$matches[3]', 'top');
      add_rewrite_rule('^' . $baseurl . 'courses\/([^/]+)\/([^/]+)', 'index.php?post_type=' . TWM_MODULE_TYPE . '&twm_course=$matches[1]&name=$matches[2]', 'top');
      add_rewrite_rule('^' . $baseurl . 'courses\/([^/]+)', 'index.php?post_type=' . TWM_COURSE_TYPE . '&name=$matches[1]', 'top');

      if( $flush )
          flush_rewrite_rules();
    }
  }