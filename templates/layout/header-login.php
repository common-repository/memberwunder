<!DOCTYPE html>
<html>
    <head>
        <?php include __DIR__ . '/head.php'; ?>
        <style> 
            <?= twmshp_get_formatted_option( 'login_page_background_image', 'html .bgLogin:after{background-image: url(%s) !important; }' ); ?>
        </style>
    </head>
    <body <?= twshp_body_classes(); ?>>
        <?php do_action( 'twshp_after_body_start' ); ?>
        <div class="bgLogin"></div>
        <div class="container">
            <div class="row Header">
                <div class="col-sm-6 ksv-xsi-tc">

                    <?php if (twmshp_get_option('show_logo_in_main')): ?>
                    
                        <div class="header__logo">
                            <?php $logo_footer = twmshp_get_option('logo_footer'); ?>
                            <?php if ($logo_footer) { ?>
                                <a href="<?php echo esc_url(twmshp_get_dashboard_url()); ?>" class="header__logo-link logo--fixed hidden-xs"><img src="<?php echo esc_attr($logo_footer); ?>" alt="" class="header__logo-img"/></a>
                            <?php } ?>
                            <?php $logo = twmshp_get_option('logo'); ?>
                            <?php if ($logo) { ?>
                                <a href="<?php echo esc_url(twmshp_get_dashboard_url()); ?>" class="header__logo-link logo--hero visible-xs"><img src="<?php echo esc_attr($logo); ?>" alt="" class="header__logo-img"/></a>
                            <?php } ?>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if (twmshp_users_can_register()): ?>
                    <div class="col-sm-6 ksv-xs-tc ksv-sm-p15t">
                        <br>
                        <?php _e('Don\'t have an account?',TWM_TD); ?>
                        <a href="<?php echo esc_url(twmshp_get_register_url()); ?>" class="but trans green ksv-xs-m15l"><?php _e('Get started',TWM_TD); ?></a>
                    </div>
                <?php endif; ?>

            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-6 text-center">
