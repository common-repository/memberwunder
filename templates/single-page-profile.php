<?php include __DIR__ . '/layout/header.php'; ?>

<?php
rewind_posts();
while (have_posts()) {
    the_post();
    ?>

    <?php $user = wp_get_current_user(); ?>

    <div class="container">
        <div class="row">
            <div class="col-sm-12 profile-headtitle">
                <h3><?php _e('PROFILE SETTINGS', TWM_TD); ?></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">

                <div class="user-profile profile--settings">
                    <div class="user-profile__container">
                        <div class="user-profile__avatar">
                            <div class="user-profile__avatar-img">
                                <img class="img-responsive avatar-url" src="<?php echo esc_attr(twmshp_get_avatar_url($user->ID)); ?>">
                            </div>
                        </div>
                        <div class="user-profile__avatar-settings">
                            <a href="" id="avatar_upload" class="btn-shadow button">
                                <i class="icon ion-edit"></i>
                            </a>
                            <a href="" id="avatar_delete" class="btn-shadow button">
                                <i class="icon ion-trash-a"></i>
                            </a>
                        </div>
                    </div>
                    <form id="avatar_upload_form" method="post" action="<?php echo twmshp_get_profile_url(); ?>" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="action" value="twm_avatar_upload" />
                        <input type="file" name="avatar" id="avatar_upload_file" multiple="false" />
                        <?php wp_nonce_field('avatar_upload', 'avatar_nonce'); ?>
                    </form>
                    <form id="avatar_delete_form" method="post" action="<?php echo twmshp_get_profile_url(); ?>" style="display: none;">
                        <input type="hidden" name="action" value="twm_avatar_delete" />
                        <?php wp_nonce_field('avatar_delete', 'avatar_nonce'); ?>
                    </form>
                </div>

            </div>
            <div class="col-sm-9 col-md-8 col-lg-6">
                <div class="Form">
                    <div class="user__profile-form js-tabs-hash">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#my-data"><?php _e('My data',TWM_TD); ?></a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#access-data"><?php _e('Access data',TWM_TD); ?></a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="my-data" class="tab-pane fade in active">
                                <?php MemberWunder\Controller\User\General::render_form_by_type('profile'); ?>
                            </div>
                            <div id="access-data" class="tab-pane fade">
                                <?php MemberWunder\Controller\User\General::render_form_by_type('profile_system'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
