<div class="HtmlText">
    <?php
    the_content();
    ?>
</div>

<div class="lesson__nav">
    <div class="lesson__nav-list">
        <div class="lesson__nav-item">
            <?php if ($lesson_prev) { ?>
                <a class="lesson__nav-link link--prev" href="<?php echo esc_url($lesson_prev); ?>">
                    <i class="icon ion-chevron-left"></i>
                    <span><?php esc_html_e('Return to previous lesson', TWM_TD); ?></span>
                </a>
            <?php } ?>
        </div>
        <div class="lesson__nav-item">
            <?php if (isset($doneLessonIds[get_the_ID()])) { ?><div class="lesson__nav-status"><?php esc_html_e('Lesson done', TWM_TD); ?></div><?php } ?>
        </div>
        <div class="lesson__nav-item">
            <?php if ($lesson_next) { ?>
                <a class="lesson__nav-link link--next" href="<?php echo esc_url($lesson_next); ?>">
                    <span><?php esc_html_e('Move on to the next lesson', TWM_TD); ?></span>
                    <i class="icon ion-chevron-right"></i>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<hr/>

<div class="CoursesProgress">
    <div class="title"><?php esc_html_e('Course progress', TWM_TD); ?></div>
    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $width; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $width; ?>%;">
            <span class="progress-title"><?php echo $width; ?></span>
        </div>
    </div>
</div>