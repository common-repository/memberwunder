<meta charset="<?php bloginfo('charset'); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
<?= twmshp_get_formatted_option( 'favicon', '<link rel="shortcut icon" type="image/x-icon" href="%s" />' ); ?>

<title><?php echo wp_get_document_title(); ?></title>

<script type='text/javascript'>
    /* <![CDATA[ */
    var ajax_object = {"ajax_url": "<?php echo esc_js(admin_url('admin-ajax.php')); ?>"};
    /* ]]> */
</script>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/vendor/jquery.plugin.min.js?ver=<?= TWM_VERSION;?>"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/vendor/jquery.countdown.min.js?ver=<?= TWM_VERSION;?>"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/custom/js.js?ver=<?= TWM_VERSION;?>"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/custom/video.js?ver=<?= TWM_VERSION;?>"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/custom/countdown.js?ver=<?= TWM_VERSION;?>"></script>
<script src="<?php echo TWM_TEMPLATES_URL; ?>/public/js/custom/tabs.js?ver=<?= TWM_VERSION;?>"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="<?php echo TWM_TEMPLATES_URL; ?>/public/css/ksv-bs.css?ver=<?= TWM_VERSION;?>">

<link rel="stylesheet" type="text/css" media="screen" href="<?= \MemberWunder\Controller\Pages::link_to_css();?>">

<?php
$quiz_results_id = empty($_GET['twm_id']) ? 0 : (int)$_GET['twm_id'];
if ($quiz_results_id) {
    $quiz_results_checksum = empty($_GET['twm_checksum']) ? 0 : (int)$_GET['twm_checksum'];
    $quiz_results = \MemberWunder\Mapper\Lesson::getInstance()->getTakenLessonQuizById($quiz_results_id);
    if ($quiz_results) {
        $quiz = get_post_meta($quiz_results->lesson_id, 'quiz', true);
        if ($quiz && $quiz_results_checksum === crc32($quiz_results->id . ':' . $quiz_results->lesson_id . ':' . $quiz_results->user_id)) {
            $quizTitle = empty($quiz['title']) ? '' : (string)$quiz['title'];
            $quizCertificate = empty($quiz['certificate']) ? '' : (string)$quiz['certificate'];
?>
<meta property="og:title" content="<?php echo esc_attr($quizTitle); ?>" />
<meta property="og:description" content="<?php printf(__('I have completed quiz and my result is %s', TWM_TD), $quiz_results->percent . '%'); ?>" />
<?php if ($quizCertificate) { ?>
<meta property="og:image" content="<?php echo esc_attr($quizCertificate); ?>" />
<?php } ?>
<?php
        }
    }
}
?>