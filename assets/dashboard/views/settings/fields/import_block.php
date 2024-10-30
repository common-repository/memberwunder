<?php
  if( !\MemberWunder\Helpers\General::is_upload_enable_to_write() ):
    _e( 'You need to make folder uploads writable before you can import data. See <a href="https://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.', TWM_TD );
    return;
  endif;
?>
<div class="import-block" data-url="<?= \MemberWunder\Helpers\ImportExport::ajax_url();?>">
  <div>
    <input type="file" name="<?= \MemberWunder\Helpers\ImportExport::field_name();?>" />
    <button data-start-loading="<?= __( 'Importing', TWM_TD );?>" name="import-submit" class="button button-primary"?>
      <span><?= _e( 'Start import', TWM_TD );?>&nbsp;</span>
      <img src="<?= TWM_ASSETS_URL.'/css/images/import-loader.gif';?>" style="display:none;" />
    </button>
  </div>  
    <?php 
      $max_upload_size = wp_max_upload_size();
      if ( !$max_upload_size )
        $max_upload_size = 0; 
    ?>
    <p class="max-upload-size">
      <?= sprintf( __( 'Maximum upload file size: %s', TWM_TD ), esc_html( size_format( $max_upload_size ) ) ); ?>
      <br/>
      <?= __( 'Loading can take a few minutes.', TWM_TD );?>
    </p>
</div>


