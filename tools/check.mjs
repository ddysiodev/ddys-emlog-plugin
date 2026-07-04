import { promises as fs } from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const root = process.cwd();
const failures = [];

const requiredFiles = [
  'README.md',
  'README.zh-CN.md',
  'LICENSE',
  '.gitignore',
  'ddys_open/ddys_open.php',
  'ddys_open/ddys_open_setting.php',
  'ddys_open/ddys_open_callback.php',
  'ddys_open/ddys_open_show.php',
  'ddys_open/preview.jpg',
  'ddys_open/source/bootstrap.php',
  'ddys_open/source/security.php',
  'ddys_open/source/cache.php',
  'ddys_open/source/client.php',
  'ddys_open/source/render.php',
  'ddys_open/source/shortcode.php',
  'ddys_open/static/css/frontend.css',
  'ddys_open/static/css/admin.css',
  'ddys_open/static/js/frontend.js',
  'ddys_open/static/js/admin.js',
  'ddys_open/static/images/icon-16.png',
  'ddys_open/static/images/icon-32.png',
  'ddys_open/static/images/icon-192.png',
  'ddys_open/static/images/icon-512.png',
  'ddys_open/static/images/logo.png',
  'ddys_open/cache/.htaccess',
  'ddys_open/cache/index.html'
];

const shortcodes = [
  'ddys_movies',
  'ddys_latest',
  'ddys_hot',
  'ddys_search',
  'ddys_suggest',
  'ddys_calendar',
  'ddys_movie',
  'ddys_sources',
  'ddys_related',
  'ddys_comments',
  'ddys_collections',
  'ddys_collection',
  'ddys_shares',
  'ddys_share',
  'ddys_requests',
  'ddys_activities',
  'ddys_user',
  'ddys_types',
  'ddys_genres',
  'ddys_regions',
  'ddys_request_form'
];

for (const file of requiredFiles) {
  await mustExist(file);
}

await checkMainFile();
await checkSettingPage();
await checkShowPage();
await checkSource();
await checkDocs();
await checkAssets();
await checkForbiddenFiles();
await checkForbiddenText();

if (failures.length > 0) {
  console.error(failures.map((failure) => `- ${failure}`).join('\n'));
  process.exit(1);
}

console.log(JSON.stringify({ ok: true, files: (await listFiles(root)).length, shortcodes: shortcodes.length }, null, 2));

async function checkMainFile() {
  const main = await read('ddys_open/ddys_open.php');
  for (const fragment of [
    'Plugin Name: 低端影视 API',
    'Version: 0.1.0',
    'Plugin URL: https://github.com/ddysiodev/ddys-emlog-plugin',
    'defined(\'EMLOG_ROOT\')',
    "addAction('index_head'",
    "addAction('index_navi_ext'",
    "addAction('article_content_echo'",
    "addAction('adm_head'"
  ]) {
    assert(main.includes(fragment), `ddys_open.php missing ${fragment}`);
  }
}

async function checkSettingPage() {
  const setting = await read('ddys_open/ddys_open_setting.php');
  for (const fragment of [
    'function plugin_setting_view',
    'function plugin_setting',
    'Storage 不可用',
    'ddys_admin_nonce',
    'test_connection',
    'clear_cache',
    'data-ddys-generator-kind',
    'data-ddys-generator-type',
    'data-ddys-api-url',
    'Output::ok',
    'Output::error'
  ]) {
    assert(setting.includes(fragment), `ddys_open_setting.php missing ${fragment}`);
  }
}

async function checkShowPage() {
  const show = await read('ddys_open/ddys_open_show.php');
  for (const fragment of [
    'showPageLink:on',
    "action === 'api'",
    "action === 'request-submit'",
    'ddys_open_proxy_response',
    'ddys_open_handle_request_form',
    'ddys_open_page_tabs',
    'ddys_open_render_page'
  ]) {
    assert(show.includes(fragment), `ddys_open_show.php missing ${fragment}`);
  }
}

