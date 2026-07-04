<?php

defined('EMLOG_ROOT') || exit('access denied!');

require_once dirname(__FILE__) . '/source/bootstrap.php';
ddys_open_bootstrap();

function plugin_setting_view()
{
    $settings = ddys_open_settings();
    $stats = ddys_open_cache_stats();
    $shortcodes = ddys_open_shortcodes();
    $nonce = ddys_open_nonce('admin');
    ?>
    <div class="ddys-emlog-admin">
        <div class="ddys-emlog-admin-head">
            <img src="<?php echo ddys_open_attr(ddys_open_plugin_url('static/images/icon-32.png')); ?>" width="32" height="32" alt="">
            <div>
                <h1 class="h4 mb-1 text-gray-800">低端影视 API</h1>
                <p>官方 Emlog Pro 插件，支持前台页面、短代码、本地代理、缓存、诊断和求片表单。</p>
            </div>
        </div>

        <div class="ddys-emlog-grid">
            <section class="card shadow mb-4">
                <div class="card-header py-3"><h2 class="h6 m-0 font-weight-bold text-primary">基础配置</h2></div>
                <div class="card-body">
                    <form method="post" action="./plugin.php?plugin=ddys_open&action=save_setting" data-ddys-emlog-form>
                        <input type="hidden" name="ddys_admin_nonce" value="<?php echo ddys_open_attr($nonce); ?>">
                        <input type="hidden" name="ddys_admin_action" value="save_settings">
                        <div class="form-group">
                            <label>API Base URL</label>
                            <input class="form-control" name="api_base_url" value="<?php echo ddys_open_attr($settings['api_base_url']); ?>" placeholder="https://ddys.io/api/v1">
                        </div>
                        <div class="form-group">
                            <label>来源站点 URL</label>
                            <input class="form-control" name="site_base_url" value="<?php echo ddys_open_attr($settings['site_base_url']); ?>" placeholder="https://ddys.io">
                        </div>
                        <div class="form-group">
                            <label>API Key</label>
                            <input class="form-control" type="password" name="api_key" value="<?php echo ddys_open_attr($settings['api_key']); ?>" autocomplete="off">
                            <small class="form-text text-muted">仅服务端求片等写接口使用，不会输出到前台页面。</small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"><label>请求超时</label><input class="form-control" type="number" min="1" max="30" name="timeout" value="<?php echo ddys_open_attr($settings['timeout']); ?>"></div>
                            <div class="form-group col-md-4"><label>默认数量</label><input class="form-control" type="number" min="1" max="50" name="default_limit" value="<?php echo ddys_open_attr($settings['default_limit']); ?>"></div>
                            <div class="form-group col-md-4"><label>求片间隔秒数</label><input class="form-control" type="number" min="10" max="3600" name="request_interval" value="<?php echo ddys_open_attr($settings['request_interval']); ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"><label>字典缓存 TTL</label><input class="form-control" type="number" min="0" max="604800" name="dictionary_cache_ttl" value="<?php echo ddys_open_attr($settings['dictionary_cache_ttl']); ?>"></div>
                            <div class="form-group col-md-4"><label>最新/热门 TTL</label><input class="form-control" type="number" min="0" max="604800" name="fresh_cache_ttl" value="<?php echo ddys_open_attr($settings['fresh_cache_ttl']); ?>"></div>
                            <div class="form-group col-md-4"><label>列表缓存 TTL</label><input class="form-control" type="number" min="0" max="604800" name="list_cache_ttl" value="<?php echo ddys_open_attr($settings['list_cache_ttl']); ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"><label>详情缓存 TTL</label><input class="form-control" type="number" min="0" max="604800" name="detail_cache_ttl" value="<?php echo ddys_open_attr($settings['detail_cache_ttl']); ?>"></div>
                            <div class="form-group col-md-4"><label>社区缓存 TTL</label><input class="form-control" type="number" min="0" max="604800" name="community_cache_ttl" value="<?php echo ddys_open_attr($settings['community_cache_ttl']); ?>"></div>
                            <div class="form-group col-md-4"><label>默认缓存 TTL</label><input class="form-control" type="number" min="0" max="604800" name="default_cache_ttl" value="<?php echo ddys_open_attr($settings['default_cache_ttl']); ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>主题</label>
                                <select class="form-control" name="theme">
                                    <?php echo ddys_open_admin_option('auto', '自动', $settings['theme']); ?>
                                    <?php echo ddys_open_admin_option('light', '浅色', $settings['theme']); ?>
                                    <?php echo ddys_open_admin_option('dark', '深色', $settings['theme']); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>布局</label>
                                <select class="form-control" name="layout">
                                    <?php echo ddys_open_admin_option('grid', '网格', $settings['layout']); ?>
                                    <?php echo ddys_open_admin_option('list', '列表', $settings['layout']); ?>
                                    <?php echo ddys_open_admin_option('compact', '紧凑', $settings['layout']); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4"><label>列数</label><input class="form-control" type="number" min="1" max="6" name="columns" value="<?php echo ddys_open_attr($settings['columns']); ?>"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>打开方式</label>
                                <select class="form-control" name="target">
                                    <?php echo ddys_open_admin_option('_blank', '新窗口', $settings['target']); ?>
                                    <?php echo ddys_open_admin_option('_self', '当前窗口', $settings['target']); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-8 ddys-emlog-checks">
                                <?php echo ddys_open_admin_checkbox('show_source_link', '显示来源链接', $settings['show_source_link']); ?>
                                <?php echo ddys_open_admin_checkbox('enable_styles', '加载默认样式', $settings['enable_styles']); ?>
                                <?php echo ddys_open_admin_checkbox('enable_request_form', '启用求片表单', $settings['enable_request_form']); ?>
                                <?php echo ddys_open_admin_checkbox('show_nav', '显示导航入口', $settings['show_nav']); ?>
                                <?php echo ddys_open_admin_checkbox('debug', '调试模式', $settings['debug']); ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">保存配置</button>
                        <span class="ddys-emlog-admin-status" role="status"></span>
                    </form>
                </div>
            </section>

            <aside>
                <section class="card shadow mb-4">
                    <div class="card-header py-3"><h2 class="h6 m-0 font-weight-bold text-primary">诊断</h2></div>
                    <div class="card-body">
                        <dl class="ddys-emlog-diagnostics">
                            <dt>前台首页</dt><dd><a href="<?php echo ddys_open_attr(ddys_open_page_url('latest')); ?>" target="_blank" rel="noopener"><?php echo ddys_open_h(ddys_open_page_url('latest')); ?></a></dd>
                            <dt>本地代理</dt><dd><code><?php echo ddys_open_h(ddys_open_endpoint_url('api') . '&route=latest&limit=6'); ?></code></dd>
                            <dt>缓存目录</dt><dd><code><?php echo ddys_open_h(ddys_open_cache_dir()); ?></code></dd>
                            <dt>缓存状态</dt><dd><?php echo $stats['writable'] ? '可写' : '不可写'; ?>，<?php echo (int)$stats['files']; ?> 个文件，<?php echo (int)$stats['expired']; ?> 个过期，<?php echo ddys_open_h(ddys_open_format_bytes($stats['size'])); ?></dd>
                            <dt>请求能力</dt><dd><?php echo function_exists('curl_init') ? 'cURL 可用' : (ini_get('allow_url_fopen') ? 'allow_url_fopen 可用' : '不可用'); ?></dd>
                        </dl>
                        <form method="post" action="./plugin.php?plugin=ddys_open&action=save_setting" data-ddys-emlog-tool>
                            <input type="hidden" name="ddys_admin_nonce" value="<?php echo ddys_open_attr($nonce); ?>">
                            <input type="hidden" name="ddys_admin_action" value="test_connection">
                            <button class="btn btn-outline-primary btn-sm" type="submit">连接测试</button>
                            <span class="ddys-emlog-admin-status" role="status"></span>
                        </form>
                        <form method="post" action="./plugin.php?plugin=ddys_open&action=save_setting" data-ddys-emlog-tool>
                            <input type="hidden" name="ddys_admin_nonce" value="<?php echo ddys_open_attr($nonce); ?>">
                            <input type="hidden" name="ddys_admin_action" value="clear_cache">
                            <button class="btn btn-outline-danger btn-sm" type="submit">清理缓存</button>
                            <span class="ddys-emlog-admin-status" role="status"></span>
                        </form>
                    </div>
                </section>

                <section class="card shadow mb-4">
                    <div class="card-header py-3"><h2 class="h6 m-0 font-weight-bold text-primary">生成器</h2></div>
                    <div class="card-body ddys-emlog-generator" data-ddys-site-root="<?php echo ddys_open_attr(ddys_open_site_root()); ?>" data-ddys-api-url="<?php echo ddys_open_attr(ddys_open_endpoint_url('api')); ?>">
                        <label>输出类型</label>
                        <select class="form-control" data-ddys-generator-kind>
                            <option value="shortcode">短代码</option>
                            <option value="page">前台页面链接</option>
                            <option value="proxy">本地代理 URL</option>
                        </select>
                        <label>组件</label>
                        <select class="form-control" data-ddys-generator-type>
                            <?php foreach ($shortcodes as $shortcode): ?>
                                <option value="<?php echo ddys_open_attr($shortcode); ?>"><?php echo ddys_open_h($shortcode); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>数量</label><input class="form-control" data-ddys-generator-limit value="<?php echo ddys_open_attr($settings['default_limit']); ?>"></div>
                            <div class="form-group col-md-6"><label>页码</label><input class="form-control" data-ddys-generator-page value="1"></div>
                        </div>
                        <label>slug / id / username / 搜索词</label>
                        <input class="form-control" data-ddys-generator-value placeholder="this-tempting-madness">
                        <textarea class="form-control ddys-emlog-output" rows="4" readonly data-ddys-generator-output></textarea>
                        <button type="button" class="btn btn-secondary btn-sm" data-ddys-copy>复制</button>
                    </div>
                </section>
            </aside>
        </div>
    </div>
    <script>
        if (window.jQuery) {
            jQuery("#menu_category_ext").addClass("active");
        }
    </script>
<?php }

