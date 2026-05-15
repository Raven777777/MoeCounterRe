<?php
declare(strict_types=1);

/**
 * 安全过滤输入参数，防止路径注入和非法字符
 */
function safeInput(string $input): string {
    return preg_replace('/[^a-zA-Z0-9_\-]/', '', $input);
}

/**
 * 数据库连接（单例思想，减少重复开启）
 */
function getDB(): SQLite3 {
    static $db = null;
    if ($db === null) {
        $dbFile = __DIR__ . '/Counter.db';
        $isNew = !file_exists($dbFile);
        $db = new SQLite3($dbFile);
        $db->busyTimeout(2000);
        if ($isNew) {
            $db->exec("CREATE TABLE IF NOT EXISTS Counter (Name TEXT PRIMARY KEY, Num INTEGER)");
        }
    }
    return $db;
}

/**
 * 数据库操作：获取并自增
 */
function updateCounter(string $name, array $config): int {
    $db = getDB();
    $stmt = $db->prepare("SELECT Num FROM Counter WHERE Name = :name");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($res !== false) {
        $newNum = (int)$res['Num'] + 1;
        $upd = $db->prepare("UPDATE Counter SET Num = :num WHERE Name = :name");
        $upd->bindValue(':num', $newNum, SQLITE3_INTEGER);
        $upd->bindValue(':name', $name, SQLITE3_TEXT);
        $upd->execute();
        return $newNum;
    } else {
        // 检查是否允许创建
        $count = $db->querySingle("SELECT COUNT(*) FROM Counter");
        if ($config['maxRecordNum'] !== -1 && $count >= $config['maxRecordNum']) return 0;
        
        $ins = $db->prepare("INSERT INTO Counter (Name, Num) VALUES (:name, 1)");
        $ins->bindValue(':name', $name, SQLITE3_TEXT);
        $ins->execute();
        return 1;
    }
}

/**
 * 渲染输出
 */
function render(int $num, array $config): void {
    $minLen = (int)($_GET['min_len'] ?? $config['minNumLength']);
    $prefix = safeInput($_GET['theme'] ?? $config['img_prefix']);
    $outMode = $_GET['out_mode'] ?? $config['out_mode'];
    
    $numStr = str_pad((string)$num, $minLen, "0", STR_PAD_LEFT);
    $len = strlen($numStr);
    $w = $config['imgWidth'];
    $h = $config['imgHeight'];

    // 缓存控制
    $etag = md5($numStr . $prefix . $outMode);
    header("ETag: \"$etag\"");
    header("Cache-Control: no-cache, must-revalidate");
    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === "\"$etag\"") {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }

    if ($outMode === 'xml') {
        header("Content-Type: image/svg+xml; charset=utf-8");
        $allWidth = $w * $len;
        $imagesHtml = "";
        
        $chars = str_split($numStr);
        foreach ($chars as $index => $char) {
            $x = $index * $w;
            // 拼接本地图片路径
            $imgPath = __DIR__ . "/img/{$prefix}{$char}.gif";
            
            if (file_exists($imgPath)) {
                // 将图片内容读取并转为 Base64
                $data = base64_encode(file_get_contents($imgPath));
                $base64Img = "data:image/gif;base64," . $data;
                // 使用原生的 SVG <image> 标签
                $imagesHtml .= "<image x=\"{$x}\" y=\"0\" width=\"{$w}\" height=\"{$h}\" xlink:href=\"{$base64Img}\" />\n";
            }
        }

        echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<svg width="{$allWidth}" height="{$h}" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <title>MoeCounter</title>
    <g>
        {$imagesHtml}
    </g>
</svg>
EOF;
    } elseif ($outMode === 'string') {
        header("Content-Type: text/plain");
        echo $numStr;
    } else {
        
        header("Content-Type: text/html; charset=utf-8");
        echo "<div style=\"display:flex;\">";
        foreach (str_split($numStr) as $n) {
            echo "<img src=\"img/{$prefix}{$n}.gif\" width=\"$w\" height=\"$h\" />";
        }
        echo "</div>";
    }
}
