<?php
namespace MemberWunder\Mapper;

class Lesson
{
    /**
     * Instance variable (Singleton)
     * @var Lesson
     */
    protected static $instance;

    /**
     * Constructor
     */
    protected function __construct()
    {
        // constructor
    }

    /**
     * Generic Singleton's function
     * @return Lesson
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param int|null $user_id
     * @return array
     */
    public function getDoneLessonIds($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return array();
            }
        }

        global $wpdb;

        $ids = $wpdb->get_col(
            $wpdb->prepare('
                SELECT lesson_id
                FROM ' . TWM_TABLE_USER_LESSONS . '
                WHERE ' . TWM_TABLE_USER_LESSONS . '.user_id = %s AND blog_id = %d
            ', $user_id, get_current_blog_id())
        );

        return $ids;
    }

    /**
     * @param array|null $course_ids
     * @return int
     */
    public function countLessonsByCourse($course_ids = null)
    {
        global $wpdb;
        
        $where = 'lp.post_type = %s AND lp.post_status = %s';
        if ($course_ids !== null && !empty($course_ids)) {
            $where .= ' AND lm.meta_value IN (' . implode(',', array_map('absint', $course_ids)) . ')';
        }

        $query = $wpdb->prepare('
            SELECT lm.meta_value AS course_id, COUNT(1) AS value
            FROM ' . $wpdb->posts . ' AS lp
            INNER JOIN ' . $wpdb->postmeta . ' AS lm ON (
                lm.post_id = lp.ID AND
                lm.meta_key = %s
            )
            INNER JOIN ' . $wpdb->posts . ' AS cp ON (
                cp.ID = lm.meta_value AND
                cp.post_status = %s
            )
            WHERE ' . $where . '
            GROUP BY lm.meta_value
        ', 'course_id', 'publish', TWM_LESSONS_TYPE, 'publish');

        $result = array();
        $rows = $wpdb->get_results($query);
        if ($rows) {
            foreach ($rows as $row) {
                $result[(int)$row->course_id] = (int)$row->value;
            }
        }

        return $result;
    }

    /**
     * get nav link to lesson
     * 
     * @param  string $type 
     * @param  int $lesson_id 
     * @param  object $module  
     * @param  array $modules  
     * 
     * @return string
     *
     * @since  1.0.26.11
     * 
     */
    public function nav_link( $type, $lesson_id, $module, $modules )
    {
        $done_lessons = $this->getDoneLessonIds();
        
        if( $type == 'next' && in_array( $lesson_id, array_values( $done_lessons ) ) )
        {
            $quiz = get_post_meta( $lesson_id, 'quiz', true );
            if( !empty( $quiz['questions'] ) )
            {
                $quiz = $this->getTakenLessonQuiz( $lesson_id );
                if( !isset( $quiz->done ) || ( isset( $quiz->done ) && !$quiz->done ) )
                    return twmshp_get_lesson_quiz_url( $lesson_id );
            }
        }

        $type = 'get_'.$type.'_available_lesson';
        $next_lesson = $this->$type( $lesson_id, $module, $modules );

        return $next_lesson !== NULL ? get_permalink( $next_lesson->ID ) : '';
    }

    /**
     * get next available lesson
     * 
     * @param  int $lesson_id
     * @param  object $module
     * @param  array $modules
     * 
     * @return object
     *
     * @since  1.0.26.11
     * 
     */
    public function get_next_available_lesson( $lesson_id, $module, $modules )
    {
        $lessons = twmshp_get_lessons_by_module( $module );
        $next_lesson = twmshp_get_next_post( $lesson_id, $lessons );

        if( !empty( $next_lesson ) )
            if( $this->isAvailable( $next_lesson->ID, $module ) )
                return $next_lesson;
            else
                return $this->get_next_available_lesson( $next_lesson->ID, $module, $modules );
        

        $module = twmshp_get_next_post( $module->ID, $modules );
        $lessons = twmshp_get_lessons_by_module( $module );
        
        if( empty($lessons) )
            return NULL;

        $next_lesson = $lessons[0];
        
        if( $this->isAvailable( $next_lesson->ID, $module ) )
            return $next_lesson;
        
        return $this->get_next_available_lesson( $next_lesson->ID, $module, $modules );
    }

    /**
     * get prev available lesson
     * 
     * @param  int $lesson_id
     * @param  object $module
     * @param  array $modules
     * 
     * @return object
     *
     * @since  1.0.26.11
     * 
     */
    public function get_prev_available_lesson( $lesson_id, $module, $modules )
    {
        $lessons = twmshp_get_lessons_by_module( $module );
        $prev_lesson = twmshp_get_prev_post( $lesson_id, $lessons );

        if( !empty( $prev_lesson ) )
            if( $this->isAvailable( $prev_lesson->ID, $module ) )
                return $prev_lesson;
            else
                return $this->get_prev_available_lesson( $prev_lesson->ID, $module, $modules );
        

        $module = twmshp_get_prev_post( $module->ID, $modules );
        $lessons = twmshp_get_lessons_by_module( $module );
        
        if( empty($lessons) )
            return NULL;

        $prev_lesson = $lessons[ sizeof($lessons) - 1 ];
        
        if( $this->isAvailable( $prev_lesson->ID, $module ) )
            return $prev_lesson;
        
        return $this->get_prev_available_lesson( $prev_lesson->ID, $module, $modules );
    }

    /**
     * get time then lesson will be unlocked
     * 
     * @param  int      $lesson_id
     * @param  object   $module
     * 
     * @return int
     *
     * @since  1.0.26.5
     * @since  1.0.26.9 v2
     * 
     */
    public function getTimeUnLocked( $lesson_id, $module )
    {
        $user_id = get_current_user_id();

        if( !$user_id )
            return -1;

        $lessons_done = $this->getDoneLessonIds();

        if( in_array( $lesson_id, $lessons_done ) )
            return 0;

        $lessons        = twmshp_get_lessons_by_module( $module );

        $before_time    = get_post_meta( $lesson_id, 'before_start', true );

        if( empty( $before_time ) )
            return 0;

        $course_id = get_post_meta( $lesson_id, 'course_id', true );
        
        if( empty( $course_id ) )
            return -1;

        $time_start = twshp_getCourseStartTime( $course_id, $user_id, get_current_blog_id() );

        $sql_time = strtotime( current_time('mysql', 1) );

        return $sql_time - $time_start >= (int)$before_time ? 0 : (int)$before_time - ( $sql_time - $time_start );  
    }

    /**
     * get full ublocked time based on module and lesson
     * 
     * @param  int    $lesson_id
     * @param  object $module 
     * 
     * @return int
     *
     * @since  1.0.28.3
     * 
     */
    public function getFullTimeUnLocked( $lesson_id, $module )
    {
        $module_time_unlocked = twshp_moduleGetBasedTimeUnlocked( $module->ID );
        if( $module_time_unlocked > 0 )
            return $module_time_unlocked;

        $lesson_time_unlocked = $this->getTimeUnLocked( $lesson_id, $module );
        return $lesson_time_unlocked;
    }

    /**
     * check lessons for available
     * 
     * @param  int      $lesson_id
     * @param  object   $module
     * 
     * @return boolean
     *
     * @since  1.0.26.5
     * 
     */
    public function isAvailable( $lesson_id, $module )
    {
        if(current_user_can('administrator'))
            return true;

        $course = twmshp_get_course_by_post( $lesson_id );
        if( self::is_module_blocked( $module, $course ) )
            return FALSE;

        return $this->getFullTimeUnLocked( $lesson_id, $module ) == 0 ? TRUE : FALSE;    
    }

    /**
     * check is module blocked?
     * 
     * @param  object  $module
     * @param  object  $course
     * 
     * @return boolean
     *
     * @since  1.0.28.6
     * 
     */
    public function is_module_blocked( $module, $course )
    {
        $info = twmshp_get_course_info( $course->ID );
        if( !isset( $info['is_obligatory'] ) || !$info['is_obligatory'] )
            return FALSE;
        
        $prev_module = self::get_prev_module( $module, $course );
        if( $prev_module === NULL )
            return FALSE;

        $mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();
        
        return $mapperLesson->isModuleFinished( $prev_module->ID ) ? self::is_module_blocked( $prev_module, $course ) : TRUE;
    }

    /**
     * get prev module
     * 
     * @param  object  $module
     * @param  object  $course
     * 
     * @return boolean
     *
     * @since  1.0.28.6
     * 
     */
    public function get_prev_module( $module, $course )
    {
        $modules = twmshp_get_modules_by_course($course);
        foreach( $modules as $key => $m )
            if( $m->ID == $module->ID  )
                break;
        return $key - 1 >= 0 ? $modules[ $key - 1 ] : NULL;
    }

    /**
     * @param int|null $user_id
     * @return int
     */
    public function countDoneLessons($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return false;
            }
        }

        global $wpdb;

        $query = $wpdb->prepare('
            SELECT COUNT(1) AS value
            FROM ' . TWM_TABLE_USER_LESSONS . ' as ul
            INNER JOIN ' . $wpdb->posts . ' AS lp ON (
                lp.ID = ul.lesson_id AND
                lp.post_type = %s AND
                lp.post_status = %s
            )
            INNER JOIN ' . $wpdb->postmeta . ' AS lm ON (
                lm.meta_key = %s AND
                lm.post_id = lp.ID
            )
            INNER JOIN ' . $wpdb->posts . ' AS cp ON (
                cp.ID = lm.meta_value AND
                cp.post_status = %s
            )
            WHERE ul.user_id = %s AND ul.blog_id = %d
        ', TWM_LESSONS_TYPE, 'publish', 'course_id', 'publish', $user_id, get_current_blog_id());

        $result = (int)$wpdb->get_var($query);

        return $result;
    }

    /**
     * @param int|null $user_id
     * @return int
     */
    public function countDoneLessonsByCourse($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return false;
            }
        }

        global $wpdb;

        $query = $wpdb->prepare('
            SELECT ' . $wpdb->postmeta . '.meta_value AS course_id, COUNT(1) AS value
            FROM ' . TWM_TABLE_USER_LESSONS . '
            INNER JOIN ' . $wpdb->posts . ' ON (
                ' . $wpdb->posts . '.ID = ' . TWM_TABLE_USER_LESSONS . '.lesson_id AND
                ' . $wpdb->posts . '.post_type = %s AND
                ' . $wpdb->posts . '.post_status = %s
            )
            INNER JOIN ' . $wpdb->postmeta . ' ON (
                ' . $wpdb->postmeta . '.post_id = ' . $wpdb->posts . '.ID AND
                ' . $wpdb->postmeta . '.meta_key = %s
            )
            WHERE ' . TWM_TABLE_USER_LESSONS . '.user_id = %s AND ' . TWM_TABLE_USER_LESSONS . '.blog_id = %d
            GROUP BY ' . $wpdb->postmeta . '.meta_value
        ', TWM_LESSONS_TYPE, 'publish', 'course_id', $user_id, get_current_blog_id());

        $result = array();
        $rows = $wpdb->get_results($query);
        if ($rows) {
            foreach ($rows as $row) {
                $result[(int)$row->course_id] = (int)$row->value;
            }
        }

        return $result;
    }
    
    
    function isModuleFinished($module_id,$user_id = null) {        
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return false;
            }
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare('
            SELECT COUNT(1) AS value
            FROM ' . TWM_TABLE_USER_LESSONS . ' AS t
            INNER JOIN ' . $wpdb->posts . ' AS p ON (
                p.ID = t.lesson_id AND
                p.post_type = %s AND
                p.post_status = %s
            )
            INNER JOIN ' . $wpdb->postmeta . ' AS pm ON (
                pm.post_id = p.ID AND
                pm.meta_key = %s AND
                pm.meta_value = %d
            )
            WHERE t.user_id = %s AND t.blog_id = %d 
            GROUP BY pm.meta_value
        ', TWM_LESSONS_TYPE, 'publish', 'module_id', $module_id, $user_id, get_current_blog_id());
        
        $lessonsAmountFinished = $wpdb->get_col($query);        
        
        $query = $wpdb->prepare('
            SELECT COUNT(1) AS value
            FROM ' . $wpdb->posts . ' AS p
            INNER JOIN ' . $wpdb->postmeta . ' AS pm ON (
                pm.post_id = p.ID AND
                pm.meta_key = %s AND
                pm.meta_value = %d
            )
            WHERE                
                p.post_type = %s AND
                p.post_status = %s
            GROUP BY pm.meta_value
        ', 'module_id', $module_id,  TWM_LESSONS_TYPE, 'publish');        
        $lessonsAmountAll = $wpdb->get_col($query);
        
        if($lessonsAmountAll==$lessonsAmountFinished) {
            return true;
        }
        return false;
    }
    
    
    function isCourseFinished($course_id,$user_id = null) {        
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return false;
            }
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare('
            SELECT COUNT(1) AS value
            FROM ' . TWM_TABLE_USER_LESSONS . ' AS t
            INNER JOIN ' . $wpdb->posts . ' AS p ON (
                p.ID = t.lesson_id AND
                p.post_type = %s AND
                p.post_status = %s
            )
            INNER JOIN ' . $wpdb->postmeta . ' AS pm ON (
                pm.post_id = p.ID AND
                pm.meta_key = %s AND
                pm.meta_value = %d
            )
            WHERE t.user_id = %s AND t.blog_id = %d 
            GROUP BY pm.meta_value
        ', TWM_LESSONS_TYPE, 'publish', 'course_id', $course_id, $user_id, get_current_blog_id());
        
        $lessonsAmountFinished = $wpdb->get_col($query);        
        
        $query = $wpdb->prepare('
            SELECT COUNT(1) AS value
            FROM ' . $wpdb->posts . ' AS p
            INNER JOIN ' . $wpdb->postmeta . ' AS pm ON (
                pm.post_id = p.ID AND
                pm.meta_key = %s AND
                pm.meta_value = %d
            )
            WHERE                
                p.post_type = %s AND
                p.post_status = %s
            GROUP BY pm.meta_value
        ', 'course_id', $course_id,  TWM_LESSONS_TYPE, 'publish');        
        $lessonsAmountAll = $wpdb->get_col($query);
        
        if($lessonsAmountAll==$lessonsAmountFinished) {
            return true;
        }
        return false;
    }

    /**
     * @param int $lesson_id
     * @param int|null $user_id
     * @return int|\WP_Error
     */
    public function markLessonAsDone($lesson_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new \WP_Error('twm_lesson', __('Error marking lesson as done'));
            }
        }

        global $wpdb;
        
        $query = $wpdb->prepare('
                SELECT t.id 
                FROM '.TWM_TABLE_USER_LESSONS.' AS t
                WHERE blog_id = %d AND user_id = %d AND lesson_id = %d',
                get_current_blog_id(),
                $user_id,
                $lesson_id
        );
        $res = (int)$wpdb->get_var($query);
        if ($res) {
            return $res;
        }

        if (!$wpdb->insert(
            TWM_TABLE_USER_LESSONS,
            array(
                'blog_id' => get_current_blog_id(),
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
                'created_at' => current_time('mysql', 1)
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s'
            )
        )) {
            return new \WP_Error('twm_lesson', __('Error marking lesson as done'));
        }

        $id = $wpdb->insert_id;

        return $id;
    }

    /**
     * @param int $lesson_id
     * @param int $percent
     * @param int $done
     * @param int|null $user_id
     * @return int|\WP_Error
     */
    public function markLessonQuizAsTaken($lesson_id, $percent, $done, $user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new \WP_Error('twm_lesson', __('Error marking quiz as taken'));
            }
        }

        global $wpdb;

        $current_time = current_time('mysql', 1);

        $query = $wpdb->prepare('
            SELECT t.id
            FROM '.TWM_TABLE_USER_LESSONS_QUIZES.' AS t
            WHERE blog_id = %d AND user_id = %d AND lesson_id = %d
        ', get_current_blog_id(), $user_id, $lesson_id);

        $res = (int)$wpdb->get_var($query);
        if ($res) {
            $wpdb->update(
                TWM_TABLE_USER_LESSONS_QUIZES,
                array(
                    'percent' => $percent,
                    'done' => $done,
                    'modified_at' => $current_time
                ),
                array(
                    'id' => $res
                ),
                array('%d', '%d', '%s'),
                array('%d')
            );

            return $res;
        }

        if (!$wpdb->insert(
            TWM_TABLE_USER_LESSONS_QUIZES,
            array(
                'blog_id' => get_current_blog_id(),
                'user_id' => $user_id,
                'lesson_id' => $lesson_id,
                'percent' => $percent,
                'done' => $done,
                'created_at' => $current_time,
                'modified_at' => $current_time,
            ),
            array('%d', '%d', '%d', '%d', '%d', '%s', '%s')
        )) {
            return new \WP_Error('twm_lesson', __('Error marking quiz as taken'));
        }

        $id = $wpdb->insert_id;

        return $id;
    }

    /**
     * @param int $lesson_id
     * @param int|null $user_id
     * @return object|bool|\WP_Error
     */
    public function getTakenLessonQuiz($lesson_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return new \WP_Error('twm_lesson', __('Error getting taken quiz'));
            }
        }

        global $wpdb;

        $query = $wpdb->prepare('
            SELECT t.id, t.user_id, t.lesson_id, t.percent, t.done, t.modified_at
            FROM '.TWM_TABLE_USER_LESSONS_QUIZES.' AS t
            WHERE blog_id = %d AND user_id = %d AND lesson_id = %d
        ', get_current_blog_id(), $user_id, $lesson_id);

        $res = $wpdb->get_row($query);
        if ($res) {
            return $res;
        }

        return false;
    }

    /**
     * @param int $id
     * @return object|bool
     */
    public function getTakenLessonQuizById($id)
    {
        global $wpdb;

        $query = $wpdb->prepare('
            SELECT t.id, t.user_id, t.lesson_id, t.percent, t.done, t.modified_at
            FROM '.TWM_TABLE_USER_LESSONS_QUIZES.' AS t
            WHERE blog_id = %d AND id = %d
        ', get_current_blog_id(), $id);

        $res = $wpdb->get_row($query);
        if ($res) {
            return $res;
        }

        return false;
    }

    /**
     * @param int $lesson_id
     * @param string $modified_at
     * @return bool
     */
    public function canTakeLessonQuiz($lesson_id, $modified_at)
    {
        $quiz = get_post_meta($lesson_id, 'quiz', true);
        if (!$quiz) {
            return false;
        }

        $quizRetake = empty($quiz['retake']) ? '' : (string)$quiz['retake'];
        $last_take_time = mysql2date('U', $modified_at);

        $interval = 0;
        if ($quizRetake === 'once_per_day') {
            $interval = 3600 * 24;
        } elseif ($quizRetake === 'once_per_month') {
            $interval = 3600 * 24 * 30;
        }
        if (time() - $last_take_time < $interval) {
            return false;
        }

        return true;
    }
}