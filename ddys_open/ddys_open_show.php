<?php
/*
 * 低端影视 API 前台展示页面
 * showPageLink:on
 */

defined('EMLOG_ROOT') || exit('access denied!');

require_once dirname(__FILE__) . '/source/bootstrap.php';
ddys_open_bootstrap();

$action = ddys_open_get('action');
if ($action === 'api') {
    ddys_open_json_response(ddys_open_proxy_response());
}
if ($action === 'request-submit') {
    ddys_open_json_response(ddys_open_handle_request_form());
}

$view = ddys_open_choice(ddys_open_get('view', 'latest'), array('latest', 'hot', 'search', 'calendar', 'movie', 'collections', 'collection', 'requests'), 'latest');
$params = array(
    'q' => ddys_open_get('q'),
    'type' => ddys_open_get('type'),
    'year' => ddys_open_get('year'),
    'month' => ddys_open_get('month'),
    'slug' => ddys_open_get('slug'),
    'page' => ddys_open_get('page', '1'),
    'limit' => ddys_open_get('limit')
);
$title = ddys_open_page_title($view);
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ddys_open_h($title); ?></title>
    <?php echo ddys_open_frontend_assets(); ?>
</head>
<body class="ddys-emlog-page">
<main class="ddys-emlog-shell">
    <header class="ddys-emlog-page-header">
        <a class="ddys-emlog-brand" href="<?php echo ddys_open_attr(ddys_open_page_url('latest')); ?>">
            <img src="<?php echo ddys_open_attr(ddys_open_plugin_url('static/images/logo.png')); ?>" width="32" height="32" alt="">
            <span>低端影视</span>
        </a>
        <?php echo ddys_open_page_tabs($view); ?>
    </header>
    <section class="ddys-emlog-page-content">
        <h1><?php echo ddys_open_h($title); ?></h1>
        <?php echo ddys_open_render_page($view, $params); ?>
        <?php if ($view === 'requests') { echo ddys_open_render_request_form(array()); } ?>
    </section>
</main>
</body>
</html>