async function checkSource() {
  const security = await read('ddys_open/source/security.php');
  const bootstrap = await read('ddys_open/source/bootstrap.php');
  const cache = await read('ddys_open/source/cache.php');
  const client = await read('ddys_open/source/client.php');
  const render = await read('ddys_open/source/render.php');
  const shortcode = await read('ddys_open/source/shortcode.php');
  const frontendJs = await read('ddys_open/static/js/frontend.js');
  const adminJs = await read('ddys_open/static/js/admin.js');

  for (const shortcodeName of shortcodes) {
    assert(shortcode.includes(`'${shortcodeName}'`), `shortcode.php missing ${shortcodeName}`);
  }
  for (const fragment of [
    'Storage::getInstance',
    'Input::getStrVar',
    'Input::postStrVar',
    'AUTH_KEY',
    'ddys_open_nonce',
    'ddys_open_verify_nonce',
    'ddys_open_hash_equals',
    'ddys_open_page_url',
    'ddys_open_endpoint_url'
  ]) {
    assert(security.includes(fragment), `security.php missing ${fragment}`);
  }
  for (const fragment of ['.htaccess', 'index.html']) {
    assert(bootstrap.includes(fragment), `bootstrap.php missing runtime protection marker ${fragment}`);
  }
  for (const fragment of [
    'request_*.lock',
    'ddys_open_prune_locks',
    'LOCK_EX'
  ]) {
    assert(cache.includes(fragment), `cache.php missing ${fragment}`);
  }
  for (const fragment of [
    'ddys_open_allowed_route',
    'ddys_open_proxy_response',
    'ddys_open_handle_request_form',
    'Authorization: Bearer',
    "allow_url_fopen",
    "!ini_get('open_basedir')",
    '年份格式无效',
    '豆瓣 ID 格式无效',
    'IMDb ID 格式无效',
    '备注不能超过 1000 个字符'
  ]) {
    assert(client.includes(fragment), `client.php missing ${fragment}`);
  }
  for (const fragment of [
    'ddys_open_render_page',
    'ddys_open_render_request_form',
    'ddys_open_frontend_assets',
    'ddys_open_nav_item',
    'ddys_open_render_calendar',
    'ddys_open_render_resource_links',
    'magnet:',
    'ed2k:',
    'thunder:'
  ]) {
    assert(render.includes(fragment), `render.php missing ${fragment}`);
  }
  assert(shortcode.includes('ddys_open_article_content') && shortcode.includes("log_content"), 'article content hook must replace log_content.');
  assert(frontendJs.includes('!window.fetch') && frontendJs.includes('FormData'), 'frontend request JS must gracefully fall back without fetch/FormData.');
  assert(adminJs.includes("kind === 'page'") && adminJs.includes("kind === 'proxy'") && adminJs.includes('data-ddys-generator-output'), 'admin generator JS is incomplete.');

  for (const rel of [
    'ddys_open/ddys_open.php',
    'ddys_open/ddys_open_callback.php',
    'ddys_open/source/bootstrap.php',
    'ddys_open/source/security.php',
    'ddys_open/source/cache.php',
    'ddys_open/source/client.php',
    'ddys_open/source/render.php',
    'ddys_open/source/shortcode.php'
  ]) {
    await checkBalancedPhp(rel);
  }

  for (const rel of [
    'ddys_open/ddys_open.php',
    'ddys_open/ddys_open_setting.php',
    'ddys_open/ddys_open_callback.php',
    'ddys_open/ddys_open_show.php',
    'ddys_open/source/bootstrap.php',
    'ddys_open/source/security.php',
    'ddys_open/source/cache.php',
    'ddys_open/source/client.php',
    'ddys_open/source/render.php',
    'ddys_open/source/shortcode.php'
  ]) {
    const text = await read(rel);
    assert(text.includes("defined('EMLOG_ROOT')"), `${rel} must guard direct access.`);
  }
}

async function checkDocs() {
  const zh = await read('README.zh-CN.md');
  const en = await read('README.md');
  assert(zh.includes('[English](README.md) | 简体中文'), 'Chinese README missing language link.');
  assert(en.includes('English | [简体中文](README.zh-CN.md)'), 'English README missing language link.');
  assert(zh.includes('[低端影视](https://ddys.io/) API'), 'Chinese README must link low-end movie API text.');
  assert(en.includes('[DDYS](https://ddys.io/) API'), 'English README must link DDYS API text.');
  assert(zh.includes('ddys-emlog-plugin-v0.1.0.zip') && en.includes('ddys-emlog-plugin-v0.1.0.zip'), 'README files missing release zip.');
  assert(zh.includes('content/plugins/ddys_open') && en.includes('content/plugins/ddys_open'), 'README files missing install path.');
  for (const shortcodeName of shortcodes) {
    assert(zh.includes(`[${shortcodeName}`), `Chinese README missing ${shortcodeName}`);
  }
  for (const marker of [
    '?plugin=ddys_open&view=movie&slug=this-tempting-madness',
    '?plugin=ddys_open&action=api&route=latest',
    '?plugin=ddys_open&action=request-submit',
    '/plugin/ddys_open'
  ]) {
    assert(zh.includes(marker), `Chinese README missing route marker ${marker}`);
    assert(en.includes(marker), `English README missing route marker ${marker}`);
  }
  assert(!/npm/i.test(zh + en), 'README files should not mention npm for this plugin.');
}

