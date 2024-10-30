<?php

namespace MemberWunder\Controller;

class Reset {

    public function __construct() {
        
    }

    public function hooks() {

        add_action('login_form_rp', array($this, 'redirect_to_custom_password_reset'));
        add_action('login_form_resetpass', array($this, 'redirect_to_custom_password_reset'));
        // Handlers for form posting actions

        add_action('login_form_rp', array($this, 'do_password_reset'));
        add_action('login_form_resetpass', array($this, 'do_password_reset'));


        // Setup
        add_shortcode('twm-password-reset-form', array($this, 'render_password_reset_form'));
    }

    /**
     * Redirects to the custom password reset page, or the login page
     * if there are errors.
     */
    public function redirect_to_custom_password_reset() {
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Verify key / login combo
            $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(add_query_arg(array('login' => 'expiredkey'), twmshp_get_dashboard_url()));
                } else {
                    wp_redirect(add_query_arg(array('login' => 'invalidkey'), twmshp_get_dashboard_url()));
                }
                exit;
            }
            $redirect_url = twmshp_get_reset_url();
            $redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
            $redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);
            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Resets the user's password if the password reset form was submitted.
     */
    public function do_password_reset() {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $rp_key = $_REQUEST['rp_key'];
            $rp_login = $_REQUEST['rp_login'];
            $user = check_password_reset_key($rp_key, $rp_login);
            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(add_query_arg(array('login' => 'expiredkey'), twmshp_get_dashboard_url()));
                } else {
                    wp_redirect(add_query_arg(array('login' => 'invalidkey'), twmshp_get_dashboard_url()));
                }
                exit;
            }
            if (isset($_POST['pass1'])) {
                if ($_POST['pass1'] != $_POST['pass2']) {
                    // Passwords don't match
                    $redirect_url = twmshp_get_reset_url();
                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }
                if (empty($_POST['pass1'])) {
                    // Password is empty
                    $redirect_url = twmshp_get_reset_url();
                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }
                // Parameter checks OK, reset password
                reset_password($user, $_POST['pass1']);
                wp_redirect(add_query_arg(array('password' => 'changed'), twmshp_get_dashboard_url()));
            } else {
                echo "Invalid request.";
            }
            exit;
        }
    }

    /**
     * A shortcode for rendering the form used to reset a user's password.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_password_reset_form($attributes, $content = null) {
        // Parse shortcode attributes
        $default_attributes = array('show_title' => false);
        $attributes = shortcode_atts($default_attributes, $attributes);
        if (is_user_logged_in()) {
            return __('You are already signed in.', TWM_TD);
        } else {
            if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
                $attributes['login'] = $_REQUEST['login'];
                $attributes['key'] = $_REQUEST['key'];
                // Error messages
                $errors = array();
                if (isset($_REQUEST['error'])) {
                    $error_codes = explode(',', $_REQUEST['error']);
                    foreach ($error_codes as $code) {
                        $errors [] = $this->get_error_message($code);
                    }
                }
                $attributes['errors'] = $errors;
                return $this->get_template_html('password_reset_form', $attributes);
            } else {
                return __('Invalid password reset link.', TWM_TD);
            }
        }
    }

    /**
     * Renders the contents of the given template to a string and returns it.
     *
     * @param string $template_name The name of the template to render (without .php)
     * @param array  $attributes    The PHP variables for the template
     *
     * @return string               The contents of the template.
     */
    private function get_template_html($template_name, $attributes = null) {
        if (!$attributes) {
            $attributes = array();
        }
        ob_start();
        do_action('personalize_login_before_' . $template_name);
        require( TWM_PATH . '/templates/reset.php');
        do_action('personalize_login_after_' . $template_name);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Finds and returns a matching error message for the given error code.
     *
     * @param string $error_code    The error code to look up.
     *
     * @return string               An error message.
     */
    private function get_error_message($error_code) {
        switch ($error_code) {
     
            // Reset password
            case 'expiredkey':
            case 'invalidkey':
                return __('The password reset link you used is not valid anymore.', TWM_TD);
            case 'password_reset_mismatch':
                return __("The two passwords you entered don't match.", TWM_TD);
            case 'password_reset_empty':
                return __("Sorry, we don't accept empty passwords.", TWM_TD);
            default:
                break;
        }
        return __('An unknown error occurred. Please try again later.', TWM_TD);
    }

}
