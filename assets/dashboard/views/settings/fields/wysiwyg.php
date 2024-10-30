<input 
  type="hidden" 
  <?= isset( $tooltip ) ? 'data-tooltip="'.$tooltip.'"' : '';?>
  <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> 
  <?= isset( $id ) ? 'id="'.$id.'"' : '';?>
/>
<?php
  wp_editor(
              $value, 
              \MemberWunder\Controller\Options::OPTION_NAME.'-'.$id, 
              array_merge( array( 'textarea_name' => $name ), isset( $wysiwyg ) ? $wysiwyg : array() )
          );
?>
<?= isset( $description ) ? '<br/><div class="ms-desc ms-desc-wide">'.$description.'</div>' : '';?>