async function checkAssets() {
  for (const [rel, size] of [
    ['ddys_open/static/images/icon-16.png', 16],
    ['ddys_open/static/images/icon-32.png', 32],
    ['ddys_open/static/images/icon-192.png', 192],
    ['ddys_open/static/images/icon-512.png', 512],
    ['ddys_open/static/images/logo.png', 32]
  ]) {
    const buffer = await fs.readFile(path.join(root, rel));
    const dim = pngDimensions(buffer);
    assert(dim && dim.width === size && dim.height === size, `${rel} must be ${size}x${size}.`);
  }
  const preview = await fs.readFile(path.join(root, 'ddys_open/preview.jpg'));
  const dim = jpegDimensions(preview);
  assert(dim && dim.width === 75 && dim.height === 75, 'ddys_open/preview.jpg must be 75x75.');
}

async function checkForbiddenFiles() {
  const files = await listFiles(root);
  for (const full of files) {
    const rel = path.relative(root, full).replace(/\\/g, '/');
    assert(!/(^|\/)(\.env|node_modules|vendor|dist|build)(\/|$)/i.test(rel), `Forbidden file in repository: ${rel}`);
    assert(!/\.(zip|log|bak|tmp)$/i.test(rel), `Forbidden file in repository: ${rel}`);
    assert(!/^ddys_open\/cache\/.+\.php$/i.test(rel), `Runtime cache file must not be committed: ${rel}`);
    assert(!/^ddys_open\/cache\/request_.*\.lock$/i.test(rel), `Runtime lock file must not be committed: ${rel}`);
  }
}

async function checkForbiddenText() {
  const files = await listFiles(root);
  const patterns = ['ghp' + '_', 'npm' + '_', 'OpenAI', 'AI Agent', 'GPT', 'Open' + ' API', 'TODO', 'FIXME', 'var_dump', 'print_r', 'console.log'];
  for (const full of files) {
    const rel = path.relative(root, full).replace(/\\/g, '/');
    if (rel === 'tools/check.mjs' || /\.(png|jpg|jpeg|webp|gif)$/i.test(rel)) continue;
    const text = await read(rel);
    for (const pattern of patterns) {
      if (text.includes(pattern)) {
        failures.push(`${rel} contains restricted text pattern ${pattern}`);
      }
    }
  }
}

async function checkBalancedPhp(file) {
  const text = await read(file);
  assert(!text.startsWith('\uFEFF'), `${file} must not contain BOM.`);
  assert(!/\?>\s*$/.test(text), `${file} should omit closing PHP tag.`);
  const pairs = { '}': '{', ')': '(', ']': '[' };
  const stack = [];
  let quote = '';
  let escaped = false;
  for (let i = 0; i < text.length; i++) {
    const char = text[i];
    if (quote) {
      if (escaped) {
        escaped = false;
        continue;
      }
      if (char === '\\') {
        escaped = true;
        continue;
      }
      if (char === quote) quote = '';
      continue;
    }
    if (char === '"' || char === "'") {
      quote = char;
      continue;
    }
    if (char === '{' || char === '(' || char === '[') stack.push(char);
    if (char === '}' || char === ')' || char === ']') {
      const opener = stack.pop();
      if (opener !== pairs[char]) {
        failures.push(`${file} has mismatched bracket near offset ${i}.`);
        return;
      }
    }
  }
  assert(stack.length === 0, `${file} has unclosed bracket(s).`);
  assert(quote === '', `${file} has unterminated string.`);
}

function pngDimensions(buffer) {
  if (buffer.toString('ascii', 1, 4) !== 'PNG') return null;
  return { width: buffer.readUInt32BE(16), height: buffer.readUInt32BE(20) };
}

function jpegDimensions(buffer) {
  if (buffer[0] !== 0xff || buffer[1] !== 0xd8) return null;
  let offset = 2;
  while (offset < buffer.length) {
    if (buffer[offset] !== 0xff) return null;
    const marker = buffer[offset + 1];
    const length = buffer.readUInt16BE(offset + 2);
    if (marker >= 0xc0 && marker <= 0xc3) {
      return { height: buffer.readUInt16BE(offset + 5), width: buffer.readUInt16BE(offset + 7) };
    }
    offset += 2 + length;
  }
  return null;
}

async function mustExist(file) {
  try {
    await fs.access(path.join(root, file));
  } catch {
    failures.push(`Missing required file: ${file}`);
  }
}

async function read(file) {
  return fs.readFile(path.join(root, file), 'utf8');
}

async function listFiles(dir) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  const files = [];
  for (const entry of entries) {
    if (entry.name === '.git' || entry.name === 'node_modules' || entry.name === 'vendor') continue;
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...await listFiles(full));
    } else {
      files.push(full);
    }
  }
  return files;
}

function assert(condition, message) {
  if (!condition) failures.push(message);
}
