<input 
  name="<?= $name;?>" 
  value="0" 
  type="hidden"  
/>
<input 
  name="<?= $name;?>" 
  value="1" 
  type="checkbox"
  <?= $value ? 'checked="checked"' : '';?>
  <?= isset( $tooltip ) ? 'data-tooltip="' . esc_attr( $tooltip ) . '"' : '';?>
  <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
  <?= isset( $dependet ) ? 'data-dependet-field="'.$dependet['field'].'" data-dependet-value="'.$dependet['value'].'"' : '';?>
/>
                