<textarea 
  <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> 
  name="<?= $name;?>" 
  <?= isset( $tooltip ) ? 'data-tooltip="'.$tooltip.'"' : '';?>
  <?= isset( $cols ) ? 'cols="'.$cols.'"' : '';?>
  <?= isset( $rows ) ? 'rows="'.$rows.'"' : '';?>
  <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
  <?= isset( $dependet ) ? 'data-dependet-field="'.$dependet['field'].'" data-dependet-value="'.$dependet['value'].'"' : '';?>
><?= $value; ?></textarea>
<?= isset( $description ) ? '<p class="description">'.$description.'</p>' : '';?>