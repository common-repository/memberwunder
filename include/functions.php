<?php
/**
 * generate rules for show curses on login page
 * 
 * @return array
 *
 * @since  1.0.28.9
 * 
 */
function twmshp_args_login_curses()
{
    $option = (int)twmshp_get_option( 'login_advansed_layot_type' );

    $args = array( 'posts_per_page' => -1 );

    switch ( $option ) {
        case 1:
            //  Don't show courses
            $args = array( 'post__in' => array( 0 ) );
            break;
        case 2:
            //  Show only free courses
            $ids = array();
            foreach( twmshp_get_courses() as $course )
                if( twshp_check_curs_is_free( $course->ID ) )
                    $ids[] = $course->ID;
            $args = array_merge( $args, array( 'post__in' => $ids ) );
            break;
        case 4:
            //  Show all courses
            break;
        case 5:
            $args = array_merge( $args, array(
                                            'meta_key'          =>  'twm_info_show_on_login',
                                            'meta_value'        =>  1,
                                            ) 
                                );
            break;
    }

    return $args;
}

/**
 * get total count free lessons for user
 * 
 * @return int
 *
 * @since  1.0.26.8
 * 
 */
function twshp_count_free_lessons()
{
    $user_id = get_current_user_id();
    $count = 0;

    if( !$user_id )
        return $count;

    $courses = get_user_meta( $user_id, 'mw_started_free_courses', true );
    
    if( empty( $courses ) )
        return $count;

    $courses = json_decode( $courses, true );
    
    foreach( array_keys( $courses ) as $course )
        $count += sizeof( get_posts( array( 'post_type' => TWM_LESSONS_TYPE, 'posts_per_page' => -1, 'meta_key' => 'course_id', 'meta_value' => array( $course ) ) ) );
    
    return $count;
}

    /**
     * check module is available
     *
     * @param  object $module
     * 
     * @return boolean
     *
     * @since  1.0.26.7
     * @since  1.0.28.3 added before_time for module
     * 
     */
    function twmshp_moduleIsAvailable( $module ) 
    {
        $module_time_unlocked = twshp_moduleGetBasedTimeUnlocked( $module->ID );
        if( $module_time_unlocked > 0 )
            return FALSE;

        $lessons        = twmshp_get_lessons_by_module( $module->ID );
        if( empty( $lessons ) )
            return FALSE;
        
        $count_not_available = 0;
        $mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();
        foreach( $lessons as $lesson )
            if( !$mapperLesson->isAvailable( $lesson->ID, $module ) )
                $count_not_available ++;

         
        return $count_not_available == sizeof( $lessons ) ? FALSE : TRUE;
    }

    /**
     * get based module time unlocked
     * 
     * @param  int $module_id
     * 
     * @return int
     *
     * @since  1.0.28.3
     * 
     */
    function twshp_moduleGetBasedTimeUnlocked( $module_id )
    {
        $user_id = get_current_user_id();

        if( !$user_id )
            return -1;

        $mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();
        if( $mapperLesson->isModuleFinished( $module_id ) )
            return -1;

        $before_time    = get_post_meta( $module_id, 'before_start', true );

        if( empty( $before_time ) )
            return 0;

        $course_id = get_post_meta( $module_id, 'course_id', true );
        
        if( empty( $course_id ) )
            return -1;

        $time_start = twshp_getCourseStartTime( $course_id, $user_id, get_current_blog_id() );
        
        $sql_time = strtotime( current_time('mysql', 1) );

        return $sql_time - $time_start >= (int)$before_time ? 0 : (int)$before_time - ( $sql_time - $time_start );
    }

    /**
     * get user curse time start
     * 
     * @param  int $course_id 
     * @param  int $user_id   
     * @param  int $blog_id  
     * 
     * @return string
     *
     * @since  1.0.28.3
     * 
     */
    function twshp_getCourseStartTime( $course_id, $user_id, $blog_id )
    {
        $courses = get_user_meta( $user_id, 'mw_started_free_courses', true );
        
        if( empty( $courses ) )
            return -1;
        
        $courses = json_decode( $courses, true );
        
        if( !isset( $courses[ $course_id ] ) )
            return -1;
        
        $time_start = $courses[ $course_id ];

        return $time_start;
    }

