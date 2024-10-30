<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div><h1><?php _e('Impressum', TWM_TD); ?></h1></div>
            <?php echo apply_filters('the_content', twmshp_get_option('impressum_content')); ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
