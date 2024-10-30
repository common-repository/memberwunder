<?php include __DIR__ . '/layout/header-1.php'; ?>

<?php
/** @var \WP_Post|null $lesson_prev */
/** @var \WP_Post|null $lesson_next */
?>

<div class="WM_Overlay">
    <div class="ModalWindow">
        <a href="<?php the_permalink(); ?>" class="wm_close"></a>
        <div class="cntr">
            <div class=" ksv-xs-m30t">
                <img src="<?php echo TWM_TEMPLATES_URL; ?>/public/css/img/ok.png" />
            </div>
            <div class=" ksv-xs-m30">
                <h1><?php _e('CONGRATULATIONS', TWM_TD); ?></h1>
                <div class="comment"><?php printf(__('You have completed the lesson &quot;%s&quot;', TWM_TD), get_the_title()); ?></div>
            </div>
            <div class="MobNavPanel">
                <?php if ($lesson_prev) { ?>
                    <a class="prev" href="<?php echo esc_url($lesson_prev); ?>">
                        <i class="ion-chevron-left"></i>
                        <span><?php esc_html_e('Return to previous lesson', TWM_TD); ?></span>
                    </a>
                <?php } ?>
                <?php if ($lesson_next) { ?>
                    <a class="next" href="<?php echo esc_url($lesson_next); ?>">
                        <span><?php esc_html_e('Move on to the next lesson', TWM_TD); ?></span>
                        <i class="ion-chevron-right"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer-1.php'; ?>