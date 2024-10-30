<input type="hidden" class="js-twmembership_info" data-tooltip="<?php _e('Here you can change basic information of your courses.', TWM_TD); ?>">
<div class="ms-metabox-course-info">
    <div class="ms-field ms-field-large">
        <label class="ms-label js-twm-tooltip-element"
               for="cinfo_subject"
               data-tooltip="<?php _e('Hier bestimmst Du die Kategorie deines Kurses wie z.B. “Traffic”.', TWM_TD); ?>"
               ><?php _e('Subject', TWM_TD); ?>:</label>
        <div class="ms-value">
            <input type="text" class="ms-big" id="cinfo_subject" name="cinfo[subject]"<?php if (!empty($template_vars['subject'])) { ?> value="<?php echo esc_attr($template_vars['subject']); ?>"<?php } ?> />
        </div>
    </div>
    <div class="ms-field ms-field-large">
        <label class="ms-label js-twm-tooltip-element"
               for="cinfo_level"
               data-tooltip="<?php _e('Here you can change the difficulty level of your courses, e.g. “beginner, advanced or expert”', TWM_TD); ?>"
               ><?php _e('Level', TWM_TD); ?>:</label>
        <div class="ms-value">
            <input type="text" class="ms-big" id="cinfo_level" name="cinfo[level]"<?php if (!empty($template_vars['level'])) { ?> value="<?php echo esc_attr($template_vars['level']); ?>"<?php } ?> />
        </div>
    </div>
    <div class="ms-group">
        <div class="ms-group-item">
            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element"
                       for="cinfo_duration"
                       data-tooltip="<?php _e('Write down the approximate length of your course, e.g. 12 hours', TWM_TD); ?>"
                       ><?php _e('Duration', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input class="ms-small" type="text" id="cinfo_duration" name="cinfo[duration]" size="4"<?php if (!empty($template_vars['duration'])) { ?> value="<?php echo esc_attr($template_vars['duration']); ?>"<?php } ?> />
                    <span class="ms-text ms-text-light"><?php _e('Hours', TWM_TD); ?></span>
                </div>
            </div>

            <div class="ms-field ms-field-large">
                <label class="ms-label"
                       for="cinfo_is_obligatory"><?php _e( 'Make modules obligatory', TWM_TD ); ?>:</label>
                <div class="ms-value">
                    <select class="ms-large" id="cinfo_is_obligatory" name="cinfo[is_obligatory]">
                        <?php 
                        $arr = array(
                            0 => __( 'No', TWM_TD ),
                            1 => __( 'Yes', TWM_TD ), 
                        );
                        foreach ($arr as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['is_obligatory']) && $template_vars['is_obligatory'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>                    
                </div>
            </div>

            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element"
                       for="cinfo_price"
                       data-tooltip="<?php _e('Determine the price to be shown in the membership are. Important: The real price is determined in Digistore24.', TWM_TD); ?>"
                       ><?php _e('Price', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select class="ms-small" name="cinfo[currency]">
                        <?php foreach( array_merge( array( '' => '' ), \MemberWunder\Controller\Options\Currency::get_currencies() ) as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['currency']) && $template_vars['currency'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>
                    <input type="text" id="cinfo_price" name="cinfo[price]" size="4"<?php if (!empty($template_vars['price'])) { ?> value="<?php echo esc_attr($template_vars['price']); ?>"<?php } ?> />
                    <select class="ms-small" name="cinfo[payment_period]">
                        <?php foreach ( twmshp_get_course_price_period_label() as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['payment_period']) && $template_vars['payment_period'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="ms-field ms-field-large for_memberwunder_pro">
                <label class="ms-label js-twm-tooltip-element"
                       for="cinfo_duration"
                       data-tooltip="<?php _e( 'Set the time on how long you want to allow a specific user to have access to this course', TWM_TD );?>"
                       ><?php _e('Limit access by time', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <input class="ms-small" type="text" id="cinfo_time_access" name="cinfo[time_access]" size="4"<?php if (!empty($template_vars['time_access'])) { ?> value="<?= ( isset( $template_vars['time_access'] ) ? $template_vars['time_access'] : 0 ) / 86400; ?>"<?php } ?> />
                    <span class="ms-text ms-text-light"><?php _e('Days', TWM_TD); ?></span>
                </div>
            </div>
        </div>
    </div>   
    <?php if( twmshp_get_option( 'login_layot_type' ) == 'advansed' && (int)twmshp_get_option( 'login_advansed_layot_type' ) == 5 ): ?>
    <div class="ms-group">
        <div class="ms-group-item">            
            <div class="ms-field ms-field-large">
                <label class="ms-label" for="cinfo_show_on_login"><?php _e( 'Show this course on login page', TWM_TD ); ?>:</label>
                <div class="ms-value">
                    <input id="cinfo_show_on_login" type="checkbox" name="cinfo[show_on_login]" value="1" <?= isset( $template_vars['show_on_login'] ) && $template_vars['show_on_login'] ? 'checked' : '';?> />                  
                </div>
            </div>
        </div>
    </div> 
    <?php endif; ?>
        
    <div class="ms-group">
        <div class="ms-group-item">            
            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element"
                       data-tooltip="<?php _e('Choose to either show or hide the price of the course in the membership area before buying it', TWM_TD); ?>"
                       for="cinfo_price_display_options_bp"><?php _e('Price display options before purchase', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select class="ms-large" id="cinfo_price_display_options_bp" name="cinfo[price_display_options_bp]">
                        <?php 
                        $arr = array(
                            'all' => __('Show price in course and on course overview', TWM_TD), 
                            'course' => __('Show price only in course', TWM_TD),
                            'hide' => __('Don’t show price', TWM_TD),
                        );
                        foreach ($arr as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['price_display_options_bp']) && $template_vars['price_display_options_bp'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>                    
                </div>
            </div>
        </div>
    </div> 
    
    <div class="ms-group">
        <div class="ms-group-item">            
            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element"
                       data-tooltip="<?php _e('Choose to either show or hide the price of the course in the membership area after buying it', TWM_TD); ?>"
                       for="cinfo_price_display_options_ap"><?php _e('Price display options after purchase', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select class="ms-large" id="cinfo_price_display_options_ap" name="cinfo[price_display_options_ap]">
                        <?php 
                        $arr = array(
                            'hide' => __('Don’t show price', TWM_TD),
                            'all' => __('Show price in course and on course overview', TWM_TD), 
                            'course' => __('Show price only in course', TWM_TD),
                        );
                        foreach ($arr as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['price_display_options_ap']) && $template_vars['price_display_options_ap'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>                    
                </div>
            </div>
        </div>
    </div>    
    
    
    <div class="ms-group">
        <div class="ms-group-item">            
            <div class="ms-field ms-field-large">
                <label class="ms-label js-twm-tooltip-element" 
                       data-tooltip="<?php _e('Choose to show price of the course as “net”, “gross” or without taxes.', TWM_TD); ?>"
                       for="cinfo_tax_display_options"><?php _e('Tax display options', TWM_TD); ?>:</label>
                <div class="ms-value">
                    <select class="ms-large" id="cinfo_tax_display_options" name="cinfo[tax_display_options]">
                        <?php 
                        $arr = array(
                            'after' => __('Add "after tax (brutto)"', TWM_TD),
                            'before' => __('Add "before tax (netto)"', TWM_TD), 
                            'hide' => __('Don’t show tax', TWM_TD),
                        );
                        foreach ($arr as $k => $v) { ?>
                        <option value="<?php echo esc_attr($k); ?>"<?php if (!empty($template_vars['tax_display_options']) && $template_vars['tax_display_options'] == $k) { ?> selected="selected"<?php } ?>><?php echo esc_html($v); ?></option>
                        <?php } ?>
                    </select>                    
                </div>
            </div>
        </div>
    </div>    
    
    
    <div class="ms-group">   
        <div class="ms-group-item">
            <!--
            <div class="ms-field ms-field-medium">
                <label class="ms-label" for="cinfo_start">Start course:</label>
                <div class="ms-value">
                    <input type="text" id="cinfo_start" name="cinfo[start]" size="10"<?php if (!empty($template_vars['start'])) { ?> value="<?php echo esc_attr($template_vars['start']); ?>"<?php } ?> />
                </div>
            </div>
            <div class="ms-field ms-field-medium">
                <label class="ms-label" for="cinfo_videos_amount">Amount of Videos:</label>
                <div class="ms-value">
                    <input type="text" id="cinfo_videos_amount" name="cinfo[videos_amount]" size="10"<?php if (!empty($template_vars['videos_amount'])) { ?> value="<?php echo esc_attr($template_vars['videos_amount']); ?>"<?php } ?> />
                </div>
            </div>
            -->
        </div>
    </div>
    <div>
        <label class="ms-label ms-label-wide js-twm-tooltip-element"
               data-tooltip="<?php _e('Here you can fill in a description of your courses.', TWM_TD); ?>"
               ><?php _e('Description', TWM_TD); ?>:</label>
        <?php echo twmshp_render_editor(!empty($template_vars['description']) ? $template_vars['description'] : '', 'cinfo[description]', 'cinfo_description'); ?>
    </div>
</div>