<div id="posttype-<?= $field_id;?>" class="posttypediv">
  <div id="tabs-panel-wishlist-<?= $field_id;?>" class="tabs-panel tabs-panel-active">
    <ul id ="wishlist-<?= $field_id;?>-checklist" class="categorychecklist form-no-clear">
      <?php 
        foreach( $pages as $key => $page ): 
          if( $page['menu-item-status'] != 'publish' )
            continue;
      ?>
      <li>
        <label class="menu-item-title">
          <input type="checkbox" class="menu-item-checkbox" name="menu-item[-<?= ( $key + 1 );?>][menu-item-object-id]" value="0"> <?= $page['menu-item-title'];?>
        </label>
        <input type="hidden" class="menu-item-type" name="menu-item[-<?= ( $key + 1 );?>][menu-item-type]" value="custom">
        <input type="hidden" class="menu-item-title" name="menu-item[-<?= ( $key + 1 );?>][menu-item-title]" value="<?= $page['menu-item-title'];?>">
        <input type="hidden" class="menu-item-url" name="menu-item[-<?= ( $key + 1 );?>][menu-item-url]" value="<?= $page['menu-item-url'];?>">
        <input type="hidden" class="menu-item-classes" name="menu-item[-<?= ( $key + 1 );?>][menu-item-classes]" value="<?= $field_id;?>-pop">
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <p class="button-controls wp-clearfix">
    <span class="add-to-menu">
      <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', TWM_TD ); ?>" name="add-<?= $field_id;?>-menu-item" id="submit-<?= $field_id;?>" />
      <span class="spinner"></span>
    </span>
  </p>
</div>