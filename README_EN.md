## 📦 Out-of-the-Box MoeCounterRe
MoeCounterRe is a lightweight, highly customizable moe-style visit counter developed in PHP. It supports multiple display modes and output formats, and can be easily integrated into personal blogs, GitHub projects, or other web pages.
![Moe-counter](https://love4z.cn/moec/?name=github&theme=rule34_&min_len=7)
This is a lightweight, personalized visit counter developed with PHP and SQLite3 (similar to Moe-Counter). It supports multiple output modes and can embed images via SVG, making it perfect for GitHub profiles or blogs.
### 🚀 Project Introduction
* **Lightweight & Efficient**: Uses SQLite database, eliminating the need for cumbersome MySQL configuration—ready to use out of the box.
* **Multi-Theme Support**: Supports switching between different digit themes via image prefixes.
* **Three Output Modes**:
* `xml (SVG)`: The most recommended mode. Converts images to Base64 and embeds them into SVG, which can be used directly in GitHub `<img>` tags.
* `string`: Directly outputs plain text numbers, convenient for API calls.
* `html`: Outputs standard HTML `<img>` tag groups.
* **Performance Optimization**: Features built-in singleton pattern for database connections and supports ETag browser cache control.
### 🛠️ Usage
### 1. Deployment
Upload the code to your server, ensuring the PHP environment has the `pdo_sqlite` and `sqlite3` extensions enabled.
Make sure the directory has **write permissions** so the `Counter.db` database file can be generated.
### 2. Parameter Description
Configure via URL query parameters:
* `name`: Counter name (e.g., `index`, `github_profile`), defaults to `default`.
* `theme`: Theme prefix (must correspond to the filenames in the `img/` directory), defaults to `rule34_`.
* `min_len`: Minimum number of display digits; pads with 0s if insufficient.
* `out_mode`: Output format, options are `xml`, `string`, `html`.
### 3. Usage Examples
#### Using in GitHub Readme (Recommended)
```markdown
![Moe-counter](https://love4z.cn/moec/?name=github&theme=rule34_&min_len=7)
```
#### Directly Fetching the Number via API
```bash
curl "https://love4z.cn/moec/?name=github&out_mode=string"
```
### 📂 File Structure
* `index.php`: Program entry point, responsible for business logic and configuration.
* `lib.php`: Core library, containing database operations, security filtering, and rendering logic.
* `img/`: Image directory, storing digit images for different themes (naming convention: `prefix{0-9}.gif`).
* `Counter.db`: Auto-generated SQLite database file.
### ⚠️ Security Notes
The project includes a built-in `safeInput` function, which uses the regular expression `/[^a-zA-Z0-9_\-]/` to strictly filter all input parameters, effectively preventing **Path Traversal** and **SQL Injection** risks.
### 📄 Open Source License
This project is licensed under the MIT License. Free to use and modify.
> Project URL: https://github.com/Raven777777/MoeCounterRe

> Original Source: https://github.com/ApliNi/Moe-counter-PHP

> Tools Used: Gemini ChatGPT GLM
