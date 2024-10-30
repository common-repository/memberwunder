<?php $social = twmshp_get_option('social'); ?>

<?php if (is_array($social) && !empty($social['image']) && !empty($social['url'])) { ?>
    <div class="container BlockList1">
        <?= twmshp_get_formatted_option('social_header', '<h3>%s</h3>', '<h3>' . __('FOLLOW US', TWM_TD) . '</h3>'); ?>

        <div class="ExpertList">

            <?php foreach ($social['url'] as $index => $socialUrl) { ?>
                <?php $socialImage = empty($social['image'][$index]) ? '' : $social['image'][$index]; ?>

                <a <?php if ($socialUrl): ?>href="<?php echo esc_url($socialUrl); ?>"<?php endif; ?>
                                            target="_blank"
                                            class="item">
                                                <?php if ($socialImage): ?>
                        <div class="img" style="background-image: url(<?php echo $socialImage; ?>)"></div>
                    <?php endif; ?>
                    <div class="info">
                        <?php if ($social['label'][$index]): ?>
                            <div class="name"><?php echo $social['label'][$index]; ?></div>
                        <?php endif; ?>
                        <?php if ($social['description'][$index]): ?>
                            <div class="desc"><?php echo $social['description'][$index]; ?></div>
                        <?php endif; ?>
                    </div>
                </a>            
            <?php } ?>

        </div>


    </div>
<?php } ?>


<?php echo '</div>'; ?>

<footer class="footer">
    <div class="footer-container">
        <div class="footer__nav">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="footer__logo">
                            <?php $logo_footer = twmshp_get_option('logo_footer'); ?>
                            <?php if ($logo_footer) { ?>
                                <a href="<?php echo esc_url(twmshp_get_dashboard_url()); ?>" class="footer__logo-link">
                                    <img src="<?php echo esc_attr($logo_footer); ?>" alt="" class="header__logo-img"/>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <?= do_shortcode( '[memberwunder-menu name="'.( empty( $MenuTopHide ) ? 'footer' : 'guest' ).'"]' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php \MemberWunder\Helpers\Template\General::copyright( '<div class="container"><div class="row"><div class="col-md-12 text-center"><div class="text__privacy">%s</div></div></div></div>' ); ?>
    </div>
</footer>

<?php echo '</div>'; ?>
<?php do_action('twshp_before_body_end'); ?>
</body>
</html>