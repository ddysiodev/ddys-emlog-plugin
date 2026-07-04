<?php

defined('EMLOG_ROOT') || exit('access denied!');

function ddys_open_defaults()
{
    return array(
        'api_base_url' => DDYS_OPEN_EMLOG_API_DEFAULT,
        'site_base_url' => DDYS_OPEN_EMLOG_SITE_DEFAULT,
        'api_key' => '',
        'timeout' => 12,
        'default_cache_ttl' => 300,
        'dictionary_cache_ttl' => 86400,
        'fresh_cache_ttl' => 300,
        'list_cache_ttl' => 600,
        'detail_cache_ttl' => 1800,
        'community_cache_ttl' => 120,
        'theme' => 'auto',
        'layout' => 'grid',
        'columns' => 4,
        'target' => '_blank',
        'show_source_link' => 1,
        'enable_styles' => 1,
        'enable_request_form' => 0,
        'show_nav' => 1,
        'default_limit' => 12,
        'request_interval' => 60,
        'debug' => 0
    );
}

function ddys_open_settings()
{
    $saved = array();
    if (class_exists('Storage')) {
        $value = Storage::getInstance(DDYS_OPEN_EMLOG_ID)->getValue('settings');
        if (is_array($value)) {
            $saved = $value;
        }
    }
    return ddys_open_normalize_settings(array_merge(ddys_open_defaults(), $saved));
}

function ddys_open_normalize_settings($settings)
{
    $settings['api_base_url'] = ddys_open_normalize_base_url(isset($settings['api_base_url']) ? $settings['api_base_url'] : '', DDYS_OPEN_EMLOG_API_DEFAULT);
    $settings['site_base_url'] = ddys_open_normalize_base_url(isset($settings['site_base_url']) ? $settings['site_base_url'] : '', DDYS_OPEN_EMLOG_SITE_DEFAULT);
    $settings['api_key'] = trim((string)(isset($settings['api_key']) ? $settings['api_key'] : ''));
    $settings['timeout'] = ddys_open_int_range(isset($settings['timeout']) ? $settings['timeout'] : 12, 12, 1, 30);
    $settings['default_cache_ttl'] = ddys_open_int_range(isset($settings['default_cache_ttl']) ? $settings['default_cache_ttl'] : 300, 300, 0, 604800);
    $settings['dictionary_cache_ttl'] = ddys_open_int_range(isset($settings['dictionary_cache_ttl']) ? $settings['dictionary_cache_ttl'] : 86400, 86400, 0, 604800);
    $settings['fresh_cache_ttl'] = ddys_open_int_range(isset($settings['fresh_cache_ttl']) ? $settings['fresh_cache_ttl'] : 300, 300, 0, 604800);
    $settings['list_cache_ttl'] = ddys_open_int_range(isset($settings['list_cache_ttl']) ? $settings['list_cache_ttl'] : 600, 600, 0, 604800);
    $settings['detail_cache_ttl'] = ddys_open_int_range(isset($settings['detail_cache_ttl']) ? $settings['detail_cache_ttl'] : 1800, 1800, 0, 604800);
    $settings['community_cache_ttl'] = ddys_open_int_range(isset($settings['community_cache_ttl']) ? $settings['community_cache_ttl'] : 120, 120, 0, 604800);
    $settings['theme'] = ddys_open_choice(isset($settings['theme']) ? $settings['theme'] : 'auto', array('auto', 'light', 'dark'), 'auto');
    $settings['layout'] = ddys_open_choice(isset($settings['layout']) ? $settings['layout'] : 'grid', array('grid', 'list', 'compact'), 'grid');
    $settings['columns'] = ddys_open_int_range(isset($settings['columns']) ? $settings['columns'] : 4, 4, 1, 6);
    $settings['target'] = ddys_open_choice(isset($settings['target']) ? $settings['target'] : '_blank', array('_blank', '_self'), '_blank');
    $settings['default_limit'] = ddys_open_int_range(isset($settings['default_limit']) ? $settings['default_limit'] : 12, 12, 1, 50);
    $settings['request_interval'] = ddys_open_int_range(isset($settings['request_interval']) ? $settings['request_interval'] : 60, 60, 10, 3600);
    foreach (array('show_source_link', 'enable_styles', 'enable_request_form', 'show_nav', 'debug') as $key) {
        $settings[$key] = !empty($settings[$key]) && ddys_open_bool($settings[$key]) ? 1 : 0;
    }
    return $settings;
}

