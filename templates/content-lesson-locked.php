<?php
    $prev_module = $mapperLesson->get_prev_module( $module, $course );
?>
<div class="course-details text-center">
    <div class="course-details__image">
        <div class="course-details__icon">
            <i class="ion-locked"></i>
        </div>
    </div>
    <div class="course-details__title">
        <h3><?= sprintf( __( 'You have to finish %s before you can continue with this module.', TWM_TD ), '<a href="'.get_permalink( $prev_module->ID ).'">'.$prev_module->post_title.'</a>' );?></h3>
    </div>
    <div class="course-details__note">
        <a href="<?= get_permalink( $prev_module->ID );?>" class="but trans green"><?= $prev_module->post_title;?> →</a>
    </div>
</div>