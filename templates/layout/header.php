<?php $MenuTopHide = get_current_user_id() ? 0 : 1; ?>
<!DOCTYPE html>
<html>
    <head>
        <?php include __DIR__ . '/head.php'; ?>
    </head>
    <body <?php body_class(); ?>>
        <?php do_action( 'twshp_after_body_start' ); ?>
        <?php echo '<div class="page">'; ?>
        <?php echo '<div class="inner">'; ?>
        
        <header class="header">
            <div class="header-container">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3 col-md-4">
                            <div class="header__logo">
                                <div
                                    class="header__logo-nav visible-xs"
                                    data-toggle="collapse"
                                    data-target="#TopMenus"
                                    aria-expanded="false">
                                    <i class="icon ion-android-menu"></i>
                                    <i class="icon ion-android-close"></i>
                                </div>
                                <?php $logo = twmshp_get_option('logo'); ?>
                                <?php if ($logo) { ?>
                                    <a href="<?php echo esc_url(twmshp_get_dashboard_url()); ?>" class="header__logo-link"><img src="<?php echo esc_attr($logo); ?>" alt="" class="header__logo-img" /></a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="col-sm-9 col-md-8">
                            <?php if (empty($MenuTopHide)) { ?>
                            <div id="TopMenus" class="collapse navbar-collapse header__nav-container">
                                <div class="header__nav">
                                    <?= do_shortcode('[memberwunder-menu name="header"]'); ?>
                                    
                                    <div class="header__nav-personal">
                                        <ul class="header__nav-list">
                                            <li class="header__nav-item">
                                                <a href="<?php echo esc_url(twmshp_get_profile_url()); ?>" class="header__nav-link">
                                                    <i class="icon ion-gear-a"></i>
                                                    <span><?php _e( 'Settings', TWM_TD );?></span>
                                                </a>
                                            </li>
                                            <li class="header__nav-item">
                                                <a href="<?php echo wp_logout_url(twmshp_get_dashboard_url()); ?>" class="header__nav-link">
                                                    <i class="icon ion-android-exit"></i>
                                                    <span><?php _e( 'Logout', TWM_TD );?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </header> 
