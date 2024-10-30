</div>
</div>
</div>

<div class="FooterBottom">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-6 text-center">
                <div class="footer__nav">
                    <?= strip_tags( do_shortcode( '[memberwunder-menu name="guest"]' ), '<a>' ); ?>
                </div>
            </div>
            <?php \MemberWunder\Helpers\Template\General::copyright( '<div class="col-sm-6 col-sm-offset-6 text-center"><div class="text__privacy">%s</div></div>' );?>
        </div>
    </div>
</div>
    <?php do_action( 'twshp_before_body_end' ); ?>
</body>
</html>				