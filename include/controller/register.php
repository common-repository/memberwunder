<?php

namespace MemberWunder\Controller;

class Register {

    /**
     * @var \WP_Error|null
     */
    protected $errors = null;
    protected $user_name = '';
    protected $user_email = '';

    public function __construct() {
        $error_codes = empty($_GET['twm_error_codes']) ? array() : explode(',', (string)$_GET['twm_error_codes']);
        if ($error_codes) {
            $errors = new \WP_Error();
            $errorList = array(
                array('user_name', __('Usernames can only contain lowercase letters (a-z) and numbers.')),
                array('user_name', __('Please enter a username.')),
                array('user_name', __('Sorry, that username is not allowed.')),
                array('user_name', __('Username must be at least 4 characters.')),
                array('user_name', __('Username may not be longer than 60 characters.')),
                array('user_name', __('Sorry, usernames must have letters too!')),
                array('user_name', __('Sorry, that username already exists!')),
                array('user_name', __('That username is currently reserved but may be available in a couple of days.')),
                array('user_email', __('You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.')),
                array('user_email', __( 'Please enter a valid email address.')),
                array('user_email', __('Sorry, that email address is not allowed!')),
                array('user_email', __( 'Sorry, that email address is already used!')),
                array('user_email', __('That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing.')),
                array('empty_username', __('<strong>ERROR</strong>: Please enter a username.')),
                array('username_exists', __('<strong>ERROR</strong>: This username is already registered. Please choose another one.')),
                array('invalid_username', __('<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.')),
                array('invalid_username', __('<strong>ERROR</strong>: Sorry, that username is not allowed.')),
                array('empty_email', __('<strong>ERROR</strong>: Please type your email address.')),
                array('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.')),
                array('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.')),
                array('first_name_error', __('<strong>ERROR</strong>: Please type your first name.', TWM_TD)),
                array('last_name_error', __('<strong>ERROR</strong>: Please type your last name.', TWM_TD)),
                array('password_error', __('<strong>ERROR</strong>: Please type your password.', TWM_TD)),
                array('password_error', __('<strong>ERROR</strong>: Password length must be greater than 5.', TWM_TD)),
                array('confirm_error', __('<strong>ERROR</strong>: Password does not match the confirm password.', TWM_TD))
            );
            $errorMap = array();
            foreach ($errorList as $errorListItem) {
                if (!isset($errorMap[$errorListItem[0]])) {
                    $errorMap[$errorListItem[0]] = array();
                }
                $errorMap[$errorListItem[0]][(string)crc32($errorListItem[1])] = $errorListItem[1];
            }

            foreach ($error_codes as $error_data) {
                $error_data = explode('.', $error_data);
                if (count($error_data) === 2 && isset($errorMap[$error_data[0]]) && isset($errorMap[$error_data[0]][$error_data[1]])) {
                    $errors->add($error_data[0], $errorMap[$error_data[0]][$error_data[1]]);
                }
            }

