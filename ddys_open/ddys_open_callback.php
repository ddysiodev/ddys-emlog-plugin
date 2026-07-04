<?php

defined('EMLOG_ROOT') || exit('access denied!');

require_once dirname(__FILE__) . '/source/bootstrap.php';
ddys_open_bootstrap();

function callback_init()
{
    ddys_open_save_settings(ddys_open_settings());
    ddys_open_ensure_runtime();
}

function callback_rm()
{
    ddys_open_cache_clear();
    if (class_exists('Storage')) {
        $storage = Storage::getInstance('ddys_open');
        $storage->deleteAllName('YES');
    }
}

function callback_up()
{
    ddys_open_save_settings(ddys_open_settings());
    ddys_open_ensure_runtime();
}

