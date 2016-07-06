/**
 * contest.js
 */

// タイムを分・秒・小数点以下3桁にフォーマットする
var formatTime = function(input) {
    var post = '.000';
    var second = input - 0;
    if (String(input).indexOf('.') != -1) {
        second = String(input).split('.')[0] - 0;
        // 小数点以下が .31299999... みたいなときは .313 にしたいので4桁切り出して1桁目を四捨五入する
        var decimal = (String(input).split('.')[1] + '0000').substr(0, 4) - 0;
        post = '.' + ('000' + String(Math.round(decimal / 10))).slice(-3);
    }
    if (second < 10) {
        return (second + post).substr(0, 5);
    } else if (second < 60) {
        return (second + post).substr(0, 6);
    } else {
        var minute = Math.floor(Math.floor(second) / 60);
        var s = ('0' + (second - minute * 60)).slice(-2);
        if (minute < 10) {
            return (minute + ':' + s + post).substr(0, 8);
        } else {
            return (minute + ':' + s + post).substr(0, 9);
        }
    }
};

// Average of 5 を計算
var calcAverage5 = function(details) {
    var sum = 0.000;
    var count = 0, countDNF = 0;
    var lowerIndex = -1, upperIndex = -1;
    var lower = 9999.999, upper = 0.000;
    details.forEach(function(d, index) {
        if (9999 < d) {
            countDNF++;
            upperIndex = index;
            if (lowerIndex == -1) {
                lowerIndex = index;
            }
        } else {
            sum += d;
            count++;
            if (d < lower) {
                lower = d;
                lowerIndex = index;
            }
            if (upper < d) {
                upper = d;
                upperIndex = index;
            }
        }
    });
    if (countDNF == 0) {
        return {'average': (Math.round(((sum - lower - upper) / (count - 2)) * 1000)) / 1000,
                'best': lowerIndex, 'worst': upperIndex};
    } else if (countDNF == 1) {
        return {'average': (Math.round(((sum - lower) / (count - 1)) * 1000)) / 1000,
                'best': lowerIndex, 'worst': upperIndex};
    } else {
        return {'average': 9999.999, 'best': lowerIndex, 'worst': upperIndex};
    }
};
