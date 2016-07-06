<?php

define('ROOT_PATH',     dirname(__FILE__) . '/..');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

include_once ROOT_PATH . '/config.php';
include_once ROOT_PATH . '/../functions.php';


//var_dump($_GET);
//var_dump($_REQUEST);
//var_dump($_SERVER);

$URIs = explode('/', $_SERVER['REQUEST_URI']);
//var_dump($URIs);
$query = end($URIs);
//var_dump($query);

$is_index = false;
$contest_no = -1;
$title = '';

// ルーティング
// トップページ
if (empty($query) || $query === '') {
    $title = 'lab.tribox.com';
    include TEMPLATES_PATH . '/index.tpl.php';
}
// コンテスト結果
else {
    $is_index = false;
    $contest_no = (int)$query;
    $title = '戸川研コンテスト 第' . $contest_no . '回';

    // コンテストデータ読み込み
    $jsonstr = file_get_contents(dirname(__FILE__) . '/data/' . $contest_no . '.json');
    $obj = json_decode($jsonstr, true);
    //var_dump($obj);

    // データ集計
    foreach ($obj['results'] as $person => $result) {
        // (1) レコードを数値化
        $obj['results'][$person]['records_val'] = array();
        foreach ($result['records'] as $index => $record) {
            $obj['results'][$person]['records_val'][$index] = parse_record($record);
        }

        // (2) 結果を計算
        $obj['results'][$person]['result_val'] = calc_result($obj['results'][$person]['records_val']);

        // (3) 結果をフォーマットして文字列化
        $obj['results'][$person]['result'] = format_result($obj['results'][$person]['result_val']);
    }
    // (4) 順位を確定
    // TODO: 本当はアベレージが同じ場合はシングルを比較しないといけないけど今は無視
    foreach ($obj['results'] as $person => $result) {
        $key_val[$person] = $result['result_val'];
    }
    array_multisort($key_val, SORT_ASC, $obj['results']);

    include TEMPLATES_PATH . '/contest.tpl.php';
}
