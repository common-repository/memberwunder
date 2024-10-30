<?php include __DIR__ . '/layout/header.php'; ?>

<?php
/** @var string $quizRetake */
/** @var string $quizContent */
/** @var array $quizQuestions */
/** @var \WP_Post $course */
/** @var \WP_Post $module */
?>

<div class="TopImgBlock">
    <?php $module_thumbnail_url = twmshp_get_image_url(get_post_meta($module->ID, 'image', true)); ?>
    <div class="bg" style="background-image: url('<?php echo esc_attr($module_thumbnail_url); ?>')"></div>
    <div class="container ksv-sm-m30b">
        <div class="row">
            <div class="col-sm-6 ksv-xsi-tc">
                <h3 class="fntWN"><?php echo get_the_title($module); ?></h3>
                <h1><?php echo esc_html($quizTitle); ?></h1>
            </div>
            <div class="col-sm-6 ksv-xs-tc ksv-sm-tr ksv-sm-p30t">
                <a href="<?php the_permalink(); ?>" class=""><i class="fa fa-chevron-left" aria-hidden="true"></i> <?php _e('Back to the lesson', TWM_TD); ?></a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="Quiz">
                <form method="post" action="<?php echo esc_url(twmshp_get_lesson_quiz_url(get_the_ID())); ?>">
                    <?php wp_nonce_field('send_' . get_the_ID(), 'quiz_nonce'); ?>
                    <?php if ($quizContent) { ?>
                        <div class="row question">
                            <?php echo apply_filters('the_content', $quizContent); ?>
                        </div>
                    <?php } ?>
                    <?php if ($quizQuestions) { ?>

                        <?php $n = 1; foreach ($quizQuestions as $questionIndex => $question) { ?>
                            <?php
                            $questionTitle = empty($question['title']) ? '' : (string)$question['title'];
                            $questionType = empty($question['type']) ? '' : (string)$question['type'];
                            $questionAnswers = empty($question['answers']) ? array() : (array)$question['answers'];
                            $questionAnswersCorrect = empty($question['answers_correct']) ? array() : (array)$question['answers_correct'];
                            ?>
                            <div class="row question">
                                <div class="col-xs-12 item text"><?php echo esc_html($n); ?>. <?php echo esc_html($questionTitle); ?></div>
                                <?php foreach ($questionAnswers as $answerIndex => $answer) { ?>
                                    <?php $answerValue = empty($answer['value']) ? '' : (string)$answer['value']; ?>
                                    <?php $answerImage = empty($answer['image']) ? '' : (string)$answer['image']; ?>
                                    <?php if ($questionType === 'single' || $questionType === 'multi') { ?>
                                        <label class="col-xs-12 item answer">
                                            <input name="answers[<?php echo esc_attr($questionIndex); ?>][]" value="<?php echo esc_attr($answerIndex); ?>" type="<?php if ($questionType === 'single') { ?>radio<?php } else { ?>checkbox<?php } ?>" /><span class="checked"></span>
                                            <span><?php echo esc_html($answerValue); ?></span>
                                        </label>
                                    <?php } else { ?>
                                        <label class="col-sm-4 item answer">
                                            <?php if ($answerImage) { ?>
                                                <img class="img-responsive img" src="<?php echo esc_url($answerImage); ?>" />
                                            <?php } ?>
                                            <input name="answers[<?php echo esc_attr($questionIndex); ?>][]" value="<?php echo esc_attr($answerIndex); ?>" type="<?php if ($questionType === 'single_image') { ?>radio<?php } else { ?>checkbox<?php } ?>" /><span class="checked"></span>
                                        </label>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <?php $n++; } ?>
                        <div class="ksv-xs-m15 FileButton text-center">
                            <a href="<?php the_permalink(); ?>" class="but blue ksv-xs-m15b" style="vertical-align: top;"><?php _e('Skip quiz', TWM_TD); ?><span class="sm"><?php _e('(continue later)', TWM_TD); ?></span></a>
                            <button class="but green ksv-xs-m15b" style="vertical-align: top;"><?php _e('Confirm answers', TWM_TD); ?><span class="sm"><?php _e('(and see results)', TWM_TD); ?></span></button>
                        </div>
                        <?php if ($quizRetake === 'once_per_day') { ?>
                            <div class="text-center"><?php printf(__('Warning: You can only redo this quiz after %s', TWM_TD), __('24 hours', TWM_TD)); ?></div>
                        <?php } ?>
                        <?php if ($quizRetake === 'once_per_month') { ?>
                            <div class="text-center"><?php printf(__('Warning: You can only redo this quiz after %s', TWM_TD), __('1 month', TWM_TD)); ?></div>
                        <?php } ?>
                    <?php } ?>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
