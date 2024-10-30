<!DOCTYPE html>
<html>
<head>
	<?php include __DIR__ . '/head.php';?>
	<style>	
		<?= twmshp_get_formatted_option( 'registration_background', 'html .bgReg:after{background-image: url(%s) !important; }' ); ?>
	</style>
</head>
<body <?= twshp_body_classes(); ?>>
	<?php do_action( 'twshp_after_body_start' ); ?>
<div class="bgReg"></div>
<div class="container">
	<div class="row Header">
		<div class="col-sm-6 ksv-xsi-tc">
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
		</div>
            
            
	</div>
	<div class="row">
		<div class="_col-xs-6 _col-xs-offset-3 _col-sm-4 _col-sm-offset-7 col-xs-12 col-sm-6 col-sm-offset-6 text-center">