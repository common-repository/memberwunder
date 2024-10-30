<?php

namespace MemberWunder\Services;

class Smtp {

    const OPTION_NAME = 'twmembership';

    /**
     * Add all hooks
     */
    public function hooks() {

// delete_option('twm_smtp_options');
        add_action('phpmailer_init', array($this, 'twm_smtp_init_smtp'));
        add_action('admin_init', array($this, 'twm_smtp_admin_init'));
    }

    public static function action() {

        $twm_smtp_options = get_option('twm_smtp_options');
        $display_add_options = $message = $error = $result = '';

        if (isset($_POST['twm_smtp_form_submit'])) 
        {
            /* Update settings */
            $twm_smtp_options['from_name_field'] = isset($_POST['twm_smtp_from_name']) ? sanitize_text_field(wp_unslash($_POST['twm_smtp_from_name'])) : '';
            if( isset( $_POST['twm_smtp_from_email'] ) )
                if( !empty( $_POST['twm_smtp_from_email'] ) ) 
                {
                    if( is_email( $_POST['twm_smtp_from_email'] ) ) 
                    {
                        $twm_smtp_options['from_email_field'] = sanitize_email($_POST['twm_smtp_from_email']);
                    } else {
                        $error .= " " . __("Please enter a valid email address in the 'FROM' field.", TWM_TD);
                    }
                }else{
                    $twm_smtp_options['from_email_field'] = '';
                }

            $twm_smtp_options['smtp_settings']['host'] = sanitize_text_field($_POST['twm_smtp_smtp_host']);
            $twm_smtp_options['smtp_settings']['type_encryption'] = ( isset($_POST['twm_smtp_smtp_type_encryption']) ) ? sanitize_text_field($_POST['twm_smtp_smtp_type_encryption']) : 'none';
            $twm_smtp_options['smtp_settings']['autentication'] = ( isset($_POST['twm_smtp_smtp_autentication']) ) ? sanitize_text_field($_POST['twm_smtp_smtp_autentication']) : 'yes';
            $twm_smtp_options['smtp_settings']['username'] = sanitize_text_field($_POST['twm_smtp_smtp_username']);
            $smtp_password = stripslashes($_POST['twm_smtp_smtp_password']);
            $twm_smtp_options['smtp_settings']['password'] = base64_encode($smtp_password);

            /* Check value from "SMTP port" option */
            if( isset( $_POST['twm_smtp_smtp_port'] ) ) 
                if( !empty( $_POST['twm_smtp_smtp_port'] ) )
                {
                    if(empty($_POST['twm_smtp_smtp_port']) || 1 > intval($_POST['twm_smtp_smtp_port']) || (!preg_match('/^\d+$/', $_POST['twm_smtp_smtp_port']) )) {
                        $twm_smtp_options['smtp_settings']['port'] = '25';
                        $error .= " " . __("Please enter a valid port in the 'SMTP Port' field.", TWM_TD);
                    } else {
                        $twm_smtp_options['smtp_settings']['port'] = sanitize_text_field($_POST['twm_smtp_smtp_port']);
                    }
                }else{
                    $twm_smtp_options['smtp_settings']['port'] = '';
                }
                
            /* Update settings in the database */
            if (empty($error)) {
                update_option('twm_smtp_options', $twm_smtp_options);
                $message .= __("Settings saved.", TWM_TD);
            } else {
                $error .= " " . __("Settings are not saved.", TWM_TD);
            }
        }

        return $twm_smtp_options;
    }

