<?php

namespace MemberWunder\Services;

class Mail {

    protected $user;
    protected $headers;
    protected $course;

    public function __construct($user, $course) {
        $this->user = $user;
        $this->headers = array(sprintf('From: %s <%s>', get_bloginfo('name'), 'noreply@' . $_SERVER['SERVER_NAME']), 'content-type: text/html',);
        $this->course = $course;
    }

    public function sendForNewUser($password = null) {
        $user = $this->user;
        $email = isset($user->user_email) ? $user->user_email: $user->email ;

        if ($password === null) {
            $password = $user->user_pass;
        }

        $var = array(
            '%login%' => $user->user_login,
            '%password%' => $password,
            '%course_name%' => $this->course->post_title,
            '%url%' => twmshp_get_dashboard_url(),
            '%user_id%' => $user->ID,
            '%email%' => $email,
            '%first_name%' => $user->first_name,
            '%last_name%' => $user->last_name,
            '%date%' => date('d.m.Y'),
        );

        $body = twmshp_get_option('purchase_email');
        $body = str_replace(array_keys($var), array_values($var), $body);
        
        $body = str_replace("\n", '<br>', $body);
        
        $subject = twmshp_get_option('subject');
        $subject = str_replace(array_keys($var), array_values($var), $subject);        
        
        //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test.txt', $subject.' '.$email.' '.$body, FILE_APPEND);
        
        wp_mail($email, $subject ? $subject : translate('Welcome to MemberWunder!', TWM_TD), $body, $this->headers);
    }

    public function sendForExistingUser() {

        $user = $this->user;
        $email = isset($user->user_email) ? $user->user_email: $user->email ;

        $var = array(
            '%login%' => $user->user_login,
            '%course_name%' => $this->course->post_title,
            '%url%' => twmshp_get_dashboard_url(),
            '%user_id%' => $user->ID,
            '%email%' => $email,
            '%first_name%' => $user->first_name,
            '%last_name%' => $user->last_name,
            '%date%' => date('d.m.Y'),
        );

        $body = twmshp_get_option('purchase_email_existing_user');
        $body = str_replace(array_keys($var), array_values($var), $body);
        $body = str_replace("\n", '<br>', $body);
        
        $subject = twmshp_get_option('subject_existing_user');
        $subject = str_replace(array_keys($var), array_values($var), $subject);
        
        wp_mail($email, $subject ? $subject : translate('Welcome to MemberWunder!', TWM_TD), $body, $this->headers);
    }

}
