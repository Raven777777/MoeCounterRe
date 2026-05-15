<?php
// 开启严格类型模式，符合 PHP 8+ 最佳实践
declare(strict_types=1);

/**
 * 获取URL参数中的name并校验长度
 */
function getName(array $config): string {
    // 使用 PHP 7/8 的 Null 合并运算符 ??
    $name = $_GET['name'] ?? '';
    
    // 不再需要 escapeString，因为我们将使用 PDO/预处理语句
    if (mb_strlen($name, 'UTF-8') >= $config['maxNameLength']) {
        die('参数超出长度限制');
    }

    return $name;
}

/**
 * 初始化并连接数据库
 */
function openDB(): SQLite3 {
    $dbFile = 'Counter.db';
    $isNewDb = !file_exists($dbFile);

    $db = new SQLite3($dbFile);
    $db->busyTimeout(2000);

    // 如果数据库刚创建，初始化表结构
    if ($isNewDb) {
        $db->exec("CREATE TABLE IF NOT EXISTS Counter (Name TEXT UNIQUE, Num INTEGER)");
    }

    return $db;
}

/**
 * 获取指定名称的数值
 */
function getNum(SQLite3 $db, string $name): int {
    // 使用预处理语句，防止 SQL 注入
    $stmt = $db->prepare("SELECT Num FROM Counter WHERE Name = :name");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    if ($result !== false) {
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row !== false && isset($row['Num'])) {
            return (int)$row['Num'];
        }
    }
    return -1;
}

/**
 * 获取数据库中的记录总数
 */
function getSum(SQLite3 $db): int {
    $result = $db->query("SELECT MAX(rowid) as max_id FROM Counter;");
    if ($result !== false) {
        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row !== false && isset($row['max_id'])) {
            return (int)$row['max_id'];
        }
    }
    return 0;
}

/**
 * 修改现有记录的数值
 */
function setNum(SQLite3 $db, string $name, int $num): bool {
    $stmt = $db->prepare("UPDATE Counter SET Num = :num WHERE Name = :name");
    $stmt->bindValue(':num', $num, SQLITE3_INTEGER);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    
    return (bool)$stmt->execute();
}

/**
 * 创建新记录
 */
function addName(SQLite3 $db, string $name, int $num = 0): bool {
    $stmt = $db->prepare("INSERT INTO Counter (Name, Num) VALUES (:name, :num)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':num', $num, SQLITE3_INTEGER);
    
    return (bool)$stmt->execute();
}

/**
 * 判断是否允许创建新记录
 */
function CANcreateRecord(array $config, SQLite3 $db): string {
    if ($config['createRecord'] !== true) {
        return '服务器不允许创建记录';
    }

    if ($config['maxRecordNum'] !== -1 && getSum($db) >= $config['maxRecordNum']) {
        return '达到记录创建限制';
    }

    return 'ok';
}

/**
 * 渲染并输出计数的图像或代码
 * 使用联合类型 int|string
 */
function renderImg(array $config, int|string $outNum): string {
    // 获取动态参数或默认配置
    $minLen = abs((int)($_GET['min_num_length'] ?? $config['minNumLength']));
    $outMode = $_GET['out_mode'] ?? $config['out_mode'];
    $imgPrefix = $_GET['img_prefix'] ?? $config['img_prefix'];
    
    // 补 0, 转换为字符串
    $outNumStr = str_pad((string)$outNum, $minLen, "0", STR_PAD_LEFT);

    $width = (int)$config['imgWidth'];
    $height = (int)$config['imgHeight'];
    $allWidth = strlen($outNumStr) * $width;

    $iM = '';

    if ($outMode === 'xml') { // XML 图片格式 (SVG)
        $chars = str_split($outNumStr);

        foreach ($chars as $key => $value) {
            $_width = $key * $width;
            $imgPath = $config['imgPath-xml'] . $imgPrefix . $value . '.' . $config['imgFormat'];
            
            // 确保文件存在再读取，避免抛出警告
            if (file_exists($imgPath)) {
                $base64Data = base64_encode(file_get_contents($imgPath));
                $img = 'data:image/' . $config['imgFormat'] . ';base64,' . $base64Data;

                $iM .= <<<EOF
                <image x="{$_width}" y="0" width="{$width}" height="{$height}" xlink:href="{$img}" />

EOF;
            }
        }

        $iM = <<<EOF
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="{$allWidth}" height="{$height}" version="1.1">
    <title>MoeCount</title>
    <g>
$iM
    </g>
</svg>
EOF;
        header("Content-Type: image/svg+xml; charset=utf-8");

    } else if ($outMode === 'html') { // HTML 代码格式
        $html_imgLocation = $_GET['align'] ?? $config['html_align'];
        $chars = str_split($outNumStr);

        foreach ($chars as $value) {
            $img = $config['imgPath-html'] . $imgPrefix . $value . '.' . $config['imgFormat'];
            $iM .= <<<EOF
<img src="{$img}" width="{$width}" height="{$height}" alt="{$value}" />

EOF;
        }

        $iM = <<<EOF
<body style="margin:0; padding:0;">
    <div style="min-width:{$allWidth}px; height:{$height}px; text-align:{$html_imgLocation};">
        $iM
    </div>
</body>
EOF;
        header("Content-Type: text/html; charset=utf-8");

    } else if ($outMode === 'string') { // 字符串格式
        $iM = $outNumStr;
        header("Content-Type: text/plain; charset=utf-8");
    }

    // 设置防缓存头
    header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
    return $iM;
}
