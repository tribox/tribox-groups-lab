<?php
/**
 * views/group.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="GroupCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <ol class="breadcrumb">
      <li><a href="/"><?php echo MAIN_TITLE; ?></a></li>
      <li class="active">{{ group.name }}</li>
    </ol>

    <h2 class="inline-block">{{ group.name }} のコンテスト一覧</h2>
    <a href="/<?php echo $tag; ?>/edit" class="btn btn-default btn-cog" role="button">
        <i class="fa fa-cog"></i> 編集
    </a>

    <ul>
      <li ng-repeat="contest in contests | orderBy: '-cid'">
        <a href="/<?php echo $tag; ?>/{{ contest.cid | removeHead }}">{{ contest.name }} ({{ contest.date }})</a>
      </li>
    </ul>

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<script>
app.controller('GroupCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.group = null;
    $scope.contests = null;
    $scope.pageLoaded = false;

    ref.child('groups').child('<?php echo $tag; ?>').once('value', function(snapGroup) {
        var Group = snapGroup.val();
        var gid = Group.gid;
    ref.child('contests').child(gid).once('value', function(snapContests) {
        var ContestsObj = snapContests.val();
        var Contests = [];

        Object.keys(ContestsObj).forEach(function(cid) {
            var obj = ContestsObj[cid];
            obj.cid = cid;
            Contests.push(obj);
        });

        $timeout(function() {
            $scope.group = Group;
            $scope.contests = Contests;
            $scope.pageLoaded = true;
        });
    });
    });
}]);
</script>

</body>
</html>
