<input 
  type="text" 
  <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> 
  name="<?= $name;?>" 
  value="<?= $value; ?>" 
  <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
  <?= isset( $tooltip ) ? 'data-tooltip="'.$tooltip.'"' : '';?>
  <?= isset( $dependet ) ? 'data-dependet-field="'.$dependet['field'].'" data-dependet-value="'.$dependet['value'].'"' : '';?>
/>
<?= isset( $description ) ? '<p class="description">'.$description.'</p>' : '';?>