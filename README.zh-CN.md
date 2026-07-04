# 低端影视 API Emlog Pro 插件

[English](README.md) | 简体中文

[低端影视](https://ddys.io/) API 的官方 Emlog Pro 插件。安装后，站点可以通过前台插件页、文章短代码和本地 JSON 代理展示低端影视内容，并支持服务端求片提交。

- GitHub 仓库：[ddysiodev/ddys-emlog-plugin](https://github.com/ddysiodev/ddys-emlog-plugin)
- GitHub Release：[v0.1.0](https://github.com/ddysiodev/ddys-emlog-plugin/releases/tag/v0.1.0)
- 下载压缩包：[ddys-emlog-plugin-v0.1.0.zip](https://github.com/ddysiodev/ddys-emlog-plugin/releases/download/v0.1.0/ddys-emlog-plugin-v0.1.0.zip)
- 插件目录：`ddys_open`
- 兼容目标：Emlog Pro 插件体系
- 分发方式：GitHub Release ZIP

## 功能

- 后台配置：API Base URL、来源站点 URL、API Key、请求超时、缓存 TTL、默认数量、主题、布局、导航入口和求片开关。
- 后台诊断：连接测试、缓存目录状态、缓存文件统计、缓存清理和入口 URL 检查。
- 生成器：在后台生成短代码、前台页面链接或本地代理 URL。
- 前台页面：最新、热门、搜索、日历、影片详情、片单、片单详情和求片列表。
- 文章短代码：文章或页面内容中出现 `[ddys_*]` 时自动渲染。
- 本地 JSON 代理：浏览器请求站点本地插件页，插件服务端再请求低端影视 API。
- 服务端求片：带 nonce、限流、字段校验、错误提示，API Key 不暴露到前端。
- 缓存：按接口和参数生成文件缓存，区分字典、列表、详情和社区数据 TTL。
- 安全：Emlog 插件入口保护、Input 参数读取、路由白名单、参数白名单、输出转义、媒体 URL 校验、请求超时和敏感信息保护。

## 安装

1. 下载 Release 中的 `ddys-emlog-plugin-v0.1.0.zip`。
2. 在 Emlog Pro 后台打开 `插件 -> 上传安装`，上传 zip。
3. 或者手动解压后，将 `ddys_open` 上传到 `content/plugins/ddys_open`。
4. 进入后台插件列表，启用“低端影视 API”。
5. 打开插件设置页，填写 API Base URL、缓存时间、显示样式等配置。
6. 如果要启用求片表单，填写 API Key，并在后台执行连接测试。

## 前台入口

默认动态入口：

```text
?plugin=ddys_open
?plugin=ddys_open&view=hot
?plugin=ddys_open&view=search
?plugin=ddys_open&view=calendar
?plugin=ddys_open&view=movie&slug=this-tempting-madness
?plugin=ddys_open&view=collections
?plugin=ddys_open&view=collection&slug=editor-choice
?plugin=ddys_open&view=requests
```

如果站点启用了 Emlog Pro 的伪静态插件页，前台首页也可以通过：

```text
/plugin/ddys_open
```

访问。其他视图仍然可以追加查询参数，例如：

```text
/plugin/ddys_open?view=hot
/plugin/ddys_open?view=movie&slug=this-tempting-madness
```

本地代理示例：

```text
?plugin=ddys_open&action=api&route=latest&limit=6
?plugin=ddys_open&action=api&route=movie&slug=this-tempting-madness
?plugin=ddys_open&action=api&route=collections&page=1
```

求片提交入口：

```text
?plugin=ddys_open&action=request-submit
```

## 短代码

```text
[ddys_latest limit="12"]
[ddys_hot limit="10"]
[ddys_search]
[ddys_suggest q="星际" limit="8"]
[ddys_calendar year="2026" month="7"]
[ddys_movie slug="this-tempting-madness"]
[ddys_sources slug="this-tempting-madness"]
[ddys_related slug="this-tempting-madness"]
[ddys_comments slug="this-tempting-madness"]
[ddys_collections page="1"]
[ddys_collection slug="editor-choice"]
[ddys_shares page="1"]
[ddys_share id="1"]
[ddys_requests page="1"]
[ddys_activities page="1"]
[ddys_user username="demo"]
[ddys_types]
[ddys_genres]
[ddys_regions]
[ddys_request_form]
```

还支持完整影片列表：

```text
[ddys_movies type="movie" genre="drama" region="us" year="2026" sort="latest" page="1" per_page="12"]
```

## 缓存

缓存文件位于：

```text
content/plugins/ddys_open/cache
```

插件会写入 `.htaccess` 和 `index.html`，减少缓存文件被直接访问的风险。后台可以随时清理缓存。

## 伪静态

Emlog Pro 已内置插件页路由。站点开启伪静态后，确认 `/plugin/ddys_open` 能访问即可。Nginx/Apache 规则以 Emlog Pro 官方伪静态规则为准，本插件不需要额外改写内部 PHP 文件。

## 开发检查

在仓库根目录运行：

```powershell
node tools/check.mjs
```

检查覆盖 Emlog 插件结构、主文件元数据、前台页、设置页、短代码覆盖、代理、求片、缓存、安全文案、图标尺寸和敏感文件。
