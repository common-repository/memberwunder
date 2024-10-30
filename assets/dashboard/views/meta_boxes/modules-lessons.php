<input type="hidden" class="js-twmembership_ml" data-tooltip="<?php _e('Here you can fill in the actual content of your courses. If you want to know how it works, visit https://memberwunder.com/hilfe', TWM_TD); ?>">
<script type="text/html" id="twmshp-accordion__module-template">
    <?php echo  twmshp_render_modules('{%module_id%}', __('New module', TWM_TD), '', '', '{%lessons%}'); ?>
</script>
<script type="text/html" id="twmshp-accordion__lesson-template">
    <?php echo  twmshp_render_lesson('{%lesson_id%}', '[{%module_id%}][{%lesson_id%}]', __('New lesson', TWM_TD), '{%editor%}', false, '{%quizes%}'); ?>
</script>
<script type="text/html" id="twmshp-accordion__quiz-template">
    <?php echo  twmshp_render_quiz('{%lesson_id%}', 'lessons_quiz[{%module_id%}][{%lesson_id%}]', array('title' => __('New quiz', TWM_TD), 'threshold' => '80', 'retake' => 'unlimited'), '{%editor%}'); ?>
</script>
<script type="text/html" id="twmshp-accordion__quiz-question-template">
    <?php echo  twmshp_render_quiz_question('{%question_id%}', 'lessons_quiz[{%module_id%}][{%lesson_id%}][questions][{%question_id%}]', array('title' => __('New question', TWM_TD), 'type' => 'text')); ?>
</script>
<script type="text/html" id="twmshp-accordion__quiz-question-answer-template">
    <?php echo  twmshp_render_quiz_question_answer('{%question_id%}_{%answer_id%}', '{%answer_id%}', 'lessons_quiz[{%module_id%}][{%lesson_id%}][questions][{%question_id%}][answers][{%answer_id%}]', array(), 'lessons_quiz[{%module_id%}][{%lesson_id%}][questions][{%question_id%}][answers_correct][]', array(), 'text'); ?>
</script>
<script type="text/html" id="twmshp-editor">
   <?php echo  twmshp_render_editor('', '{%name%}','{%id%}'); ?>
</script>

<div class="js-twmshp__modules ms-metabox-modules-lessons">
    <?php 
    if (!empty($template_vars)) { 
        foreach ($template_vars->modules as $module) {
            $lessonsHtml = '';
            if(!empty($module->lessons)) {
                foreach ($module->lessons as $lesson) {
                    $lesson_editor_name = 'lessons_content['.$module->ID.']['.$lesson->ID.']';
                    $lesson_editor_id = 'lesson_editor_'.$lesson->ID;
                    $editor = twmshp_render_editor($lesson->post_content, $lesson_editor_name, $lesson_editor_id);

                    $quizesHtml = '';

                    $quiz = get_post_meta($lesson->ID, 'quiz', true);
                    if ($quiz) {
                        $questionsHtml = '';
                        if (!empty($quiz['questions']) && is_array($quiz['questions'])) {
                            foreach ($quiz['questions'] as $i => $question) {
                                $question_type = empty($question['type']) ? 'text' : (string)$question['type'];
                                $answersHtml = '';
                                if (!empty($question['answers']) && is_array($question['answers'])) {
                                    $answers_correct = empty($question['answers_correct']) ? array() : (array)$question['answers_correct'];
                                    foreach ($question['answers'] as $j => $answer) {
                                        $answersHtml .= twmshp_render_quiz_question_answer(
                                            $i . '_' . $j,
                                            $j,
                                            'lessons_quiz['.$module->ID.']['.$lesson->ID.'][questions][' . $i . '][answers][' . $j . ']',
                                            $answer,
                                            'lessons_quiz['.$module->ID.']['.$lesson->ID.'][questions][' . $i . '][answers_correct][]',
                                            $answers_correct,
                                            $question_type
                                        );
                                    }
                                }
                                $questionsHtml .= twmshp_render_quiz_question($i, 'lessons_quiz['.$module->ID.']['.$lesson->ID.'][questions][' . $i . ']', $question, $answersHtml);
                            }
                        }

                        $quizEditor = twmshp_render_editor(empty($quiz['content']) ? '' : $quiz['content'], 'lessons_quiz['.$module->ID.']['.$lesson->ID.'][content]', 'lesson_quiz_editor_'.$lesson->ID);
                        $quizesHtml .= twmshp_render_quiz($lesson->ID,'lessons_quiz['.$module->ID.']['.$lesson->ID.']', $quiz, $quizEditor, $questionsHtml);
                    }
                    $lessonsHtml .= twmshp_render_lesson($lesson->ID,'['.$module->ID.']['.$lesson->ID.']',$lesson->post_title, $editor, !!$quiz, $quizesHtml);
                }
            }
            $image = get_post_meta( $module->ID, 'image', true );
            
            echo twmshp_render_modules($module->ID, $module->post_title, $module->post_content, $image, $lessonsHtml);
         } 
    } 
    ?>
</div>
<div class="ms-actions">
    <a class="js-twmshp__modules-add button button-primary button-large" href="javascript:void(0);">
        <span class="ms-icon ms-icon-module"></span>
        <?php _e('Add module', TWM_TD); ?>
    </a>
</div>