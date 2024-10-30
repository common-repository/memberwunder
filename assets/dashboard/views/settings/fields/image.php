<div class="js-twmshp__image_upload_wrapper" >
  <input 
    type="text" 
    name="<?= $name;?>" 
    <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?>  
    value="<?= $value; ?>" 
    <?= isset( $tooltip ) ? 'data-tooltip="' . esc_attr( $tooltip ) . '" ' : '';?>
    <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
  />
  <input 
    type="button" 
    class="js-twmshp__image_upload_button button" 
    value="<?= esc_attr( __( 'Upload image', TWM_TD ) );?>" 
  />
</div>