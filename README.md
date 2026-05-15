# 📦 开箱即用的 MoeCounterRe

MoeCounterRe 是一款基于 PHP 开发的轻量级、高度可定制的萌系访问计数器。它支持多种显示模式和输出格式，能够轻松集成到个人博客、GitHub 项目或其他网页中。

![Moe-counter](https://love4z.cn/moec/?name=github&out_mode=xml)

---

## ✨ 核心特性

- **现代 PHP 支持**：深度优化，完美适配 PHP 8.0 至 PHP 8.5 环境。
- **多种输出模式**：支持 `xml` (SVG 矢量图)、`html` (外链图片) 以及纯文本格式。
- **安全可靠**：全面采用 SQLite3 预处理语句（Prepared Statements），从根本上杜绝 SQL 注入风险。
- **性能卓越**：无 `global` 变量污染，结构清晰，支持响应式布局与防缓存处理。
- **开箱即用**：自带数据库初始化逻辑，上传即可运行。

---

## 🚀 自部署开始

### 1. 部署环境
- PHP 版本 >= 8.0 (推荐 PHP 8.5)
- 开启 `SQLite3` 扩展
- 开启 `mbstring` 扩展

### 2. 安装步骤
1. 下载项目源代码。
2. 将文件上传至你的 Web 服务器目录（例如 `/moec/`）。
3. 确保目录具有读写权限，以便程序自动创建 `Counter.db` 数据库文件。

### 3. 调用方式
在你的页面中插入以下代码即可显示计数器：

#### GitHub / Markdown 环境 (推荐 XML 模式)

```

```text
File generated: MoeCounterRe_Documentation.md

```markdown
![Moe-counter](https://你的域名/moec/?name=unique_name&out_mode=xml)

```

#### 网页底部 / 博客页脚 (HTML 模式)

```html
<img src="https://你的域名/moec/?name=unique_name&out_mode=xml" alt="MoeCounter">

```

---

## ⚙️ 配置说明

在 `index.php` 的 `$c` 数组中，你可以自定义以下参数：

| 参数 | 说明 | 默认值 |
| --- | --- | --- |
| `maxNameLength` | 记录名称的最大长度 | 24 |
| `minNumLength` | 计数器显示的最小位数（不足补0） | 7 |
| `maxRecordNum` | 数据库允许创建的最大记录数 | 520000 |
| `img_prefix` | 使用的主题前缀（对应 img 目录下的文件名） | gelbooru |
| `imgWidth` / `imgHeight` | 单个数字图片的宽度和高度 | 45 / 100 |

---

## 🛠️ 高级用法

通过 URL 参数可以动态调整显示效果：

* **`name`**: 指定计数器记录的唯一标识符。
* **`mode`**: `ADD_NUM` (默认): 每次访问 +1。 `MONITOR`: 仅显示指定的数字（需配合 `&num=12345`）。 `RECORD_NUM`: 显示当前系统中总共有多少个计数器记录。


* **`out_mode`**: `xml` (推荐), `html`, `string`。
* **`theme`**: 通过 `img_prefix` 参数切换不同的数字主题。

---

## 🛡️ 安全性增强

本版本已废弃传统的字符串拼接 SQL 方式，使用绑参模式确保数据安全：

```php
$stmt = $db->prepare(\"SELECT Num FROM Counter WHERE Name = :name\");
$stmt->bindValue(':name', $name, SQLITE3_TEXT);

```

---

## 📄 开源协议

本项目遵循 MIT 协议。欢迎自由使用与二次开发。