    /**
     * Renders the admin settings menu of the plugin.
     * @return void
     */
    public static function twm_smtp_settings() {
        $twm_smtp_options = self::action();
        ?>
        <div class="inside">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e("From Email Address", TWM_TD); ?></th>
                    <td>
                        <input type="email" 
                               name="twm_smtp_from_email"
                               value="<?php echo esc_attr($twm_smtp_options['from_email_field']); ?>"/><br />
                        <p class="description"><?php _e("This email address will be used in the 'From' field.", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e("From Name", TWM_TD); ?></th>
                    <td>
                        <input type="text" name="twm_smtp_from_name" value="<?php echo esc_attr($twm_smtp_options['from_name_field']); ?>"/><br />
                        <p class="description"><?php _e("This text will be used in the 'FROM' field", TWM_TD); ?></p>
                    </td>
                </tr>			
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('SMTP Host', TWM_TD); ?></th>
                    <td>
                        <input type='text' name='twm_smtp_smtp_host' value='<?php echo esc_attr($twm_smtp_options['smtp_settings']['host']); ?>' /><br />
                        <p class="description"><?php _e("Your mail server", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('Type of Encryption', TWM_TD); ?></th>
                    <td>
                        <label for="twm_smtp_smtp_type_encryption_1"><input type="radio" id="twm_smtp_smtp_type_encryption_1" name="twm_smtp_smtp_type_encryption" value='none' <?php if ('none' == $twm_smtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('None', TWM_TD); ?></label>
                        <label for="twm_smtp_smtp_type_encryption_2"><input type="radio" id="twm_smtp_smtp_type_encryption_2" name="twm_smtp_smtp_type_encryption" value='ssl' <?php if ('ssl' == $twm_smtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('SSL', TWM_TD); ?></label>
                        <label for="twm_smtp_smtp_type_encryption_3"><input type="radio" id="twm_smtp_smtp_type_encryption_3" name="twm_smtp_smtp_type_encryption" value='tls' <?php if ('tls' == $twm_smtp_options['smtp_settings']['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('TLS', TWM_TD); ?></label><br />
                        <p class="description"><?php _e("For most servers SSL is the recommended option", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('SMTP Port', TWM_TD); ?></th>
                    <td>
                        <input type='number' 
                               name='twm_smtp_smtp_port'
                               value='<?php echo esc_attr($twm_smtp_options['smtp_settings']['port']); ?>' 
                               /><br />
                        <p class="description"><?php _e("The port to your mail server", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('SMTP Authentication', TWM_TD); ?></th>
                    <td>
                        <label for="twm_smtp_smtp_autentication"><input type="radio" id="twm_smtp_smtp_autentication" name="twm_smtp_smtp_autentication" value='no' <?php if ('no' == $twm_smtp_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('No', TWM_TD); ?></label>
                        <label for="twm_smtp_smtp_autentication"><input type="radio" id="twm_smtp_smtp_autentication" name="twm_smtp_smtp_autentication" value='yes' <?php if ('yes' == $twm_smtp_options['smtp_settings']['autentication']) echo 'checked="checked"'; ?> /> <?php _e('Yes', TWM_TD); ?></label><br />
                        <p class="description"><?php _e("This options should always be checked 'Yes'", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('SMTP username', TWM_TD); ?></th>
                    <td>
                        <input type='text' name='twm_smtp_smtp_username' value='<?php echo esc_attr($twm_smtp_options['smtp_settings']['username']); ?>' /><br />
                        <p class="description"><?php _e("The username to login to your mail server", TWM_TD); ?></p>
                    </td>
                </tr>
                <tr class="ad_opt twm_smtp_smtp_options">
                    <th><?php _e('SMTP Password', TWM_TD); ?></th>
                    <td>
                        <input type='password' name='twm_smtp_smtp_password' value='<?php echo esc_attr(self::twm_smtp_get_password_stat()); ?>' /><br />
                        <p class="description"><?php _e("The password to login to your mail server", TWM_TD); ?></p>
                    </td>
                </tr>
            </table>

            <input type="hidden" class="regular-text" name="<?php echo self::OPTION_NAME ?>[twm_smtp_form_submit]" value="<?php echo time(); ?>" />
            <input type="hidden" name="twm_smtp_form_submit" value="submit" />

            <h3 class="hndle"><label for="title"><?php _e('Testing And Debugging Settings', TWM_TD); ?></label></h3>
            <div class="inside js-twm-test-email"
                 data-wait="<?php _e("Wait...", TWM_TD); ?>"             
                 >    

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e("To", TWM_TD); ?>:</th>
                        <td>
                            <input type="email"
                                   name="twm_smtp_to"                                   
                                   class="js-twm-test-email__email"
                                   /><br />
                            <p class="description"><?php _e("Enter the recipient's email address", TWM_TD); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Subject", TWM_TD); ?>:</th>
                        <td>
                            <input type="text"
                                   name="twm_smtp_subject"
                                   value=""
                                   class="js-twm-test-email__subject"
                                   /><br />
                            <p class="description"><?php _e("Enter a subject for your message", TWM_TD); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Message", TWM_TD); ?>:</th>
                        <td>
                            <textarea name="twm_smtp_message"
                                      id="twm_smtp_message"
                                      rows="5"
                                      class="js-twm-test-email__message"
                                      ></textarea><br />
                            <p class="description"><?php _e("Write your email message", TWM_TD); ?></p>
                        </td>
                    </tr>				
                </table>
                <p class="submit">
                    <input type="button"
                           id="settings-form-submit" 
                           class="button js-twm-test-email__sbm" 
                           value="<?php _e('Send Test Email', TWM_TD) ?>"
                           />
                </p>

            </div><!-- end of inside -->   





        </div><!-- end of inside -->


        <?php
    }

    /**
     * Plugin functions for init
     * @return void
     */
    public function twm_smtp_admin_init() {
        if (isset($_REQUEST['page']) && 'memberwunder' == $_REQUEST['page'] &&
                isset($_REQUEST['section']) && 'smtp' == $_REQUEST['section']
        ) {
            /* register plugin settings */
            $this->twm_smtp_register_settings();
        }
    }

    /**
     * Register settings function
     * @return void
     */
    public function twm_smtp_register_settings() {
        $twm_smtp_options_default = array(
                                            'from_email_field'      => '',
                                            'from_name_field'       => '',
                                            'smtp_settings'         => array(
                                                                        'host'              => '',
                                                                        'type_encryption'   => '',
                                                                        'port'              => '',
                                                                        'autentication'     => 'yes',
                                                                        'username'          => '',
                                                                        'password'          => ''
                                                                            )
                                        );

        /* install the default plugin options */
        if (!get_option('twm_smtp_options')) {

            add_option('twm_smtp_options', $twm_smtp_options_default, '', 'yes');
        }
    }

    /**
     * Function to add smtp options in the phpmailer_init
     * @param \PHPMailer $phpmailer
     * @return void
     */
    public function twm_smtp_init_smtp($phpmailer) {
        //check if SMTP credentials have been configured.
        if (!$this->twm_smtp_credentials_configured()) {
            return;
        }
        $twm_smtp_options = get_option('twm_smtp_options');
        /* Set the mailer type as per config above, this overrides the already called isMail method */
        $phpmailer->IsSMTP();
        $from_email = $twm_smtp_options['from_email_field'];
        $phpmailer->From = $from_email;
        $from_name = $twm_smtp_options['from_name_field'];
        $phpmailer->FromName = $from_name;
        $phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName);
        $phpmailer->Timeout = 10;

        /* Set the SMTPSecure value */
        if ($twm_smtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $phpmailer->SMTPSecure = $twm_smtp_options['smtp_settings']['type_encryption'];
        }

        /* Set the other options */
        $phpmailer->Host = $twm_smtp_options['smtp_settings']['host'];
        $phpmailer->Port = $twm_smtp_options['smtp_settings']['port'];

        /* If we're using smtp auth, set the username & password */
        if ('yes' == $twm_smtp_options['smtp_settings']['autentication']) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $twm_smtp_options['smtp_settings']['username'];
            $phpmailer->Password = $this->twm_smtp_get_password();
        }
        //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
        $phpmailer->SMTPAutoTLS = false;
    }

    public function twm_smtp_get_password() {
        return $this::twm_smtp_get_password_stat();
    }

    public static function twm_smtp_get_password_stat() {
        $twm_smtp_options = get_option('twm_smtp_options');
        $temp_password = $twm_smtp_options['smtp_settings']['password'];
        $password = "";
        $decoded_pass = base64_decode($temp_password);
        /* no additional checks for servers that aren't configured with mbstring enabled */
        if (!function_exists('mb_detect_encoding')) {
            return $decoded_pass;
        }
        /* end of mbstring check */
        if (base64_encode($decoded_pass) === $temp_password) {  //it might be encoded
            if (false === mb_detect_encoding($decoded_pass)) {  //could not find character encoding.
                $password = $temp_password;
            } else {
                $password = base64_decode($temp_password);
            }
        } else { //not encoded
            $password = $temp_password;
        }
        return $password;
    }

    public function twm_smtp_credentials_configured() {
        $twm_smtp_options = get_option('twm_smtp_options');
        $credentials_configured = true;
        if (!isset($twm_smtp_options['from_email_field']) || empty($twm_smtp_options['from_email_field'])) {
            $credentials_configured = false;
        }
        if (!isset($twm_smtp_options['from_name_field']) || empty($twm_smtp_options['from_name_field'])) {
            $credentials_configured = false;
        }
        return $credentials_configured;
    }

    public function twm_smtp_test_mail($to_email, $subject, $message) {
        if (!$this->twm_smtp_credentials_configured()) {
            return;
        }              

        $errors = '';

        $swpsmtp_options = get_option('twm_smtp_options');

        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new \PHPMailer();

        $charset = get_bloginfo('charset');
        $mail->CharSet = $charset;
        $mail->Timeout = 10;

        $from_name = $swpsmtp_options['from_name_field'];
        $from_email = $swpsmtp_options['from_email_field'];

        $mail->IsSMTP();

        /* If using smtp auth, set the username & password */
        if ('yes' == $swpsmtp_options['smtp_settings']['autentication']) {
            $mail->SMTPAuth = true;
            $mail->Username = $swpsmtp_options['smtp_settings']['username'];
            $mail->Password = $this->twm_smtp_get_password();
        }

        /* Set the SMTPSecure value, if set to none, leave this blank */
        if ($swpsmtp_options['smtp_settings']['type_encryption'] !== 'none') {
            $mail->SMTPSecure = $swpsmtp_options['smtp_settings']['type_encryption'];
        }

        /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
        $mail->SMTPAutoTLS = false;

        /* Set the other options */
        $mail->Host = $swpsmtp_options['smtp_settings']['host'];
        $mail->Port = $swpsmtp_options['smtp_settings']['port'];

        $mail->SetFrom($from_email, $from_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        $mail->AddAddress($to_email);
        global $debugMSG;
        $debugMSG = '';
        $mail->Debugoutput = function($str, $level) {
            global $debugMSG;
            $debugMSG .= $str;
        };
        $mail->SMTPDebug = 4;

        /* Send mail and return result */
        if (!$mail->Send())
            $errors = $mail->ErrorInfo;

        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        $cl = new \stdClass();
        $cl->debug = $debugMSG;

        if (!empty($errors)) {
            $cl->msg = $errors;
            $cl->status = 'fail';
        } else {
            $cl->msg = __('Test mail was sent', TWM_TD);
            $cl->status = 'success';
        }

        return $cl;
    }

    public function twm_smtp_send_test_mail($email, $subject, $message) {
        $email = sanitize_text_field($email);
        if (!is_email($email)) {        
            return array('status' => 'fail', 'msg' => __("Please enter a valid email address in the recipient email field.", TWM_TD)); 
        }

        $subject = sanitize_text_field($subject);
        $message = sanitize_text_field($message);

        $resp = $this->twm_smtp_test_mail($email, $subject, $message);

        if (!$resp)
            return array( 'status' => 'fail', 'message' => __("Please configure your SMTP credentials", TWM_TD), 'resp' => $resp ); 

        return array( 'status' => $resp->status, 'message' => ( $resp->status == 'success' ? __( 'Mail successfully sent', TWM_TD ) : __( "Mail couldn't be sent successfully", TWM_TD ) ), 'resp' => $resp );
    }

}