/**
 * get CPT meta data
 *
 * @param  integer||NULL  $id
 * @param  string         $title
 * @param  string         $default
 * @param  string         $format
 * @param  boolean        $br
 * @param  boolean        $page
 * @param  boolean        $mail
 *
 * @return string
 *
 * @since 1.0.26.5
 *
 */
function twshp_get_meta_data( $id, $title, $default = '', $format = '%s', $br = false, $page = false, $mail = false )
{
  $data = get_post_meta( $id, $title, true );
  if( empty( $data ) )
    $data = $default;

  $value = $br ? str_replace( "\r", '</br>', $data ) : $data;

  if( $value != null )
    if( $page )
      $value = sprintf( $format, get_permalink($value), get_the_title($value) );
    else
      $value = $mail ? sprintf( $format, $value, $value ) : sprintf( $format, $value );

  return $value;
}

/**
 * labels for countdown
 *
 * @return  string
 * 
 * @since 1.0.28
 * 
 */
function twsp_labels_for_countdown()
{
    $data = array(
                'days'      =>  array( __( 'Day', TWM_TD ), __( 'Days', TWM_TD ) ),
                'hours'     =>  array( __( 'Hour', TWM_TD ), __( 'Hours', TWM_TD ) ),
                'minutes'   =>  array( __( 'Minute', TWM_TD ), __( 'Minutes', TWM_TD ) ),
                'seconds'   =>  array( __( 'Second', TWM_TD ), __( 'Seconds', TWM_TD ) )
                );

    return json_encode( $data );
}

/**
 * check curs. is started?
 * 
 * @param  int $id
 * 
 * @return boolean
 *
 * @since  1.0.21.6
 * 
 */
function twshp_check_curs_is_started( $id )
{
    $user_id = get_current_user_id();
    $free_courses = get_user_meta( $user_id, 'mw_started_free_courses', true );
    
    if( empty( $free_courses ) )
        return FALSE;

    $free_courses = json_decode( $free_courses, true );

    return isset( $free_courses[ $id ] ) ? TRUE : FALSE;
}

/**
 * set curs as started
 * 
 * @param  int $curs_id
 * 
 * @since 1.0.21.6
 * 
 */
function twshp_set_curs_is_started( $curs_id )
{
    $user_id = get_current_user_id();
    $free_courses = get_user_meta( $user_id, 'mw_started_free_courses', true );
    
    $free_courses = empty( $free_courses ) ? array() : json_decode( $free_courses, true );

    if( !isset( $free_courses[ $curs_id ] ) )
        $free_courses[ $curs_id ] = strtotime( current_time('mysql', 1) ); 

    $free_courses = json_encode( $free_courses );

    update_user_meta( $user_id, 'mw_started_free_courses', $free_courses );
}

/**
 * return option value in format
 * 
 * @param  string $option  
 * @param  string $format  
 * @param  string $default 
 * 
 * @return string         
 *
 * @since 1.0.18.9
 * 
 */
function twmshp_get_formatted_option( $option, $format = '%s', $default = '' )
{
    $value = twmshp_get_option( $option );
    if( empty($value) )
        return $default;

    return sprintf( $format, $value );
}

/**
 * generate of list body classes for plugin template
 *
 * @param array $classes default classes
 * 
 * @return string
 *
 * @since 1.0.17.1
 * @since 1.0.21 refactor code
 * 
 */
function twshp_body_classes( $classes = array() )
{
    $classes = apply_filters( TWM_TD.'_body_class', $classes );
    return empty( $classes ) ? '' : 'class="'.implode( ' ', $classes ).'"';
}

function twmshp_load_textdomain() {
    $domain = TWM_TD;
    if (is_textdomain_loaded($domain)) {
        return true;
    } else {
        $locale = apply_filters(
            'plugin_locale',
            (is_admin() && function_exists('get_user_locale')) ? get_user_locale() : get_locale(),
            $domain
        );

        if ($locale === 'de_DE_formal' || $locale === 'de_CH' || $locale === 'de_CH_informal') {
            $locale = 'de_DE';
        }

        $mofile = $domain . '-' . $locale . '.mo';
        $path = TWM_PATH . DIRECTORY_SEPARATOR . 'languages';

        return load_textdomain($domain, $path . '/' . $mofile);
    }
}

/**
 * get template part from views folder
 * 
 * @param  string    $template    Template name
 * @param  array     $args        Array of args
 * @param  string    $ext         Extension of file
 * @param  boolean   $dashboard   Dashboard or Frontend
 * 
 * @since  1.0.15.4
 * 
 */
