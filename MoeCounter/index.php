<?php
declare(strict_types=1);
require_once 'lib.php';

$config = [
    'out_mode'      => 'xml',
    'img_prefix'    => 'rule34_', // 对应 img/ 目录下的前缀
    'imgWidth'      => 45,
    'imgHeight'     => 100,
    'minNumLength'  => 7,
    'maxRecordNum'  => 500000,
    'maxNameLength' => 32
];

$name = safeInput($_GET['name'] ?? 'default');

if (strlen($name) > $config['maxNameLength']) {
    die("Name too long");
}

// 执行业务逻辑并渲染
$currentNum = updateCounter($name, $config);
render($currentNum, $config);
