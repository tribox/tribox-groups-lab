<?php
/**
 * views/contest.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php
include dirname(__FILE__) . '/head.tpl.php';
?>
<body>
  <div class="container" ng-controller="ContestCtrl">

    <h1>{{ group.name }} {{ contest.name }}</h1>
 
    <p>
      <i class="fa fa-calendar"></i> {{ contest.date }}
    </p>

    <div id="table-results-container" class="table-container">
      <table id="table-results" class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th class="col-result">Result</th>
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
              {{ detail | formatTime }}
            </td>
            <td class="col-details col-mobile">
              <span ng-repeat="detail in record.details">
                {{ detail | formatTime }}
              </span>
            <td>
              <a href="https://store.tribox.com/products/detail.php?product_id=<?php echo $result['puzzle']['id']; ?>" target="_blank" ng-show="record.puzzle.id">
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
          <tr ng-repeat="scramble in result.scrambles">
            <td>{{ $index + 1 }}</td>
            <td>{{ scramble }}</td>
          </tr>
        </tbody>
      </table>
    </div><!-- /#table-scrambles-container -->

<?php
include dirname(__FILE__) . '/footer.tpl.php';
?>

  </div><!-- /.container -->

<script>
app.controller('ContestCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.group = null;
    $scope.contest = null;
    $scope.result = null;

    var tag = '<?php echo $tag; ?>'
    var cid = 'c<?php echo $cid; ?>';
    ref.child('groups').once('value', function(snapGroups) {
        var Groups = snapGroups.val();
        var gid = Groups[tag].gid;
    ref.child('contests').child(gid).child(cid).once('value', function(snapContest) {
        var Contest = snapContest.val();
    ref.child('results').child(gid).child(cid).once('value', function(snapResult) {
        var Result = snapResult.val();
        var ResultArr = { 'records': [], 'scrambles': Result.scrambles };

        Object.keys(Result.records).forEach(function(uid) {
            var average = calcAverage5(Result.records[uid].details);
            Result.records[uid].average = average;
            Result.records[uid].averageF = formatTime(average.average);
            // 整数部をaverageの値(6桁)、小数部をbestの値(6桁)で表現して数値比較でランキングできるようにする
            Result.records[uid].priority = ( ('000000' + String(average.average).replace('.', '')).slice(-6) + '.' +
                                             ('000000' + String(Result.records[uid].details[average.best]).replace('.', '')).slice(-6) ) - 0;
            ResultArr.records.push(Result.records[uid]);
        });

        $timeout(function() {
            $scope.groups = Groups;
            $scope.group = Groups[tag];
            $scope.contest = Contest;
            $scope.result = ResultArr;
            $timeout(function() {
                window.setupTable();
            }, 100);
        });
    });
    });
    });
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
