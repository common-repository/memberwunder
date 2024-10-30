<?php 
  include __DIR__ . '/layout/header.php'; 
  $course = twmshp_get_course( get_the_ID() );
  if ($course):
    $info = twmshp_get_course_info($course->ID);
    $course_description = empty($info['description']) ? '' : $info['description'];
    $course_thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url($course, 'twm_block_small'));
    $course_advantages = empty($info['advantages']) ? array() : $info['advantages'];
?>
<div class="TopImgBlock">
  <div class="bg" style="background-image: url('<?= TWM_TEMPLATES_URL; ?>/public/pict/01.jpg')"></div>
  <div class="container ksv-sm-m30b">
    <div class="row">
      <div class="col-sm-6">
        <div class="CourceList">
            <div class="item">
              <a href="<?php the_permalink($course); ?>" class="cntr">
                <div class="img"<?php if ($course_thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($course_thumbnail_url); ?>)"<?php } ?>></div>
                <div class="free"><?php _e( 'Free', TWM_TD );?></div>
                <!-- <div class="videos">2</div> -->
              </a>
            </div>
        </div>
      </div>
      <div class="col-sm-6 ksv-xsi-tc">
        <h1><?= get_the_title($course); ?></h1>
        <?= apply_filters('the_content', $course_description); ?>
        <p>
          <a href="<?= esc_url( twmshp_get_register_url() ); ?>" class="but green"><?php _e( 'Register', TWM_TD ); ?></a>
          <a href="<?= esc_url( twmshp_get_dashboard_url() ); ?>?mw-cr=<?= $course->ID;?>" class="but trans"><?php _e( 'Login', TWM_TD );?></a>
        </p>
      </div>
    </div>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php if (is_array($course_advantages) && !empty($course_advantages['image']) && !empty($course_advantages['title'])) { ?>
            <?php
            $n = 0;
            foreach ($course_advantages['image'] as $index => $advImage) {
                $advTitle = empty($course_advantages['title'][$index]) ? '' : $course_advantages['title'][$index];
                if ($advTitle || $advImage ) {
                    $n++;
                }
            }
            ?>
            <?php if ($n) { ?>
            <div class="blockShadow Marks ksv-xs-m15t ksv-sm-m-45t">
                <div class="row fntB text-center">
                    <?php foreach ($course_advantages['image'] as $index => $advImage) { ?>
                        <?php $advTitle = empty($course_advantages['title'][$index]) ? '' : $course_advantages['title'][$index]; ?>
                        <?php if ($advTitle || $advImage ) { ?>
                        <div class="col-sm-6 col-md-<?php echo floor(12 / $n); ?> ksv-xs-m45">
                            <img src="<?php echo esc_url($advImage); ?>" alt="" />
                            <div class="ksv-xs-h15"></div>
                            <p><?php echo esc_html($advTitle); ?></p>
                        </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php
  endif;
  include __DIR__ . '/layout/footer.php'; 
?>