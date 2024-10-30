<div class="wrap">
    <h1><?php _e( 'MemberWunder', TWM_TD ); ?></h1>
    <div id="twm-tabs">
        <ul class="nav-tab-wrapper">
            <?php 
                foreach ( \MemberWunder\Controller\Options::getSections() as $section_key => $section_label )
                    echo '<li><a href="#'.esc_attr( $section_key ).'" class="nav-tab">'.esc_html( $section_label ).'</a></li>';
            ?>
        </ul>

        <form action="options.php" method="post">
            <?php 
                settings_fields( \MemberWunder\Controller\Options::ADMIN_PAGE ); 
                foreach( \MemberWunder\Controller\Options::getSections() as $section_key => $section_label ):
            ?>
            <div id="<?= esc_attr( $section_key );?>">
                <table class="form-table">
                    <?php do_settings_fields( \MemberWunder\Controller\Options::ADMIN_PAGE, $section_key ); ?>
                </table>
            </div>
            <?php 
                endforeach;
                submit_button(); 
            ?>
        </form>
    </div>
</div>