<?php
/**
 * views/contest.tpl.php
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
      <li class="active">{{ contest.name }}</li>
    </ol>

    <h2 class="inline-block">{{ contest.name }} <small>{{ group.name }}</small></h2>
    <a href="/<?php echo $tag; ?>/<?php echo $cid; ?>/edit" class="btn btn-default btn-cog" role="button">
        <i class="fa fa-cog"></i> 編集
    </a>

    <p>
      <i class="fa fa-calendar"></i> {{ contest.date }}
    </p>

    <div id="table-results-container" class="table-container">
      <table id="table-results" class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th class="col-result">Average</th>
            <th class="col-detail col-desktop">1st</th>
            <th class="col-detail col-desktop">2nd</th>
            <th class="col-detail col-desktop">3rd</th>
            <th class="col-detail col-desktop">4th</th>
            <th class="col-detail col-desktop">5th</th>
            <th class="col-details col-mobile">Details</th>
            <th>Puzzle</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="record in result.records | orderBy: 'priority'">
            <td>{{ $index + 1 }}</td>
            <td>{{ record.name }}</td>
            <td data-order="{{ record.average.average }}"><b>{{ record.averageF }}</b></td>
            <td data-order="{{ detail }}" class="col-detail col-desktop" ng-repeat="detail in record.details">
              <span class="text-success" ng-show="record.average.best == $index">
                ({{ detail | formatTime }})
              </span>
              <span class="text-danger" ng-show="record.average.worst == $index">
                ({{ detail | formatTime }})
              </span>
              <span ng-show="record.average.best != $index && record.average.worst != $index">
                {{ detail | formatTime }}
              </span>
            </td>
            <td class="col-details col-mobile">
              <span ng-repeat="detail in record.details">
                <span class="text-success" ng-show="record.average.best == $index">
                  ({{ detail | formatTime }})
                </span>
                <span class="text-danger" ng-show="record.average.worst == $index">
                  ({{ detail | formatTime }})
                </span>
                <span ng-show="record.average.best != $index && record.average.worst != $index">
                  {{ detail | formatTime }}
                </span>
              </span>
            <td>
              <a href="https://store.tribox.com/products/detail.php?product_id={{ record.puzzle.id }}" target="_blank" ng-show="record.puzzle.id">
                {{ record.puzzle.name }}
              </a>
              <span ng-show="!(record.puzzle.id)">
                {{ record.puzzle.name }}
              </span>
              </td>
          </tr>
        </tbody>
      </table>
    </div><!-- /#table-results-container -->

    <div id="table-scrambles-container" class="table-container">
      <h3>スクランブル</h3>
      <table id="table-scrambles" class="table table-sm">
        <tbody>
          <tr ng-repeat="i in range(5)">
            <td>{{ $index + 1 }}</td>
            <td>{{ result.scrambles[$index] }}</td>
          </tr>
        </tbody>
      </table>
    </div><!-- /#table-scrambles-container -->

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<script>
app.controller('ContestCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.group = null;
    $scope.contest = null;
    $scope.result = null;
    $scope.pageLoaded = false;

    var tag = '<?php echo $tag; ?>'
    var cid = '-<?php echo $cid; ?>';
    ref.child('groups').once('value', function(snapGroups) {
        var Groups = snapGroups.val();
        var gid = Groups[tag].gid;
    ref.child('contests').child(gid).child(cid).once('value', function(snapContest) {
        var Contest = snapContest.val();
    ref.child('results').child(gid).child(cid).once('value', function(snapResult) {
        var ResultObj = snapResult.val();
        var Result = { 'records': [], 'scrambles': [] };

        if (ResultObj) {
            Result.scrambles = ResultObj.scrambles;
            Object.keys(ResultObj.records).forEach(function(uid) {
                var average = calcAverage5(ResultObj.records[uid].details);
                ResultObj.records[uid].average = average;
                ResultObj.records[uid].averageF = formatTime(average.average);
                // 整数部をaverageの値(6桁)、小数部をbestの値(6桁)で表現して数値比較でランキングできるようにする
                ResultObj.records[uid].priority = (
                    ('000000' + String(formatTimeAlt(average.average)).replace('.', '')).slice(-6) + '.' +
                    ('000000' + String(formatTimeAlt(ResultObj.records[uid].details[average.best])).replace('.', '')).slice(-6) ) - 0;
                Result.records.push(ResultObj.records[uid]);
            });
        }

        $timeout(function() {
            $scope.groups = Groups;
            $scope.group = Groups[tag];
            $scope.contest = Contest;
            $scope.result = Result;
            $scope.pageLoaded = true;
            $timeout(function() {
                window.setupTable();
            }, 100);
        });
    });
    });
    });

    $scope.range = function(n) {
        var arr = [];
        for (var i = 0; i < n; i++)
            arr.push(i);
        return arr;
    };
}]);

var setupTable = function() {
    $('#table-results').DataTable({
        'bPaginate': false, 'order': [[2, 'asc']],
        'columnDefs': [{'orderable': false, 'targets': [0, 8]}]
    });
};
</script>

</body>
</html>