function plugin_setting()
{
    $action = ddys_open_post('ddys_admin_action', 'save_settings');
    if (!ddys_open_verify_nonce(ddys_open_post('ddys_admin_nonce'), 'admin')) {
        ddys_open_admin_error('后台表单令牌无效，请刷新页面后重试。');
    }

    if ($action === 'clear_cache') {
        $count = ddys_open_cache_clear();
        ddys_open_admin_ok('已清理 ' . $count . ' 个缓存文件。');
    }

    if ($action === 'test_connection') {
        $payload = ddys_open_api_get('/latest', array('limit' => 1), array('no_cache' => true));
        if (ddys_open_is_error($payload)) {
            ddys_open_admin_error($payload['message']);
        }
        ddys_open_admin_ok('连接成功，低端影视 API 返回正常。');
    }

    $settings = ddys_open_settings();
    foreach (array('api_base_url', 'site_base_url', 'api_key', 'theme', 'layout', 'target') as $key) {
        $settings[$key] = ddys_open_post($key, $settings[$key]);
    }
    foreach (array('timeout', 'default_limit', 'request_interval', 'default_cache_ttl', 'dictionary_cache_ttl', 'fresh_cache_ttl', 'list_cache_ttl', 'detail_cache_ttl', 'community_cache_ttl', 'columns') as $key) {
        $settings[$key] = ddys_open_post($key, $settings[$key]);
    }
    foreach (array('show_source_link', 'enable_styles', 'enable_request_form', 'show_nav', 'debug') as $key) {
        $settings[$key] = ddys_open_post($key, '') === '1' ? 1 : 0;
    }

    if (!ddys_open_save_settings($settings)) {
        ddys_open_admin_error('保存失败，Emlog Storage 不可用。');
    }
    ddys_open_admin_ok('保存成功。');
}

function ddys_open_admin_option($value, $label, $current)
{
    return '<option value="' . ddys_open_attr($value) . '"' . ((string)$value === (string)$current ? ' selected' : '') . '>' . ddys_open_h($label) . '</option>';
}

function ddys_open_admin_checkbox($name, $label, $checked)
{
    return '<label class="ddys-emlog-check"><input type="checkbox" name="' . ddys_open_attr($name) . '" value="1"' . (!empty($checked) ? ' checked' : '') . '> ' . ddys_open_h($label) . '</label>';
}

function ddys_open_format_bytes($bytes)
{
    $bytes = (int)$bytes;
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

function ddys_open_admin_ok($message)
{
    if (class_exists('Output')) {
        Output::ok($message);
    }
    ddys_open_json_response(array('code' => 0, 'msg' => 'ok', 'data' => $message));
}

function ddys_open_admin_error($message)
{
    if (class_exists('Output')) {
        Output::error($message);
    }
    ddys_open_json_response(array('code' => 1, 'msg' => $message, 'data' => ''), 400);
}
