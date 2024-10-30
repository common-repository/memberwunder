<div class="memberwunder-notice <?= isset( $is_ajax ) && $is_ajax ? 'js-memberwunder-notice-ajax' : '';?>" style="display:none;">
  <div class="memberwunder-notice-logo" style="background-image: url('<?= \MemberWunder\Helpers\View::system_image( 'logo', array( 'is_dashboard' => true, 'only_path' => true ) );?>');?>">
  </div>
  <div class="memberwunder-notice-text">
    <?= $text;?>
  </div>
  <?php if( ( isset( $link['href'] ) && isset( $link['text'] ) ) || ( isset( $dismiss ) && $dismiss ) ): ?>
  <div class="memberwunder-notice-actions">
    <?= isset( $link['href'] ) && isset( $link['text'] ) ? '<a href="'.$link['href'].'" class="button-primary">'.$link['text'].( isset( $is_ajax ) && $is_ajax ? ' <img class="memberwunder-notice-actions-loader" src="'.TWM_ASSETS_URL.'/css/images/import-loader.gif" style="display:none;" />' : '' ).'</a>' : '';?>
    <?= isset( $dismiss ) && $dismiss ? '<button data-dismiss-url="'.$dismiss_url.'">'.__( 'Dismiss', TWM_TD ).'</button>' : '';?>
  </div>
  <?php endif;?>
</div>