            $this->errors = $errors;
        }

        if (isset($_POST['memberwunder_register']) && isset($_POST['user_email'])) {
            $_POST['user_login'] = $_POST['user_email'];
        }
    }

    /**
     * Register hooks
     */
    public function hooks() {
        add_shortcode('memberwunder-register-errors', array($this, 'shortcodeErrors'));

        if (isset($_POST['memberwunder_register']) && twmshp_users_can_register()) {
            // multisite sinup
            add_action('twm_pre_page_register', array($this, 'preRegister'));

            // ordinary signup
            add_action('register_post', array($this, 'register_post'), -1, 0);
            add_action('user_register', array($this, 'user_register_fields'), -1, 1);
            add_action('user_register', array($this, 'user_register'), 10, 1);
            add_filter('init', array($this, 'registration_page_redirect'));
            add_filter('random_password', array($this, 'set_password'));
        }
    }

    public function preRegister() {
        if (is_multisite()) {
            $this->user_signup();
        }
    }

    /**
     * @return array
     */
    public function user_signup() {
        $user_name = empty($_POST['user_name']) ? '' : (string) $_POST['user_name'];
        $user_email = empty($_POST['user_email']) ? '' : (string) $_POST['user_email'];

        remove_all_filters('wpmu_validate_user_signup');

        $result = wpmu_validate_user_signup($user_name, $user_email);
        $user_name = $result['user_name'];
        $user_email = $result['user_email'];

        /** @var \WP_Error $errors */
        $errors = $result['errors'];
        if ($errors->get_error_code()) {
            ob_clean();

            $error_codes = $errors->get_error_codes();

            $args = array();
            $args['twm_user_name'] = $user_name;
            $args['twm_user_email'] = $user_email;
            if ($error_codes) {
                $args['twm_error_codes'] = array();
                foreach ($errors->get_error_codes() as $error_code) {
                    $error_message = $errors->get_error_message($error_code);
                    if ($error_message) {
                        $args['twm_error_codes'][] = $error_code . '.' . (string)crc32($error_message);
                    }
                }
                $args['twm_error_codes'] = implode(',', $args['twm_error_codes']);
            }

            $url = add_query_arg($args, twmshp_get_register_url());

            wp_redirect($url);
            exit;
        }

        wpmu_signup_user($user_name, $user_email, array('add_to_blog' => get_current_blog_id(), 'new_role' => 'subscriber'));

        do_action('signup_finished');

        $args = array();
        $args['twm_finished'] = 1;
        $args['twm_user_name'] = $user_name;
        $args['twm_user_email'] = $user_email;

        $url = add_query_arg($args, twmshp_get_register_url());

        wp_redirect($url);
        exit;
    }

    public function set_password() {
        return $_POST['pwd'];
    }

    public function register_post() {
        remove_all_filters('registration_errors');
        add_filter('registration_errors', array($this, 'registration_errors'), 10, 1);
    }

    /**
     * @param \WP_Error $errors
     * @return mixed
     */
    public function registration_errors($errors) {
        if (empty($_POST['first_name']) || !empty($_POST['first_name']) && trim($_POST['first_name']) == '') {
            $errors->add('first_name_error', __('<strong>ERROR</strong>: Please type your first name.', TWM_TD));
        }
        if (empty($_POST['last_name']) || !empty($_POST['last_name']) && trim($_POST['last_name']) == '') {
            $errors->add('last_name_error', __('<strong>ERROR</strong>: Please type your last name.', TWM_TD));
        }
        if (empty($_POST['pwd']) || !empty($_POST['pwd']) && trim($_POST['pwd']) == '') {
            $errors->add('password_error', __('<strong>ERROR</strong>: Please type your password.', TWM_TD));
        } else {
            if (5 > strlen($_POST['pwd'])) {
                $errors->add('password_error', __('<strong>ERROR</strong>: Password length must be greater than 5.', TWM_TD));
            }
            if (!isset($_POST['confirm_pwd']) || $_POST['pwd'] != $_POST['confirm_pwd']) {
                $errors->add('confirm_error', __('<strong>ERROR</strong>: Password does not match the confirm password.', TWM_TD));
            }
        }

        if ($errors->get_error_code()) {
            $error_codes = $errors->get_error_codes();

            $args = array();
            $args['twm_user_email'] = isset($_POST['user_email']) ? $_POST['user_email'] : '';
            $args['twm_first_name'] = isset($_POST['first_name']) ? $_POST['first_name'] : '';
            $args['twm_last_name'] = isset($_POST['last_name']) ? $_POST['last_name'] : '';

            if ($error_codes) {
                $args['twm_error_codes'] = array();
                foreach ($errors->get_error_codes() as $error_code) {
                    $error_message = $errors->get_error_message($error_code);
                    if ($error_message) {
                        $args['twm_error_codes'][] = $error_code . '.' . (string)crc32($error_message);
                    }
                }
                $args['twm_error_codes'] = implode(',', $args['twm_error_codes']);
            }

            $url = add_query_arg($args, twmshp_get_register_url());

            wp_redirect($url);
            exit;
        }
        return $errors;
    }

    public function user_register_fields($user_id) {
        if (!empty($_POST['first_name'])) {
            update_user_meta($user_id, 'first_name', trim($_POST['first_name']));
        }
        if (!empty($_POST['last_name'])) {
            update_user_meta($user_id, 'last_name', trim($_POST['last_name']));
        }
    }

    public function user_register($user_id) {
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);        
        wp_redirect(twmshp_get_dashboard_url());
        exit;
    }

    public function registration_page_redirect() {
        if (!empty($_POST)) {
            return;
        }

        global $pagenow;

        if (( strtolower($pagenow) == 'wp-login.php') && isset($_GET['action']) && ( strtolower($_GET['action']) == 'register' )) {
            wp_redirect(twmshp_get_register_url());
        }
    }

    /**
     * Return error string
     * @return string
     */
    public function shortcodeErrors() {
        $output = '';

        if (is_wp_error($this->errors)) {
            foreach ($this->errors->get_error_codes() as $code) {

                if ($code == 'empty_username') {
                    continue;
                }

                foreach ($this->errors->get_error_messages($code) as $error_message) {
                    $output .= '    ' . $error_message . "<br />\n";
                }
            }
        }

        return $output;
    }
}