function ddys_open_save_settings($settings)
{
    if (!class_exists('Storage')) {
        return false;
    }
    Storage::getInstance(DDYS_OPEN_EMLOG_ID)->setValue('settings', ddys_open_normalize_settings($settings), 'array');
    return true;
}

function ddys_open_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function ddys_open_attr($value)
{
    return ddys_open_h($value);
}

function ddys_open_get($key, $default = '')
{
    if (class_exists('Input')) {
        return ddys_open_scalar(stripslashes(Input::getStrVar($key, $default)), $default);
    }
    return isset($_GET[$key]) ? ddys_open_scalar($_GET[$key], $default) : $default;
}

function ddys_open_post($key, $default = '')
{
    if (class_exists('Input')) {
        return ddys_open_scalar(stripslashes(Input::postStrVar($key, $default)), $default);
    }
    return isset($_POST[$key]) ? ddys_open_scalar($_POST[$key], $default) : $default;
}

function ddys_open_post_array($key)
{
    if (isset($_POST[$key]) && is_array($_POST[$key])) {
        return $_POST[$key];
    }
    return array();
}

function ddys_open_scalar($value, $default = '')
{
    if (is_array($value) || is_object($value)) {
        return $default;
    }
    return trim(str_replace("\0", '', (string)$value));
}

function ddys_open_bool($value)
{
    if (is_bool($value)) {
        return $value;
    }
    return in_array(strtolower(trim((string)$value)), array('1', 'true', 'yes', 'on'), true);
}

