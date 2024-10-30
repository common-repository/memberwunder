<?php include __DIR__ . '/layout/header.php'; ?>

<?php
/** @var object $taken_quiz */
/** @var string $quizTitle */
/** @var string $quizRetake */
/** @var bool $canTake */
/** @var \WP_Post $course */
/** @var \WP_Post $module */

$lessons = twmshp_get_lessons_by_module($module);

$lesson_next = null;

for ($i = 0, $l = count($lessons); $i < $l; $i++) {
    if ($lessons[$i]->ID == get_the_ID()) {
        if ($i < $l - 1) {
            $lesson_next = $lessons[$i + 1];
        }
        break;
    }
}

if (!$lesson_next) {
    $lesson_next = $course;
}

$share_link = add_query_arg(array(
    'twm_id' => $taken_quiz->id,
    'twm_checksum' => crc32($taken_quiz->id . ':' . $taken_quiz->lesson_id . ':' . $taken_quiz->user_id)
), twmshp_get_dashboard_url());
?>

<div class="quiz">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="quiz__container">
                    <div class="quiz__box">
                        <div class="quiz__box-content">
                            <div class="quiz__box-suptitle"><?php echo get_the_title($module); ?></div>
                            <div class="quiz__box-title"><?php echo esc_html($quizTitle); ?></div>
                            <div class="quiz__box-result">
                                <div class="quiz__box-intro"><?php if ($taken_quiz->done) { ?><?php _e('You have completed quiz and your result is', TWM_TD); ?><?php } else { ?><?php _e('You have failed quiz. Try again later. Your result is', TWM_TD); ?><?php } ?></div>
                                <div class="quiz__box-total"><?php echo esc_html($taken_quiz->percent); ?>%</div>
                                <?php if ($taken_quiz->done) { ?>
                                <div class="quiz__box-buttons">
                                    <a href="<?php echo esc_url($share_link); ?>" class="button fbShare">
                                        <span class="icon">
                                            <img src="<?php echo TWM_TEMPLATES_URL; ?>/public/css/img/icon-share.png" alt="">
                                        </span>
                                        <?php _e('Share', TWM_TD); ?></a>
                                    <?php if ($quizCertificate) { ?>
                                    <a href="<?php echo esc_url($quizCertificate); ?>" target="_blank" class="button">
                                        <span class="icon">
                                            <img src="<?php echo TWM_TEMPLATES_URL; ?>/public/css/img/icon-download.png" alt="">
                                        </span>
                                        <?php _e('Download', TWM_TD); ?></a>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                                <?php if ($quizRetake === 'once_per_day') { ?>
                                    <div class="quiz__box-warning"><?php printf(__('Warning: You can only redo this quiz after %s', TWM_TD), __('24 hours', TWM_TD)); ?></div>
                                <?php } ?>
                                <?php if ($quizRetake === 'once_per_month') { ?>
                                    <div class="quiz__box-warning"><?php printf(__('Warning: You can only redo this quiz after %s', TWM_TD), __('1 month', TWM_TD)); ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="quiz__buttons">
                            <?php if ($canTake) { ?>
                            <a href="<?php echo wp_nonce_url(twmshp_get_lesson_quiz_url(get_the_ID()), 'retake_' . get_the_ID(), 'action_nonce'); ?>" class="button">
                                <span class="icon">
                                    <img src="<?php echo TWM_TEMPLATES_URL; ?>/public/css/img/icon-reload.png" alt="">
                                </span><?php _e('Retake quiz', TWM_TD); ?></a>
                            <?php } ?>
                            <?php if ($lesson_next) { ?>
                            <a href="<?php echo esc_url(get_permalink($lesson_next)); ?>" class="button">
                                <?php _e('Processed to the next lesson', TWM_TD); ?>
                                <span class="icon">
                                    <img src="<?php echo TWM_TEMPLATES_URL; ?>/public/css/img/icon-right-arrow.png" alt="">
                                </span>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <style>
                    .quiz {

                    }
                    .quiz__container {
                        margin: 40px auto;
                        max-width: 960px;
                    }
                    .quiz__box {
                        text-align: center;
                        background-color: rgb(255, 255, 255);
                        box-shadow: 0px 3px 54px 0px rgba(0, 0, 1, 0.08);
                        width: 100%;
                        height: 100%;
                    }
                    .quiz__box-content {
                        padding: 40px;
                        display: flex;
                        flex-flow: column;
                    }
                    .quiz__box .quiz__box-suptitle {
                        text-transform: uppercase;
                        font-size: 14px;
                    }
                    .quiz__box .quiz__box-title {
                        font-size: 30px;
                        font-weight: 800;
                    }
                    .quiz__box .quiz__box-result {
                        margin: 40px 0;
                    }
                    .quiz__box .quiz__box-intro {
                        color: #797979;
                        font-weight: 200;
                    }
                    .quiz__box .quiz__box-warning {
                        color: #797979;
                        font-weight: 200;
                        margin: 40px 0 0 0;
                    }
                    .quiz__box .quiz__box-total {
                        color: #5175b8;
                        font-weight: 800;
                        font-size: 72px;
                    }
                    .quiz__box .quiz__box-buttons {
                        display: flex;
                        justify-content: center;
                    }
                    .quiz__box-buttons .button {
                        padding: 14px 6px;
                        box-shadow: inset 0 0 0 2px #e4e4e4;
                        text-transform: uppercase;
                        min-width: 200px;
                        color: #353434;
                        letter-spacing: 1px;
                        text-decoration: none;
                        transition: .2s;
                    }
                    .quiz__box-buttons .button:hover {
                        background: #e4e4e4;
                        color: #353434;
                    }
                    .quiz__box-buttons .button:first-child {
                        margin-right: 10px;
                    }
                    @media (max-width: 480px) {
                        .quiz__box .quiz__box-buttons {
                            flex-flow: column;
                        }
                        .quiz__box-buttons .button:first-child {
                            margin-right: 0;
                            margin-bottom: 10px;
                        }
                    }

                    .quiz__box-buttons .button .icon {
                        width: 24px;
                        height: 24px;
                        display: inline-block;
                        margin-right: 4px;
                    }
                    .quiz__buttons {
                        display: flex;
                        justify-content: space-between;
                    }
                    @media (max-width: 480px) {
                        .quiz__buttons {
                            flex-flow: column;
                        }
                    }
                    .quiz__buttons .button {
                        background: tomato;
                        width: 100%;
                        color: white;
                        text-decoration: none;
                        padding: 36px;
                        text-transform: uppercase;
                        font-size: 14px;
                        transition: .2s;
                    }
                    .quiz__buttons .button .icon {
                        width: 24px;
                        height: 24px;
                        display: inline-block;
                        margin: 0 4px;
                    }
                    .quiz__buttons .button:first-child {
                        background: #5175b8;
                    }
                    .quiz__buttons .button:last-child {
                        background: #4ebb74;
                    }
                    .quiz__buttons .button:hover:first-child {
                        background: #3B588F;
                    }
                    .quiz__buttons .button:hover:last-child {
                        background: #338F50;
                    }

                    /* Large desktops and laptops */
                    @media (min-width: 1200px) {

                    }

                    /* Landscape tablets and medium desktops */
                    @media (min-width: 992px) and (max-width: 1199px) {

                    }

                    /* Portrait tablets and small desktops */
                    @media (min-width: 768px) and (max-width: 991px) {

                    }

                    /* Landscape phones and portrait tablets */
                    @media (max-width: 767px) {

                    }

                    /* Portrait phones and smaller */
                    @media (max-width: 480px) {

                    }
                </style>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
