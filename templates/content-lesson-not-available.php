<div class="row CourceList">
    <div class="col-md-6 col-md-offset-3 text-center">
        <div class="countdown-container">
            <div class="countdown-title">
                <h3><?= sprintf( __( 'The lesson will be available %s', TWM_TD ), '<span class="js-countdown-title-date"></span>' );?></h3>
            </div>

            <div class="countdown countdown--large js-countdown-timer"
                data-timer-labels="<?= urlencode( twsp_labels_for_countdown() );?>"
                data-timer="<?= $mapperLesson->getFullTimeUnLocked(get_the_ID(), $module); ?>"
                data-server-time="<?= strtotime( current_time('mysql', 1) );?>"
                data-timer-end="reload">
            </div>
            
        </div>
    </div>
</div>
