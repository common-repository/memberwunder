<div class="twn-tab-content-fulllink">
  <div class="twn-tab-content-fulllink-row">
    <div class="twn-tab-content-fulllink-cell twn-tab-content-fulllink-value">
      <?= $base;?>
    </div>
    <div class="twn-tab-content-fulllink-cell">
      <input 
        type="text" 
        <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> 
        name="<?= $name;?>" 
        value="<?= $value; ?>" 
        <?= isset( $tooltip ) ? 'data-tooltip="'.$tooltip.'"' : '';?>
        <?= isset( $dependet ) ? 'data-dependet-field="'.$dependet['field'].'" data-dependet-value="'.$dependet['value'].'"' : '';?>
      />
    </div>
  </div>
</div>
<?= isset( $description ) ? '<p class="description">'.$description.'</p>' : '';?>