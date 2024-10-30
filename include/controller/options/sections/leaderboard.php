<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Leaderboard extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'leaderboard';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 25;

      /**
       * get label for section
       * 
       * @return string
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_label() 
      {
        return __( 'Leaderboard', TWM_TD );
      }

      /**
       * return array of fields
       * 
       * @return array
       *
       * @since  1.0.34.2
       * 
       */
      protected static function fields()
      {
        return array(
                    array(
                          'key'     =>  'leaderboard_allow_leaderboard',
                          'label'   =>  __( 'Allow leaderboard', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'checkbox',
                                            'for_pro'       =>  TRUE,
                                        ),
                          'default' =>  TRUE,
                        ),
                    array(
                          'key'     =>  'leaderboard_uploaded_avatar',
                          'label'   =>  __( 'Uploading a profile picture', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'class'         =>  'js-twm-tooltip', 
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                            'for_pro'       =>  TRUE,
                                            'tooltip'       =>  __( 'Choose a number of points to be given to your subscribers after uploading a profile picture.', TWM_TD )
                                        ),
                          'default' =>  5,
                        ),
                    array(
                          'key'     =>  'leaderboard_started_first_course',
                          'label'   =>  __( 'Starting the first course', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  20,
                        ),
                    array(
                          'key'     =>  'leaderboard_started_course',
                          'label'   =>  __( 'Starting a course', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'class'         =>  'js-twm-tooltip', 
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                            'tooltip'       =>  __( 'Choose a number of points to be given to your subscribers after starting a course.', TWM_TD )
                                        ),
                          'default' =>  10,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_first_lesson',
                          'label'   =>  __( 'Finishing the first lesson', TWM_TD ),
                          'attr'    =>  array(  
                                            'type'          =>  'text',
                                            'class'         => 'js-twm-tooltip', 
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                            'tooltip'       => __( 'Choose a number of points to be given to your subscribers after finishing their first lesson.', TWM_TD )
                                        ),
                          'default' =>  5,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_lesson',
                          'label'   =>  __( 'Finishing a lesson', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  1,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_first_module',
                          'label'   =>  __( 'Finishing the first module', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'class'         => 'js-twm-tooltip', 
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                            'tooltip'       => __( 'Choose a number of points to be given to your subscribers after finishing a module.', TWM_TD )
                                        ),
                          'default' =>  20,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_module',
                          'label'   =>  __( 'Finishing a module', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  10,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_first_quiz',
                          'label'   =>  __( 'Finishing the first quiz', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  20,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_quiz',
                          'label'   =>  __( 'Finishing a quiz', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'class'         => 'js-twm-tooltip', 
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                            'tooltip'       => __( 'Choose a number of points to be given to your subscribers after finishing a quiz.', TWM_TD )
                                        ),
                          'default' =>  10,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_first_course',
                          'label'   =>  __( 'Finishing the first course', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  50,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_second_course',
                          'label'   =>  __( 'Finishing the second course', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'for_pro'       =>  TRUE,
                                            'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                        ),
                          'default' =>  40,
                        ),
                    array(
                          'key'     =>  'leaderboard_finished_course',
                          'label'   =>  __( 'Finishing a course', TWM_TD ),
                          'attr'    =>  array(
                                              'type'        =>  'text',
                                              'class'       =>  'js-twm-tooltip', 
                                              'for_pro'       =>  TRUE,
                                              'dependet'      =>  array(
                                                                    'field' =>  'leaderboard_allow_leaderboard',
                                                                    'value' =>  1
                                                                    ),
                                              'tooltip'     =>  __( 'Choose a number of points to be given to your subscribers after finishing an entire course.', TWM_TD )
                                        ),
                          'default' =>  30,
                        ),
                    );
      }
    }