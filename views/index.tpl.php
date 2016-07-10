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

  <div class="container" ng-controller="IndexCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <div class="row">
      <div class="col-md-6" ng-repeat="(tag, group) in groups">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">{{ group.name }}</h2>
          </div><!-- /.panel-heading -->
          <div class="panel-body">
            <ul>
              <li ng-repeat="contest in contests[group.gid] | orderBy: '-createdAt'">
                <a href="/{{ tag }}/{{ contest.cid | removeHead }}">{{ contest.name }} ({{ contest.date }})</a>
              </li>
            </ul>
            <a href="/{{ tag }}" class="btn btn-default btn-primary">{{ group.name }}の全コンテスト一覧</a>
          </div><!-- /.panel-body -->
        </div><!-- /.panel -->
      </div>
      <!--<div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title"></h2>
          </div>
          <div class="panel-body">
            
          </div>
        </div>
      </div>-->
    </div><!-- /.row -->

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<style>
h2.panel-title {
    font-size: 24px;
}
</style>

<script>
app.controller('IndexCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.contests = null;
    $scope.pageLoaded = false;

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
            $scope.pageLoaded = true;
        });
    });
    });
}]);
</script>

</body>
</html>
