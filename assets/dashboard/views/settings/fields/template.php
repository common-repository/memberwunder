<input 
  type="hidden" 
  <?= isset( $tooltip ) ? ' data-tooltip="' . esc_attr( $tooltip ) . '"' : '';?>
  id="<?= $name;?>" 
/>
<div class="ms-theme-selector">
  <?php foreach( \MemberWunder\Controller\Options\Sections\Design::$themes as $i => $tpl ): ?>
    <div class="ms-theme <?= !twm_is_pro() && isset( $tpl[2] ) && $tpl[2] ? 'for_memberwunder_pro' : '';?>">
      <input 
        class="js-twmshp__settings_template" 
        id="ms_<?= esc_attr( $tpl[0] ); ?>" 
        type="radio" value="<?= esc_attr( $tpl[0] ); ?>" 
        name="<?= MemberWunder\Controller\Options::OPTION_NAME; ?>[template]"
        <?= MemberWunder\Controller\Options::$current_template === $tpl[0] ? 'checked="checked"' : ''; ?> 
      />
      <label 
        for="ms_<?= esc_attr( $tpl[0] ); ?>" 
        class="ms-theme-screenshot"
      >
        <?= $tpl[0] === 'custom' ? '<span>'.__( 'custom', TWM_TD ).'</span>' : ''; ?>
        <?= $i >= count( \MemberWunder\Controller\Options\Sections\Design::$themes ) - 1 ? '<div class="img"></div>' : '<img src="'.esc_url( TWM_ASSETS_URL . '/css/images/themes/' . $i . '.jpg' ).'" alt="" style="background-color: '.$tpl[1].'" />';?>
      </label>
    </div>
  <?php endforeach; ?>
</div>
<div class="card js-twmshp__settings_colors" <?= MemberWunder\Controller\Options::$current_template !== 'custom' ? 'style="display: none;"' : '';?>>
  <table class="form-table">
    <?php
      foreach( \MemberWunder\Controller\Options\Colors::default_colors() as $name => $params ):
        $value = MemberWunder\Controller\Options::get_option( 'colors' );
        $value = isset( $value[$name] ) ? $value[$name] : $params['default'];
    ?>
        <tr>
            <th scope="row">
                <label for="<?= MemberWunder\Controller\Options::OPTION_NAME; ?>_colors_<?= $name; ?>">
                    <?= __( $params['label'] ); ?>
                </label>
            </th>
            <td>
                <div class="twm-custom-theme-color">          
                  <div class="twm-custom-theme-color-header">                  
                    <div 
                      class="js-twm-colorpicker twm-colorpicker-input"
                      style="background:<?= htmlspecialchars( $value ); ?>"
                      >
                        <input type="hidden"                                   
                               class="js-twm-colorpicker__input"
                               id="<?= MemberWunder\Controller\Options::OPTION_NAME; ?>_colors_<?= $name; ?>"
                               name="<?= MemberWunder\Controller\Options::OPTION_NAME; ?>[colors][<?= $name; ?>]"
                               value="<?= htmlspecialchars( $value ); ?>"
                               />                            
                    </div>
                    <div aria-hidden="true" data-icon></div>
                  </div>
                  <div class="twm-custom-theme-color-content">
                    <?= \MemberWunder\Controller\Options\Sections\Design::template_color_hint( $name );?>
                  </div>
                </div>                            
            </td>
        </tr>
    <?php
        unset( $value );
      endforeach;
    ?>
  </table>
</div>