<?php
/*
Plugin Name: 低端影视 API
Version: 0.1.0
Plugin URL: https://github.com/ddysiodev/ddys-emlog-plugin
Description: 低端影视 API 的官方 Emlog Pro 插件，支持前台展示、短代码、本地代理、缓存、后台诊断和求片表单。
Author: DDYS
Author URL: https://ddys.io/
*/

defined('EMLOG_ROOT') || exit('access denied!');

require_once dirname(__FILE__) . '/source/bootstrap.php';
ddys_open_bootstrap();

addAction('index_head', 'ddys_open_print_frontend_assets');
addAction('index_navi_ext', 'ddys_open_print_nav_item');
addAction('article_content_echo', 'ddys_open_article_content');
addAction('adm_head', 'ddys_open_print_admin_assets');

