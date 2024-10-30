<?php
namespace MemberWunder\Controller;

class Profile
{
    /**
     * @var \WP_Error|null
     */
    protected $errors = null;

    public function __construct()
    {
    }

    /**
     * Register hooks
     */
    public function hooks()
    {
        add_action('twm_pre_page_profile', array($this, 'preProfile'));
        add_action('wp_ajax_twm_avatar_upload', array($this, 'avatarUploadAction'));
        add_action('wp_ajax_twm_avatar_delete', array($this, 'avatarDeleteAction'));
    }

    public function preProfile()
    {
        $user = wp_get_current_user();
        $action = empty($_POST['action']) ? '' : (string)$_POST['action'];

        if ($action === 'update') {
            require_once(ABSPATH . 'wp-admin/includes/user.php');

            if (!current_user_can('edit_user', $user->ID)) {
                wp_die(__('Sorry, you are not allowed to edit this user.'));
            }

            $errors = edit_user($user->ID);

            if (!is_wp_error($errors)) {
                $redirect = add_query_arg('updated', true, twmshp_get_profile_url());
                wp_redirect($redirect);
                exit;
            }

            $this->errors = $errors;
        }

        if ($action === 'twm_avatar_upload') {
            $this->avatarUpload($user);
            wp_redirect(twmshp_get_profile_url());
            exit;
        }

        if ($action === 'twm_avatar_delete') {
            $this->avatarDelete($user);
            wp_redirect(twmshp_get_profile_url());
            exit;
        }
    }

    public function avatarUploadAction()
    {
        $user = wp_get_current_user();
        $result = $this->avatarUpload($user);
        if (is_wp_error($result)) {
            wp_send_json_error();
        }

        $return = twmshp_get_avatar_url($user->ID);
        wp_send_json_success($return);
    }

    public function avatarDeleteAction()
    {
        $user = wp_get_current_user();
        $result = $this->avatarDelete($user);
        if (is_wp_error($result)) {
            wp_send_json_error();
        }

        $return = twmshp_get_avatar_url($user->ID);
        wp_send_json_success($return);
    }

    /**
     * @param \WP_User $user
     * @return bool|\WP_Error
     */
    public function avatarUpload($user)
    {
        if (!current_user_can('edit_user', $user->ID)) {
            return new \WP_Error('twm_avatar');
        }

        if (isset($_POST['avatar_nonce']) && wp_verify_nonce($_POST['avatar_nonce'], 'avatar_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('avatar', 0);

            if (is_wp_error($attachment_id)) {
                return $attachment_id;
            } else {
                $old_attachment_id = get_user_meta($user->ID, 'twm_avatar_attachment_id', true);
                if ($old_attachment_id) {
                    wp_delete_attachment($old_attachment_id, true);
                }
                update_user_meta($user->ID, 'twm_avatar_attachment_id', $attachment_id);
            }
        } else {
            return new \WP_Error('twm_avatar');
        }

        return true;
    }

    /**
     * @param \WP_User $user
     * @return bool|\WP_Error
     */
    public function avatarDelete($user)
    {
        if (!current_user_can('edit_user', $user->ID)) {
            return new \WP_Error('twm_avatar');
        }

        if (isset($_POST['avatar_nonce']) && wp_verify_nonce($_POST['avatar_nonce'], 'avatar_delete')) {
            $old_attachment_id = get_user_meta($user->ID, 'twm_avatar_attachment_id', true);
            if ($old_attachment_id) {
                wp_delete_attachment($old_attachment_id, true);
            }
            delete_user_meta($user->ID, 'twm_avatar_attachment_id');
        } else {
            return new \WP_Error('twm_avatar');
        }

        return true;
    }
}