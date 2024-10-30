<div class="ksv-xs-m30b">
    <a href="<?php echo esc_url(twmshp_get_leaderboard_url()); ?>" class="btn-shadow padh2<?php if ($leaderboard_type !== 'overall') { ?> disabled<?php } ?>"><?php esc_html_e('Overall', TWM_TD); ?></a>
    <a href="<?php echo esc_url(twmshp_get_leaderboard_url('weekly')); ?>" class="btn-shadow padh2<?php if ($leaderboard_type !== 'weekly') { ?> disabled<?php } ?>"><?php esc_html_e('Weekly', TWM_TD); ?></a>
    <a href="<?php echo esc_url(twmshp_get_leaderboard_url('personal')); ?>" class="btn-shadow padh2<?php if ($leaderboard_type !== 'personal') { ?> disabled<?php } ?>"><?php esc_html_e('Personal', TWM_TD); ?></a>
</div>