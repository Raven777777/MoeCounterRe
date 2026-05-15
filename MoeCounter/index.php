<?php
// 开启严格类型模式
declare(strict_types=1);

require_once 'lib.php';

// 配置文件 (使用现代的 [] 数组语法)
$c = [
    'mode' => 'ADD_NUM',
    'selectMode' => true,
    'out_mode' => 'xml',
    'html_align' => 'center',
    'createRecord' => true,
    'maxRecordNum' => 520000, 
    'maxNameLength' => 24,
    'minNumLength' => 7,
    'imgPath-html' => 'https://fastly.jsdelivr.net/gh/ApliNi/Moe-counter-PHP@main/MoeCounter/img/',
    'imgPath-xml' => 'img/',
    'img_prefix' => 'gelbooru',
    'imgFormat' => 'gif',
    'imgWidth' => 45,
    'imgHeight' => 100,
];

// 初始化输出数据
$outNum = 0;

// 获取运行模式，如果 $_GET 中没有则使用默认配置
$mode = $_GET['mode'] ?? $c['mode'];

// 路由分发
if ($mode === 'ADD_NUM') {
    $name = getName($c);
    $db = openDB();
    $num = getNum($db, $name);

    // 记录存在
    if ($num !== -1) {
        $outNum = $num + 1;
        setNum($db, $name, $outNum);
    } 
    // 记录不存在
    else {
        $status = CANcreateRecord($c, $db);
        if ($status === 'ok') {
            addName($db, $name, 0);
            $outNum = 0;
        } else {
            die($status);
        }
    }
    
    // 操作完毕，关闭数据库释放资源
    $db->close();

} else if ($mode === 'MONITOR') {
    // 强转为整型以符合严格模式的预期
    $outNum = (int)($_GET['num'] ?? 0);

} else if ($mode === 'RECORD_NUM') {
    $db = openDB();
    $outNum = getSum($db);
    $db->close();
}

// 渲染并输出结果
echo renderImg($c, $outNum);
