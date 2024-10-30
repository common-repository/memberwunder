<select
  <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> 
  <?= isset( $tooltip ) ? 'data-tooltip="'.$tooltip.'"' : '';?>
  name="<?= $name.( isset( $multiple ) && $multiple ? '[]' : '' );?>" 
  <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
  <?= isset( $multiple ) && $multiple ? 'multiple' : '';?>
  <?= isset( $dependet ) ? 'data-dependet-field="'.$dependet['field'].'" data-dependet-value="'.$dependet['value'].'"' : '';?>
  >
  <?php  
    foreach( $options as $key => $label )
      echo '<option value="'.$key.'" '.( in_array( $key, (array)$value ) ? 'selected' : '' ).'>'.$label.'</option>';
  ?>
</select>