<?php
/**
 * functions.php
 */

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
