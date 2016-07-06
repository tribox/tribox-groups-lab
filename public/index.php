<?php

include_once dirname(__FILE__) . '/../config.php';

// 結果をパースする
// (例) "1:23.456" --> 83.456
function parse_record($record) {
    $record = (string)$record;
    if (strpos($record, ':') === FALSE) {
        return (double)$record;
    } else {
        $t = explode(':', $record);
        return 60.0 * (double)$t[0] + (double)$t[1];
    }
}

// 結果をフォーマットする
// (例) 83.4561 --> "1:23.456"
function format_result($val) {
    $minute = floor((double)$val / 60.0);
    $second = floor((double)$val - $minute * 60.0);
    $milli = floor(((double)$val - $minute * 60.0 - $second) * 1000.0);

    $ret = '';
    if (0 < $minute) {
        $ret .= (string)$minute . ':';
    }
    if (0 <= $second && $second < 10) {
        $ret .= '0' . (string)$second;
    } else {
        $ret .= (string)$second;
    }
    $ret .= '.' . (string)$milli;

    return $ret;
}

// アベレージを計算する
function calc_result($records) {
    $record_max = -1.0;
    $record_min = 10000.0;
    $total = 0.0;
    foreach ($records as $record) {
        if ($record_max < $record) {
            $record_max = $record;
        }
        if ($record < $record_min) {
            $record_min = $record;
        }
        $total = $total + $record;
    }
    $val = ($total - $record_max - $record_min) / 3.0;

    return $val;
}


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

// トップページ
if (empty($query) || $query === '') {
    $is_index = true;
    $title = '戸川研コンテスト';
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
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
</head>
<body>
  <div class="container" style="padding-right: 5px; padding-left: 5px;">

    <h1><?php echo $title; ?></h1>

<?php if ($is_index) { ?>

    <ul>
        <li><a href="<?php echo ABSOLUTE_URL ?>/1">第1回 (2016-07-01)</a></li>
    </ul>

<?php } else { ?>

    <p>
      開催日: <?php echo $obj['date']; ?>
    </p>

    <hr>

    <div id="table-results-container" class="table-container">
      <h2><?php echo $obj['event_name']; ?></h2>
      <table id="table-results" class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th class="col-result">Result</th>
            <th class="col-record col-desktop">1st</th>
            <th class="col-record col-desktop">2nd</th>
            <th class="col-record col-desktop">3rd</th>
            <th class="col-record col-desktop">4th</th>
            <th class="col-record col-desktop">5th</th>
            <th class="col-records col-mobile">Records</th>
            <th>Puzzle</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($obj['results'] as $index => $result) { ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo $result['name']; ?></td>
              <td data-order="<?php echo $result['result_val']; ?>"><b><?php echo $result['result']; ?></b></td>
              <?php foreach ($result['records'] as $record) { ?>
                <td class="col-record col-desktop">
                  <?php echo $record; ?>
                </td>
              <?php } ?>
              <td class="col-records col-mobile">
                <?php foreach ($result['records'] as $record) { ?>
                  <?php echo $record; ?>
                <?php } ?>
              <td>
                <a href="https://store.tribox.com/products/detail.php?product_id=<?php echo $result['puzzle']['id']; ?>" target="_blank">
                  <?php echo $result['puzzle']['name']; ?>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div><!-- /#table-results-container -->

    <div id="table-scrambles-container" class="table-container">
      <h3>スクランブル <?php echo $obj['scramble_info']; ?></h3>
      <table id="table-scrambles" class="table table-sm">
        <tbody>
          <?php foreach ($obj['scrambles'] as $index => $scramble) { ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo $scramble; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div><!-- /#table-scrambles-container -->

<?php }?>

    <!--<p style="margin-top: 20px;">
      Result data is courtesy of the <a href="https://www.worldcubeassociation.org/" target="_blank">World Cube Association</a>.
      The latest results can be found via the <a href="https://www.worldcubeassociation.org/results/" target="_blank">WCA Results Pages</a>.
      Data was last updated on {{ attrs.date_fetched }}.
    </p>-->

    <!--<footer class="footer">
      <p>Generated using <a href="https://github.com/kotarot/psych-gen" target="_blank">Psych sheet generator</a>
         by <a href="https://www.worldcubeassociation.org/results/p.php?i=2010TERA01" target="_blank">Kotaro Terada</a>.</p>
    </footer>-->

  </div><!-- /.container -->

<style>
body {
    font-family: 'ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック',"Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 18px;
}

@media (max-width: 768px) {
    h1 { font-size: 28px; }
    h2 { font-size: 20px; }
}
h3 { font-size: 20px; }

.table-container {
    margin-bottom: 50px;
}

table {
    border-top: 2px solid #DDD !important;
}
table, thead th {
    border-bottom: 2px solid #DDD !important;
}
th, td {
    padding: 8px !important;
}
@media (max-width: 768px) {
    .col-desktop { display: none !important; }
    .col-mobile  { display: table-cell !important; }
    th, td { letter-spacing: -1px; }
}
@media (min-width: 769px) {
    .col-desktop { display: table-cell !important; }
    .col-mobile  { display: none !important; }
}

footer {
    margin-top: 30px;
    padding-top: 20px;
    color: #777;
    border-top: 1px solid #E5E5E5;
}
</style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#table-results').DataTable({
        'bPaginate': false, 'order': [[2, 'asc']],
        'columnDefs': [{'orderable': false, 'targets': [0, 8, 9]}]
    });
});
</script>

</body>
</html>
