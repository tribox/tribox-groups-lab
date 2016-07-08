<?php
/**
 * views/contestedit.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="ContestCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <ol class="breadcrumb">
      <li><a href="/"><?php echo MAIN_TITLE; ?></a></li>
      <li><a href="/<?php echo $tag; ?>">{{ group.name }}</a></li>
      <li><a href="/<?php echo $tag; ?>/<?php echo $cid; ?>">{{ contest.name }}</a></li>
      <li class="active">編集中</li>
    </ol>

    <h2 class="inline-block">{{ contest.name }} <small>{{ group.name }}</small></h2>
    <span class="text-danger btn-cog">
        編集中
    </span>

    <p>
      <i class="fa fa-calendar"></i> {{ contest.date }}
    </p>

    <hr>

    <p>
        記録を入力してください。DNFは <i>DNF</i> と入力。Averageと順位は後で勝手に計算されます。<br>
        パズルIDを入力するとtriboxストアの商品ページへのリンクが表示されるので、できれば入力してください。
    </p>

    <div id="table-results-container" class="table-container">
      <table id="table-results" class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>1st</th>
            <th>2nd</th>
            <th>3rd</th>
            <th>4th</th>
            <th>5th</th>
            <th>Puzzle</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="(uid, record) in result.records">
            <td><input class="form-control" type="text" placeholder="名前" ng-model="result.records[uid].name" ng-blur="saveRecord(uid)"></td>
            <td ng-repeat="i in range(5)">
              <input id="{{ uid }}{{ $index }}" class="form-control" type="text" placeholder="-:--.---" ng-model="result.records[uid].detailsF[$index]" ng-blur="saveDetail(uid, $index)">
            </td>
            <td><input class="form-control" type="text" placeholder="パズル名" ng-model="result.records[uid].puzzle.name" ng-blur="saveRecord(uid)"></td>
            <td><input class="form-control" type="text" placeholder="パズルID" ng-model="result.records[uid].puzzle.id" ng-blur="saveRecord(uid)"></td>
            <td>
              <button class="btn btn-defautl btn-danger" ng-click="removeRecord(uid)">削除</button>
            </td>
          </tr>
        </tbody>
      </table>
      <button class="btn btn-default" ng-click="append()">参加者を追加</button>
    </div>

    <p>
        スクランブルを入力してください。
        スクランブル生成機能は今のところ無いです。
    </p>

    <div id="table-scrambles-container" class="table-container">
      <table id="table-scrambles" class="table table-sm">
        <tbody>
          <tr ng-repeat="i in range(5)">
            <th>{{ $index + 1}}</th>
            <td><input class="form-control" type="text" ng-model="result.scrambles[$index]" ng-blur="saveScrambles()"></td>
          </tr>
        </tbody>
      </table>
    </div>

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<style>
input {
    color: #333;
    font-size: 16px;
}
table#table-results input {
    width: 100%;
}
table#table-scrambles input {
    width: 100%;
}
.has-error input.form-control {
    border-width: 2px;
}
</style>

<script>
app.controller('ContestCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.group = null;
    $scope.contest = null;
    $scope.result = null;
    $scope.pageLoaded = false;

    var tag = '<?php echo $tag; ?>'
    var cid = '-<?php echo $cid; ?>';

    var authData = ref.getAuth();
    var gid;
    if (!authData) {
        location.href = '/<?php echo $tag; ?>/auth?next=/<?php echo $tag; ?>/<?php echo $cid; ?>/edit';
    } else {
        ref.child('groups').once('value', function(snapGroups) {
            var Groups = snapGroups.val();
            gid = Groups[tag].gid;
            if (authData.uid != gid) {
                location.href = '/<?php echo $tag; ?>/auth?next=/<?php echo $tag; ?>/<?php echo $cid; ?>/edit';
            }
        ref.child('contests').child(gid).child(cid).once('value', function(snapContest) {
            var Contest = snapContest.val();
        ref.child('results').child(gid).child(cid).on('value', function(snapResult) {
            var Result = snapResult.val();

            // 各記録のフォーマット
            if (Result) {
                if (Result.records) {
                    Object.keys(Result.records).forEach(function(uid) {
                        Result.records[uid].detailsF = [];
                        if (Result.records[uid].details) {
                            Result.records[uid].details.forEach(function(time) {
                                Result.records[uid].detailsF.push(formatTime(time));
                            });
                        }
                    });
                }
            } else {
                Result = {};
                Result.records = {};
                Result.scrambles = ['', '', '', '', ''];
            }

            $timeout(function() {
                $scope.groups = Groups;
                $scope.group = Groups[tag];
                $scope.contest = Contest;
                $scope.result = Result;
                $scope.pageLoaded = true;
            });
        });
        });
        });
    }

    // 参加者を追加
    $scope.append = function() {
        ref.child('results').child(gid).child(cid).child('records').push({
            'name': '',
            'details': [0, 0, 0, 0, 0],
            'puzzle': {
                'id': 1472,
                'name': 'YJ GuanLong'
            }
        });
    };

    // 結果を保存
    $scope.saveRecord = function(uid) {
        var data = {
            'name': $scope.result.records[uid].name,
            'details': $scope.result.records[uid].details,
            'puzzle': $scope.result.records[uid].puzzle
        }
        // 保存形式に合わせて変換
        // data.puzzle.id は文字列型なので変換
        if (!(data.puzzle.id)) {
            data.puzzle.id = null;
        } else {
            // 数値化
            data.puzzle.id = data.puzzle.id - 0;
        }
        ref.child('results').child(gid).child(cid).child('records').child(uid).update(data);
    };
    $scope.saveDetail = function(uid, index) {
        var resultForm = $scope.result.records[uid].detailsF[index];
        var inputElem = angular.element(document.getElementById(uid + index));

        // Check the format
        if (resultForm == 'DNF') {
            inputElem.parent().removeClass('has-error');
            $scope.result.records[uid].details[index] = 999.999;
            $scope.saveRecord(uid);
        } else if (!(resultForm.match(/^([0-9]:)?[0-9]{1,3}(\.[0-9]{0,3})?$/))) {
            inputElem.parent().addClass('has-error');
        } else {
            // Convert to seconds
            if (resultForm.indexOf(':') != -1) {
                var t = resultForm.split(':');
                $scope.result.records[uid].details[index] = Number(t[0]) * 60 + Number(t[1]);
            } else {
                $scope.result.records[uid].details[index] = Number(resultForm);
            }

            // Check range
            if ($scope.result.records[uid].details[index] <= 0 || 1000 <= $scope.result.records[uid].details[index]) {
                inputElem.parent().addClass('has-error');
            } else {
                inputElem.parent().removeClass('has-error');
                $scope.saveRecord(uid);
            }
        }
    }

    // 結果を削除
    $scope.removeRecord = function(uid) {
        if (window.confirm('削除します')) {
            ref.child('results').child(gid).child(cid).child('records').child(uid).set(null);
        }
    };

    // スクランブルを保存
    $scope.saveScrambles = function() {
        ref.child('results').child(gid).child(cid).update({
            'scrambles': $scope.result.scrambles
        });
    };

    $scope.range = function(n) {
        var arr = [];
        for (var i = 0; i < n; i++)
            arr.push(i);
        return arr;
    };
}]);
</script>

</body>
</html>
