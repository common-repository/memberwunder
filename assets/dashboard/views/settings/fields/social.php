<div class="js-twmshp__social-wrapper">
    <script type="text/html" class="js-twmshp__social-item-tpl">
        <div class="card js-twmshp__social-item">
            <table class="form-table">
                <tbody>
                    <tr class="js-twm-tooltip">
                        <th scope="row">
                            <label><?php _e('Label', TWM_TD); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   class="ms-text regular-text"
                                   name="<?= $name.'[label][]'; ?>"
                                   value="{%label%}"
                                   data-tooltip="<?php _e('Write down the name of your social profile here.', TWM_TD); ?>"
                                   />
                        </td>
                    </tr>
                    <tr class="js-twm-tooltip">
                        <th scope="row">
                            <label><?php _e('Description', TWM_TD); ?></label>
                        </th>
                        <td><input type="text"
                                   class="ms-text regular-text"
                                   name="<?= $name.'[description][]'; ?>"
                                   value="{%description%}"
                                   data-tooltip="<?php _e('Write down a “call-to-action” like e.g. “Follow me on Facebook”.', TWM_TD); ?>"
                                   /></td>
                    </tr>
                    <tr class="js-twm-tooltip">
                        <th scope="row">
                            <label><?php _e('URL', TWM_TD); ?></label>
                        </th>
                        <td><input type="text"
                                   class="ms-text regular-text"
                                   name="<?= $name.'[url][]'; ?>"
                                   value="{%url%}"
                                   data-tooltip="<?php _e('Put in the link to your social media profile here.', TWM_TD); ?>"
                                   /></td>
                    </tr>
                    <tr class="js-twm-tooltip">
                        <th scope="row">
                            <label><?php _e('Image', TWM_TD); ?></label>
                        </th>
                        <td>
                            <div class="js-twmshp__image_upload_wrapper">
                                <div><input type="text"
                                            name="<?= $name.'[image][]'; ?>"
                                            class="ms-image regular-text js-twmshp__image_upload_value"
                                            value="{%image%}"
                                            data-tooltip="<?php _e('Fill in a picture or icon of the chosen social network.', TWM_TD); ?>"
                                            /></div>
                                <div><input type="button" class="js-twmshp__image_upload_button button" data-size="twm_social_icon" value="<?php _e('Upload image', TWM_TD); ?>" /></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row-actions">
                <span class="delete"><a href="#twm-social-delete" class="delete js-twmshp__social-delete"><?php _e('Delete', TWM_TD); ?></a></span>
            </div>
        </div>
    </script>
    <input type="hidden" name="<?= $name; ?>" value="" />
    <div class="ms-social-field js-twmshp__social" style="display: none">
        <?php
        if (is_array($value) && !empty($value['image'])) {
            foreach ($value['image'] as $value_image) {
                ?>
                <input type="hidden" name="<?= $name.'[image][]'; ?>" value="<?php echo esc_attr($value_image); ?>" />
                <?php
            }
        }
        ?>
        <?php
        if (is_array($value) && !empty($value['url'])) {
            foreach ($value['url'] as $value_url) {
                ?>
                <input type="hidden" name="<?= $name.'[url][]'; ?>" value="<?php echo esc_attr($value_url); ?>" />
                <?php
            }
        }
        ?>
        <?php
        if (is_array($value) && !empty($value['label'])) {
            foreach ($value['label'] as $value_label) {
                ?>
                <input type="hidden" name="<?= $name.'[label][]'; ?>" value="<?php echo esc_attr($value_label); ?>" />
                <?php
            }
        }
        ?>
        <?php
        if (is_array($value) && !empty($value['description'])) {
            foreach ($value['description'] as $value_description) {
                ?>
                <input type="hidden" name="<?= $name.'[description][]'; ?>" value="<?php echo esc_attr($value_description); ?>" />
                <?php
            }
        }
        ?>
    </div>
    <div class="ms-social-field-actions js-twmshp__social-actions" style="display: none">
        <a class="js-twmshp__social-add button button-large" href="#"><?php _e('Add item', TWM_TD); ?></a>
    </div>
</div>