function twm_get_template_part( $template, $args = array(), $ext = 'php', $dashboard = true )
{
    if( !empty( $args ) )
        extract( $args );
    
    include( TWM_PATH.'/assets/'.( $dashboard ? 'dashboard' : 'frontend' ).'/views/'.$template.'.'.$ext );
}

function twmshp_render_editor($content, $name, $id) {
    ob_start();
    ?>
    <div class="ms-editor">
        <div id="wp-<?php echo $id; ?>-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
            <div id="wp-<?php echo $id; ?>-editor-tools" class="wp-editor-tools hide-if-no-js">
                <div id="wp-<?php echo $id; ?>-media-buttons" class="wp-media-buttons">
                    <button type="button" class="button insert-media add_media" data-editor="<?php echo $id; ?>"><span class="wp-media-buttons-icon"></span> <?php _e('Add Media', TWM_TD); ?></button>
                </div>
                <div class="wp-editor-tabs">
                    <button type="button" id="<?php echo $id; ?>-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="<?php echo $id; ?>"><?php _e('Visual', TWM_TD); ?></button>
                    <button type="button" id="<?php echo $id; ?>-html" class="wp-switch-editor switch-html" data-wp-editor-id="<?php echo $id; ?>"><?php _e('Text', TWM_TD); ?></button>
                </div>
            </div>
            <div id="wp-<?php echo $id; ?>-editor-container" class="wp-editor-container">
                <div id="qt_<?php echo $id; ?>_toolbar" class="quicktags-toolbar"></div>
                <textarea class="wp-editor-area js-twmshp__editor" rows="7" autocomplete="off" cols="40" name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo esc_textarea($content); ?></textarea>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function twmshp_render_lesson($id, $name, $title, $content, $hasQuiz = false, $quizesHtml = '') {
    ob_start();
    ?>
    <div class="group js-twmshp__lesson-item" data-lesson-id="<?php echo $id; ?>">
        <h3 class="js-group__label ms-head" data-default="<?php echo esc_html($title); ?>">
            <span class="js-group__label_text"><?php echo esc_html($title); ?></span>
            <a class="js-twmshp__remove-lesson" href="#"><span class="ui-icon ui-icon-trash"></span> <?php _e('Remove lesson', TWM_TD); ?></a>
        </h3>
        <div class="twmshp__lesson-form js-twmshp__lesson-form">
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="lesson<?php echo $id; ?>"><?php _e('Lesson title', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="lesson<?php echo $id; ?>" name="lessons_title<?php echo $name; ?>" size="25" class="js-twmshp__lesson-title" value="<?php echo esc_html($title); ?>"/>
                </div>
            </div>
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="lesson_before_start_<?php echo $id; ?>"><?php _e( 'Days before this lesson will be available', TWM_TD ); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="lesson_before_start_<?php echo $id; ?>" name="lesson_before_start<?php echo $name; ?>" size="25" value="<?= twshp_get_meta_data( $id, 'before_start', 0 ) / 86400; ?>"/>
                </div>
            </div>
            <div class="customEditor custom_upload_buttons">
                <div class="js-twmshp__lesson-editor"><?php echo $content; ?></div>
            </div>
            <div class="js-twmshp__wrapper-quizes ms-quizes-list">
                <?php echo $quizesHtml; ?>
            </div>
            <div class="ms-actions">
                <a class="js-twmshp__lesson-quizes-add button button-primary button-large<?php if ($hasQuiz) { ?> disabled<?php } ?>" href="javascript:void(0);">
                    <span class="ms-icon ms-icon-quiz"></span>
                    <?php _e('Add quiz', TWM_TD); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function twmshp_render_quiz($id, $name, $data, $content, $questionsHtml = '') {
    $title = empty($data['title']) ? '' : $data['title'];
    $threshold = empty($data['threshold']) ? '' : $data['threshold'];
    $certificate = empty($data['certificate']) ? '' : $data['certificate'];
    $retake = empty($data['retake']) ? '' : $data['retake'];

    $allowedRetakes = array(
        'unlimited' => __('As many times as the user wants', TWM_TD),
        'once_per_day' => __('Once per day', TWM_TD),
        'once_per_month' => __('Once per month', TWM_TD)
    );
    ob_start();
    ?>
    <div class="group js-twmshp__quiz-item">
        <h3 class="js-group__label ms-head" data-default="<?php echo esc_html($title); ?>">
            <span class="js-group__label_text"><?php echo esc_html($title); ?></span>
            <a class="js-twmshp__remove-quiz" href="#"><span class="ui-icon ui-icon-trash"></span> <?php _e('Remove quiz', TWM_TD); ?></a>
        </h3>
        <div class="twmshp__quiz-form js-twmshp__quiz-form">
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz<?php echo $id; ?>"><?php _e('Quiz title', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="quiz<?php echo $id; ?>" name="<?php echo $name; ?>[title]" size="25" class="js-twmshp__quiz-title" value="<?php echo esc_html($title); ?>"/>
                </div>
            </div>
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz<?php echo $id; ?>_threshold"><?php _e('Threshold', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="quiz<?php echo $id; ?>_threshold" name="<?php echo $name; ?>[threshold]" size="25" class="ms-small js-twmshp__quiz-threshold" value="<?php echo esc_html($threshold); ?>"/>
                    <span class="ms-text ms-text-light"><?php _e('%', TWM_TD); ?></span>
                </div>
            </div>
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz<?php echo $id; ?>_certificate"><?php _e('Certificate', TWM_TD); ?>:</label>
                <div class="ms-value js-twmshp__image_upload_wrapper">
                    <input type="text" id="quiz<?php echo $id; ?>_certificate" name="<?php echo $name; ?>[certificate]" size="25" class="js-twmshp__quiz-certificate js-twmshp__image_upload_value" value="<?php echo esc_html($certificate); ?>"/>
                    <input type="button" class="js-twmshp__image_upload_button button" value="<?php _e('Upload image', TWM_TD); ?>" />
                </div>
            </div>
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz<?php echo $id; ?>_retake"><?php _e('Retake', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select id="quiz<?php echo $id; ?>_retake" name="<?php echo $name; ?>[retake]">
                        <?php foreach ($allowedRetakes as $retakeValue => $retakeTitle) { ?>
                            <option value="<?php echo esc_attr($retakeValue); ?>"<?php if ($retakeValue === $retake) { ?> selected="selected"<?php } ?>><?php echo esc_html($retakeTitle); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="customEditor custom_upload_buttons">
                <div class="js-twmshp__quiz-editor"><?php echo $content; ?></div>
            </div>
            <div class="js-twmshp__wrapper-quiz-questions ms-quiz-questions-list">
                <?php echo $questionsHtml; ?>
            </div>
            <div class="ms-actions">
                <a class="js-twmshp__quiz-questions-add button button-primary button-large" href="javascript:void(0);">
                    <span class="ms-icon ms-icon-question"></span>
                    <?php _e('Add question', TWM_TD); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function twmshp_render_quiz_question_answer($id, $answer_id, $name, $data, $name_correct, $data_correct, $question_type) {
    $value = empty($data['value']) ? '' : $data['value'];
    $image = empty($data['image']) ? '' : $data['image'];
    ob_start();
    ?>
    <div class="ms-field ms-field-large js-twmshp__quiz-question-answer-item">
        <label class="ms-label" for="quiz_question_answer<?php echo $id; ?>_value"><?php _e('Answer', TWM_TD); ?>:</label>
        <div class="ms-value">
            <div<?php if ($question_type !== 'single' && $question_type !== 'multi') { ?> style="display: none;" <?php } ?> class="js-twmshp__quiz-question-answer_type js-twmshp__quiz-question-answer_type_single js-twmshp__quiz-question-answer_type_multi">
                <input type="text" id="quiz_question_answer<?php echo $id; ?>_value" name="<?php echo $name; ?>[value]" size="25" value="<?php echo esc_attr($value); ?>"/>
                <a class="js-twmshp__remove-quiz-question-answer" href="#"><span class="ui-icon ui-icon-closethick"></span></a>
            </div>
            <div<?php if ($question_type !== 'single_image' && $question_type !== 'multi_image') { ?> style="display: none;" <?php } ?> class="js-twmshp__quiz-question-answer_type js-twmshp__quiz-question-answer_type_single_image js-twmshp__quiz-question-answer_type_multi_image js-twmshp__image_upload_wrapper">
                <input type="text" class="js-twmshp__image_upload_value" id="quiz_question_answer<?php echo $id; ?>_image" name="<?php echo $name; ?>[image]" size="25" value="<?php echo esc_attr($image); ?>"/>
                <input type="button" class="js-twmshp__image_upload_button button" value="<?php _e('Upload image', TWM_TD); ?>" />
                <a class="js-twmshp__remove-quiz-question-answer" href="#"><span class="ui-icon ui-icon-closethick"></span></a>
            </div>
            <div>
                <label><input type="<?php if ($question_type === 'single' || $question_type === 'single_image') { ?>radio<?php } else { ?>checkbox<?php } ?>" name="<?php echo $name_correct; ?>" class="js-twmshp__quiz-question-answer_correct" value="<?php echo esc_attr($answer_id); ?>"<?php if (in_array($answer_id, $data_correct)) { ?> checked="checked"<?php } ?>> <?php _e('Correct answer', TWM_TD); ?></label>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function twmshp_render_quiz_question($id, $name, $data, $answersHtml = '') {
    $title = empty($data['title']) ? '' : $data['title'];
    $type = empty($data['type']) ? '' : $data['type'];
    $allowedTypes = array(
        'single' => __('One answer', TWM_TD),
        'single_image' => __('One answer with image', TWM_TD),
        'multi' => __('Multiple answers', TWM_TD),
        'multi_image' => __('Multiple answers with image', TWM_TD)
    );
    ob_start();
    ?>
    <div class="group js-twmshp__quiz-question-item" data-question-id="<?php echo esc_attr($id); ?>">
        <h3 class="js-group__label ms-head" data-default="<?php echo esc_html($title); ?>">
            <span class="js-group__label_text"><?php echo esc_html($title); ?></span>
            <a class="js-twmshp__remove-quiz-question" href="#"><span class="ui-icon ui-icon-trash"></span> <?php _e('Remove question', TWM_TD); ?></a>
        </h3>
        <div class="twmshp__quiz-question-form js-twmshp__quiz-question-form">
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz_question<?php echo $id; ?>_text"><?php _e('Question text', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="quiz_question<?php echo $id; ?>_text" name="<?php echo $name; ?>[title]" size="25" class="js-twmshp__quiz-question-title" value="<?php echo esc_attr($title); ?>"/>
                </div>
            </div>
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="quiz_question<?php echo $id; ?>_type"><?php _e('Type of answer', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select id="quiz_question<?php echo $id; ?>_type" name="<?php echo $name; ?>[type]" class="js-twmshp__quiz-question-type">
                        <?php foreach ($allowedTypes as $typeValue => $typeTitle) { ?>
                            <option value="<?php echo esc_attr($typeValue); ?>"<?php if ($type == $typeValue) { ?> selected="selected"<?php } ?>><?php echo esc_html($typeTitle); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="js-twmshp__wrapper-quiz-question-answers ms-quiz-question-answers-list">
                <?php echo $answersHtml; ?>
            </div>
            <div class="ms-actions js-twmshp__quiz-question-answers-actions">
                <a class="js-twmshp__quiz-question-answers-add button button-primary button-large" href="javascript:void(0);">
                    <?php _e('Add answer', TWM_TD); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function twmshp_render_modules($id, $title, $description = '', $image = '', $lessonsHtml = '') {
    ob_start();
    ?>
    <div class="group js-twmshp__modules-item" data-module-id="<?php echo $id; ?>">
        <h3 class="js-group__label js-twmshp__modules-item_title ms-head" data-default="<?php echo esc_html($title); ?>">
            <span class="js-group__label_text"><?php echo esc_html($title); ?></span>
            <a class="js-twmshp__remove-module" href="#"><span class="ui-icon ui-icon-trash"></span> <?php _e('Remove module', TWM_TD); ?></a>
        </h3>

        <div class="js-twmshp__modules-item_wrapper">
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="module_title_<?php echo $id; ?>"><?php _e('Module title', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input size="25" type="text" id="module_title_<?php echo $id; ?>" name="module_title[<?php echo $id; ?>]" class="js-twmshp__modules-item-title" value="<?php echo esc_html($title); ?>"/>
                </div>
            </div>

            <div class="ms-field ms-field-large">
                <label class="ms-label" for="module_before_start_<?php echo $id; ?>"><?php _e( 'Days before this module will be available', TWM_TD ); ?>:</label>
                <div class="ms-value">
                    <input type="text" id="module_before_start_<?php echo $id; ?>" name="module_before_start[<?php echo $id; ?>]" size="25" value="<?= twshp_get_meta_data( $id, 'before_start', 0 ) / 86400; ?>"/>
                </div>
            </div>

            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element js-twm-tooltip-element"
                       for="module_image_<?php echo $id; ?>"
                       data-tooltip="<?php _e('Fill in an overview image for the module here. Optimal size 000x000 Pixel', TWM_TD); ?>"
                       ><?php _e('Module image', TWM_TD); ?>:</label>
                <div class="ms-value js-twmshp__image_upload_wrapper">
                    <input type="text" size="25" id="module_image_<?php echo $id; ?>" name="module_image[<?php echo $id; ?>]" class="js-twmshp__image_upload_value" value="<?php echo $image; ?>" />
                    <span class="ms-text"><?php _e('OR', TWM_TD); ?></span>
                    <input type="button" class="js-twmshp__image_upload_button button" value="<?php esc_attr_e('Upload image', TWM_TD); ?>" />
                </div>
            </div>
<!--
            <div class="ms-field">
                <label class="ms-label ms-label-wide"><?php _e('Module description', TWM_TD); ?>:</label>
                <?php echo twmshp_render_editor($description, 'module_description[' . $id . ']', 'module_description_' . $id) ?>
            </div>
-->
            <div class="ms-label ms-label-wide"><?php _e('Lessons', TWM_TD); ?>:</div>

            <div class="js-twmshp__modules-item_wrapper-lessons ms-lessons-list">
                <?php echo $lessonsHtml; ?>
            </div>
            <div class="ms-actions">
                <a class="js-twmshp__modules-lessons-add button button-primary button-large" href="javascript:void(0);">
                    <span class="ms-icon ms-icon-lesson"></span>
                    <?php _e('Add lesson', TWM_TD); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

    /**
     * get value from dashboard options
     * 
     * @param  string $name
     *  
     * @return string
     *
     * @since 1.0.0.0
     * @since 1.0.18.6
     * 
     */
    function twmshp_get_option( $name ) 
    {
        return MemberWunder\Controller\Options::get_option( $name );
    }

/**
 * @return string
 */
function twmshp_get_template() {
    return twmshp_get_option( 'template' );
}

/**
 * @return string
 */
function twmshp_get_baseurl() {
    $baseurl = trim( twmshp_get_option( 'baseurl' ), '/' );
    $baseurl = $baseurl ? '/' . $baseurl . '/' : '/';
    return $baseurl;
}

/**
 * @return string
 */
function twmshp_get_page_url($path) {
    if (get_option('permalink_structure')) {
        return home_url(twmshp_get_baseurl() . ltrim($path, '/'));
    }
    return add_query_arg(
        array('twm_path' => urlencode($path)),
        home_url('/')
    );
}

/**
 * @return string
 */
function twmshp_get_dashboard_url() {
    return twmshp_get_page_url('/');
}

/**
 * @return string
 */
function twmshp_get_courses_url() {
    return twmshp_get_page_url('/courses/');
}

/**
 * @return string
 */
function twmshp_get_impressum_url() {
    return twmshp_get_page_url('/impressum/');
}

/**
 * @return string
 */
function twmshp_get_agb_url() {
    return twmshp_get_page_url('/agb/');
}

/**
 * @return string
 */
function twmshp_get_profile_url() {
    return twmshp_get_page_url('/profile/');
}

/**
 * @return string
 */
function twmshp_get_lostpassword_url() {
    return twmshp_get_page_url('/lostpassword/');
}

/**
 * @return string
 */
function twmshp_get_register_url() {
    return twmshp_get_page_url('/register/');
}

/**
 * @return string
 */
function twmshp_get_reset_url() {
    return twmshp_get_page_url('/reset/');
}

/**
 * @param int|\WP_Post $lesson_id
 * @return string|void
 */
function twmshp_get_lesson_quiz_url($lesson_id) {
    if (!get_option('permalink_structure')) {
        return add_query_arg(
            array('twm_action' => 'quiz'),
            get_permalink($lesson_id)
        );
    }
    $url = rtrim(get_permalink($lesson_id),'/') . '/quiz/';
    return $url;
}

/**
 * @param \WP_Post|int $post_id
 * @return null|\WP_Post
 */
function twmshp_get_course_by_post($post_id) {
    if ($post_id instanceof \WP_Post) {
        $post_id = $post_id->ID;
    }

    $course_id = get_post_meta($post_id, 'course_id', true);
    if (!$course_id) {
        return null;
    }
    return get_post($course_id);
}

/**
 * @param \WP_Post|int $course_id
 * @return array
 */
function twmshp_get_course_info($course_id) {
    if ($course_id instanceof \WP_Post) {
        $course_id = $course_id->ID;
    }
    $data = get_post_meta($course_id, 'twm_info', true);
    if (!is_array($data)) {
        $data = array();
    }
    return $data;
}

/**
 * @param array $args
 * @return array
 */
function twmshp_get_courses($args = array()) {
    $args = array(
        'numberposts' => -1,
        'no_found_rows' => true,
        'orderby' => 'menu_order',
        'order' => 'asc',
        'post_type' => TWM_COURSE_TYPE
    ) + $args;

    $data = get_posts($args);

    return $data;
}

/**
 * @param int|WP_Post|null $course_id
 * @return WP_Post|null
 */
function twmshp_get_course($course_id) {
    if (!$course_id) {
        return null;
    }
    $course = get_post($course_id);
    if (!$course || $course->post_type != TWM_COURSE_TYPE) {
        return null;
    }
    return $course;
}

/**
 * get label/lables for course price period
 * 
 * @param  string $key 
 * 
 * @return string    
 *
 * @since  1.0.28
 * 
 */
function twmshp_get_course_price_period_label( $key = '' )
{
    $data = array( 'onetime' => __( 'Onetime', TWM_TD ), 'yearly' => __( 'Yearly', TWM_TD ), 'monthly' => __( 'Monthly', TWM_TD ) );
    
    return empty( $key ) ? $data : $data[ $key ];
}

/**
 * @param int|WP_Post|null $course_id
 * @param string|null $type
 *
 * @return string
 */
function twmshp_get_course_price_formatted($course_id, $type = null, $purchased_course_ids = array()) {
    if ($course_id instanceof \WP_Post) {
        $course_id = $course_id->ID;
    }

    $info = twmshp_get_course_info($course_id);
    if (!$info) {
        return '';
    }

    $course_currency = empty($info['currency']) ? '' : $info['currency'];
    $course_period = twmshp_get_course_price_period_label( empty($info['payment_period']) ? 'onetime' : $info['payment_period'] );
    $course_price = empty($info['price']) ? 0.0 : (float) $info['price'];
    $course_tax_display_options = empty($info['tax_display_options']) ? 'hide' : $info['tax_display_options'];
    $course_price_display_options_bp = empty($info['price_display_options_bp']) ? 'all' : $info['price_display_options_bp'];
    $course_price_display_options_ap = empty($info['price_display_options_ap']) ? 'hide' : $info['price_display_options_ap'];

    $purchased = in_array($course_id, $purchased_course_ids);

    if (
        $type === null ||
        (!$purchased && ($course_price_display_options_bp === 'all' || $course_price_display_options_bp === $type)) ||
        ($purchased && ($course_price_display_options_ap === 'all' || $course_price_display_options_ap === $type))
    ) {
        if ($course_price) {
            $course_price_formatted = sprintf( \MemberWunder\Controller\Options\Currency::get_currency_format( $course_currency ), number_format( $course_price, 2, ',', '' ) );
            if ($course_tax_display_options === 'before') {
                $course_price_formatted .= ' ' . __('netto', TWM_TD);
            } elseif ($course_tax_display_options === 'after') {
                $course_price_formatted .= ' ' . __('brutto', TWM_TD);
            }
            $course_price_formatted .= ' '.$course_period;
        } else {
            $course_price_formatted = __('Free', TWM_TD);
        }
    } else {
        $course_price_formatted = '';
    }

    return $course_price_formatted;
}

/**
 * @param \WP_Post|int $post_id
 * @return array|null|\WP_Post
 */
function twmshp_get_module_by_post($post_id) {
    if ($post_id instanceof \WP_Post) {
        $post_id = $post_id->ID;
    }

    $module_id = get_post_meta($post_id, 'module_id', true);
    if (!$module_id) {
        return null;
    }
    return get_post($module_id);
}

/**
 * @param \WP_Post|int $course_id
 * @return array
 */
function twmshp_get_modules_by_course($course_id) {
    if ($course_id instanceof \WP_Post) {
        $course_id = $course_id->ID;
    }

    $args = array(
        'numberposts' => -1,
        'no_found_rows' => true,
        'orderby' => 'menu_order',
        'order' => 'asc',
        'post_type' => TWM_MODULE_TYPE,
        'meta_query' => array(
            array(
                'key' => 'course_id',
                'value' => (int) $course_id
            )
        )
    );
    $data = get_posts($args);

    return $data;
}

/**
 * @param \WP_Post|int $module_id
 * @return array
 */
function twmshp_get_lessons_by_module($module_id) {
    if ($module_id instanceof \WP_Post) {
        $module_id = $module_id->ID;
    }

    $args = array(
        'numberposts' => -1,
        'no_found_rows' => true,
        'orderby' => 'menu_order',
        'order' => 'asc',
        'post_type' => TWM_LESSONS_TYPE,
        'meta_query' => array(
            array(
                'key' => 'module_id',
                'value' => (int) $module_id
            )
        )
    );
    $data = get_posts($args);

    return $data;
}

/**
 * @param string $url
 * @return string
 */
function twmshp_get_image_url($url) {
    if (!$url) {
        $url = TWM_TEMPLATES_URL . '/public/pict/01.jpg';
    }
    return $url;
}

/**
 * @param int|null $user_id
 * @return string
 */
function twmshp_get_avatar_url($user_id = null) {
    if ($user_id === null) {
        $user_id = get_current_user_id();
    }
    if ($user_id) {
        $attachment_id = get_user_meta($user_id, 'twm_avatar_attachment_id', true);
        if ($attachment_id) {
            $url = wp_get_attachment_image_url($attachment_id, 'twm_avatar');
            if ($url) {
                return $url;
            }
        }
    }
    return get_avatar_url($user_id, array('size' => 300));
}

/**
 * @param int $postId
 * @param array $posts
 * @return \WP_Post|null
 */
function twmshp_get_prev_post($postId, $posts)
{
    $post = null;
    for ($i = 0, $l = count($posts); $i < $l; $i++) {
        if ($posts[$i]->ID == $postId) {
            if ($i > 0) {
                $post = $posts[$i - 1];
            }
            break;
        }
    }
    return $post;
}

/**
 * @param int $postId
 * @param array $posts
 * @return \WP_Post|null
 */
function twmshp_get_next_post($postId, $posts)
{
    $post = null;
    for ($i = 0, $l = count($posts); $i < $l; $i++) {
        if ($posts[$i]->ID == $postId) {
            if ($i < $l - 1) {
                $post = $posts[$i + 1];
            }
            break;
        }
    }
    return $post;
}

function twmshp_users_can_register()
{
    return twmshp_get_option('allow_registration') && get_option('users_can_register');
}

add_filter('the_content', 'twm_return_after_content');

function twm_return_after_content($content) {

    $content = str_replace('"../wp-content/', '"' . get_site_url() . '/wp-content/', $content);

    return $content;
}

add_action('wp_ajax_twm_test_email', 'twm_test_email');

function twm_test_email() {

    header('Content-Type: application/json');

    try {

        $smtp = new \MemberWunder\Services\Smtp();

        $to_email = isset($_REQUEST['email']) ?  $_REQUEST['email']: null;
        $subject = isset($_REQUEST['subject']) ?  $_REQUEST['subject']: null;
        $message = isset($_REQUEST['message']) ?  $_REQUEST['message']: null;
        
        echo json_encode($smtp->twm_smtp_send_test_mail($to_email, $subject, $message));
    } catch (\Exception $e) {
        echo json_encode(array('status' => 'fail'));
    }

    wp_die();
}


function twm_plugin_body_class($classes) {
    
    $template = twmshp_get_template();
    $sm = substr($template, 0, 5) === 'theme' ? substr($template, 5) : 0;
    
    $classes[] = 'theme__sm-'.$sm;
    return $classes;
}

function twm_is_pro()
{
    return in_array( 'tw-membership/tw-membership.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false;
}

add_filter('body_class', 'twm_plugin_body_class');

function getFreeStartedCoursesIds()
{
    $user_id = get_current_user_id();
    $free_courses = get_user_meta( $user_id, 'mw_started_free_courses', true );

    return empty( $free_courses ) ? array() : array_keys( json_decode( $free_courses, true ) );
}

function twshp_check_curs_is_free()
{
    return TRUE;
}