# 📦 开箱即用的 MoeCounterRe

MoeCounterRe 是一款基于 PHP 开发的轻量级、高度可定制的萌系访问计数器。它支持多种显示模式和输出格式，能够轻松集成到个人博客、GitHub 项目或其他网页中。

![Moe-counter](https://love4z.cn/moec/?name=github&theme=rule34_&min_len=7)

这是一个基于 PHP 和 SQLite3 开发的轻量级个性化访问计数器（类似 Moe-Counter）。它支持多种输出模式，并能通过 SVG 嵌入图片，非常适合用于 GitHub 个人主页或博客。

## 🚀 项目介绍

* **轻量高效**：使用 SQLite 数据库，无需配置繁琐的 MySQL，开箱即用。
* **多主题支持**：支持通过图片前缀切换不同的数字主题。
* **三种输出模式**：
* `xml (SVG)`：最推荐模式，将图片转为 Base64 嵌入 SVG，可直接在 GitHub `<img>` 标签中使用。
* `string`：直接输出纯文本数字，方便 API 调用。
* `html`：输出标准的 HTML `<img>` 标签组。

* **性能优化**：内置单例模式连接数据库，并支持 ETag 浏览器缓存控制。

## 🛠️ 使用方法

### 1. 部署

将代码上传至服务器，确保 PHP 环境已开启 `pdo_sqlite` 和 `sqlite3` 扩展。
确保目录具有**写权限**，以便生成 `Counter.db` 数据库文件。

### 2. 参数说明

通过 URL Query 参数进行配置：

* `name`: 计数器名称（如：`index`, `github_profile`），默认为 `default`。
* `theme`: 主题前缀（需对应 `img/` 目录下的文件名），默认为 `rule34_`。
* `min_len`: 最小显示位数，不足则补 0。
* `out_mode`: 输出格式，可选 `xml`, `string`, `html`。

### 3. 调用示例

#### 在 GitHub Readme 中使用 (推荐)

```markdown
![Moe-counter](https://love4z.cn/moec/?name=github&theme=rule34_&min_len=7)

```

#### 直接作为接口获取数字

```bash
curl "https://love4z.cn/moec/?name=github&out_mode=string"

```

## 📂 文件结构

* `index.php`: 程序入口，负责业务逻辑与配置。
* `lib.php`: 核心库，包含数据库操作、安全过滤及渲染逻辑。
* `img/`: 图片目录，存放不同主题的数字图片（命名规则：`前缀{0-9}.gif`）。
* `Counter.db`: 自动生成的 SQLite 数据库文件。

## ⚠️ 安全说明

项目已内置 `safeInput` 函数，通过正则表达式 `/[^a-zA-Z0-9_\-]/` 强制过滤所有输入参数，有效防止 **路径穿越** 和 **SQL 注入** 风险。

## 📄 开源协议

本项目遵循 MIT 协议。欢迎自由使用与二次开发。
