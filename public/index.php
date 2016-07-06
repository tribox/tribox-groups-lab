<?php

define('ROOT_PATH',  dirname(__FILE__) . '/..');
define('VIEWS_PATH', ROOT_PATH . '/views');

// ユーザ指定では無いような設定値
// (ユーザ指定設定は config.php)
define('NG_APP',     'app');
define('MAIN_TITLE', 'とらいぼっくすらぼ');

include_once ROOT_PATH . '/config.php';
include_once ROOT_PATH . '/functions.php';


//var_dump($_GET);
//var_dump($_REQUEST);
//var_dump($_SERVER);
$request_uri = $_SERVER['REQUEST_URI'];
$queries = explode('/', $request_uri);

// 末尾にスラッシュがついていたら外したURLにリダイレクト
// (ただし、スラッシュ1文字の場合は除く)
if ($request_uri !== '/' && substr($request_uri, -1) === '/') {
    $target = substr($request_uri, 0, -1);
    header('Location: ' . $target, TRUE, 302);
    exit;
}

// 表示ページ
$index_page = false;
$group_page = false;
$contest_page = false;

$cid = -1;
$title = '';
$header = '<i class="fa fa-flask"></i> ' . MAIN_TITLE;

// ルーティング
// トップページ
if ($request_uri === '/') {
    $index_page = true;
    $title = MAIN_TITLE;

    include VIEWS_PATH . '/index.tpl.php';
    exit;
}
// グループページ
else if (preg_match('/^\/[a-zA-Z0-9]{1,15}$/', $request_uri)) {
    $group_page = true;
    $tag = $queries[1];
    $title = MAIN_TITLE . ': ' . $queries[1];

    include VIEWS_PATH . '/group.tpl.php';
    exit;
}
// コンテストページ
else if (preg_match('/^\/[a-zA-Z0-9]{1,15}\/[0-9]{8}$/', $request_uri)) {
    $contest_page = true;
    $tag = $queries[1];
    $cid = $queries[2];
    $title = MAIN_TITLE . ': ' . $queries[1] . '/' . $queries[2];

    include VIEWS_PATH . '/contest.tpl.php';
    exit;
}
// 404
else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found\n";
    exit;
}