function ddys_open_int_range($value, $fallback, $min, $max)
{
    if (!is_numeric($value)) {
        return $fallback;
    }
    $value = (int)$value;
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

function ddys_open_choice($value, $allowed, $fallback)
{
    $value = strtolower(trim((string)$value));
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function ddys_open_normalize_base_url($value, $fallback)
{
    $value = trim((string)$value);
    if ($value === '' || !preg_match('#^https?://#i', $value)) {
        return $fallback;
    }
    $parts = parse_url($value);
    if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host']) || !empty($parts['user']) || !empty($parts['pass'])) {
        return $fallback;
    }
    return rtrim($value, '/');
}

function ddys_open_normalize_query_value($key, $value)
{
    $value = ddys_open_scalar($value);
    if ($value === '') {
        return '';
    }
    if ($key === 'limit' || $key === 'per_page') {
        return ddys_open_int_range($value, 12, 1, 50);
    }
    if ($key === 'page') {
        return ddys_open_int_range($value, 1, 1, 999);
    }
    if ($key === 'year') {
        return ddys_open_int_range($value, 0, 0, 2099);
    }
    if ($key === 'month') {
        return ddys_open_int_range($value, 0, 0, 12);
    }
    return $value;
}

function ddys_open_build_query($source, $keys)
{
    $query = array();
    foreach ($keys as $key) {
        if (isset($source[$key]) && ddys_open_scalar($source[$key]) !== '') {
            $query[$key] = ddys_open_normalize_query_value($key, $source[$key]);
        }
    }
    return $query;
}

function ddys_open_site_root()
{
    if (defined('BLOG_URL')) {
        return rtrim(BLOG_URL, '/') . '/';
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $script = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';
    return $host === '' ? './' : $scheme . '://' . $host . rtrim(str_replace('\\', '/', $script), '/') . '/';
}

function ddys_open_plugin_url($path = '')
{
    if (defined('PLUGIN_URL')) {
        $base = rtrim(PLUGIN_URL, '/') . '/' . DDYS_OPEN_EMLOG_ID . '/';
    } else {
        $base = ddys_open_site_root() . 'content/plugins/' . DDYS_OPEN_EMLOG_ID . '/';
    }
    return $base . ltrim($path, '/');
}

function ddys_open_page_url($view = 'latest', $params = array())
{
    $view = ddys_open_choice($view, array('latest', 'hot', 'search', 'calendar', 'movie', 'collections', 'collection', 'requests'), 'latest');
    $query = array_merge(array('plugin' => DDYS_OPEN_EMLOG_ID, 'view' => $view), (array)$params);
    if ($view === 'latest') {
        unset($query['view']);
    }
    return ddys_open_append_query(ddys_open_site_root(), $query);
}

function ddys_open_endpoint_url($endpoint)
{
    $action = $endpoint === 'request' ? 'request-submit' : 'api';
    return ddys_open_append_query(ddys_open_site_root(), array('plugin' => DDYS_OPEN_EMLOG_ID, 'action' => $action));
}

function ddys_open_admin_url($params = array())
{
    return ddys_open_append_query('./plugin.php', array_merge(array('plugin' => DDYS_OPEN_EMLOG_ID), $params));
}

function ddys_open_append_query($url, $params)
{
    $clean = array();
    foreach ((array)$params as $key => $value) {
        $value = ddys_open_scalar($value);
        if ($value !== '') {
            $clean[$key] = $value;
        }
    }
    if (empty($clean)) {
        return $url;
    }
    return $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($clean, '', '&');
}

function ddys_open_nonce($context = 'front')
{
    $seed = ddys_open_nonce_seed($context);
    $slot = floor(time() / 3600);
    return ddys_open_hash($seed . '|' . $slot);
}

function ddys_open_verify_nonce($nonce, $context = 'front')
{
    $seed = ddys_open_nonce_seed($context);
    $slot = floor(time() / 3600);
    return ddys_open_hash_equals(ddys_open_hash($seed . '|' . $slot), $nonce)
        || ddys_open_hash_equals(ddys_open_hash($seed . '|' . ($slot - 1)), $nonce);
}

function ddys_open_nonce_seed($context)
{
    $key = defined('AUTH_KEY') ? AUTH_KEY : 'ddys-open-emlog';
    $uid = defined('UID') ? UID : 0;
    return $key . '|' . $context . '|' . $uid . '|' . ddys_open_user_ip();
}

function ddys_open_hash($value)
{
    $key = defined('AUTH_KEY') ? AUTH_KEY : 'ddys-open-emlog';
    if (function_exists('hash_hmac')) {
        return hash_hmac('sha256', $value, $key);
    }
    return sha1($key . '|' . $value);
}

function ddys_open_hash_equals($known, $user)
{
    if (function_exists('hash_equals')) {
        return hash_equals((string)$known, (string)$user);
    }
    $known = (string)$known;
    $user = (string)$user;
    if (strlen($known) !== strlen($user)) {
        return false;
    }
    $result = 0;
    for ($i = 0; $i < strlen($known); $i++) {
        $result |= ord($known[$i]) ^ ord($user[$i]);
    }
    return $result === 0;
}

function ddys_open_user_ip()
{
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

function ddys_open_json_response($payload, $status = 200)
{
    if ($status === 200 && ddys_open_is_error($payload) && !empty($payload['status'])) {
        $status = ddys_open_int_range($payload['status'], 500, 400, 599);
    }
    if (!headers_sent()) {
        if (function_exists('http_response_code')) {
            http_response_code($status);
        }
        header('Content-Type: application/json; charset=utf-8', true, $status);
    }
    echo json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    exit;
}

function ddys_open_error($message, $status = 0, $payload = array())
{
    return array(
        'ddys_error' => true,
        'success' => false,
        'message' => (string)$message,
        'status' => (int)$status,
        'payload' => $payload
    );
}

function ddys_open_is_error($value)
{
    return is_array($value) && !empty($value['ddys_error']);
}

function ddys_open_safe_media_url($value)
{
    $value = trim((string)$value);
    return preg_match('#^https?://#i', $value) ? $value : '';
}

function ddys_open_substr($value, $start, $length)
{
    $value = (string)$value;
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }
    return substr($value, $start, $length);
}

function ddys_open_strlen($value)
{
    $value = (string)$value;
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    return strlen($value);
}

