<?php
/**
 * views/index.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="IndexCtrl">

    <div class="row">
      <div class="col-md-6" ng-repeat="(tag, group) in groups">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">{{ group.name }}</h2>
          </div><!-- /.panel-heading -->
          <div class="panel-body">
            <ul>
              <li ng-repeat="contest in contests[group.gid] | orderBy: '-cid'">
                <a href="/{{ tag }}/{{ contest.cid | removeHead }}">{{ contest.name }} ({{ contest.date }})</a>
              </li>
            </ul>
          </div><!-- /.panel-body -->
        </div><!-- /.panel -->
      </div>
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">研究室に限らず</h2>
          </div><!-- /.panel-heading -->
          <div class="panel-body">
            お試しユーザ募集中。
          </div><!-- /.panel-body -->
        </div><!-- /.panel -->
      </div>
    </div><!-- /.row -->

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div><!-- /.container -->

<style>
h2.panel-title {
    font-size: 24px;
}
</style>

<script>
app.controller('IndexCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.contests = null;

    ref.child('groups').once('value', function(snapGroups) {
        var Groups = snapGroups.val();
    ref.child('contests').once('value', function(snapContests) {
        var ContestsObj = snapContests.val();
        var Contests = {};

        // 配列化
        Object.keys(ContestsObj).forEach(function(gid) {
            Contests[gid] = [];
            Object.keys(ContestsObj[gid]).forEach(function(cid) {
                var obj = ContestsObj[gid][cid];
                obj.cid = cid;
                Contests[gid].push(obj);
            });
        });

        $timeout(function() {
            $scope.groups = Groups;
            $scope.contests = Contests;
        });
    });
    });
}]);
</script>

</body>
</html>
