<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div><h1><?php _e('AGB', TWM_TD); ?></h1></div>
            <?php echo apply_filters('the_content', twmshp_get_option('agb_content')); ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
