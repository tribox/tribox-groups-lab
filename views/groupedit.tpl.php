<?php
/**
 * views/groupedit.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="GroupEditCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <ol class="breadcrumb">
      <li><a href="/"><?php echo MAIN_TITLE; ?></a></li>
      <li><a href="/<?php echo $tag; ?>">{{ group.name }}</a></li>
      <li class="active">編集中</li>
    </ol>

    <h2 class="inline-block">{{ group.name }} のコンテスト一覧</h2>
    <span class="text-danger btn-cog">
        編集中
    </span>

    <table class="table">
      <thead>
        <tr>
          <th>コンテスト名</th>
          <th>開催日</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="(cid, contest) in contests">
          <td><input class="form-control" type="text" placeholder="コンテスト名" ng-model="contests[cid].name" ng-blur="save(cid)"></td>
          <td><input class="form-control" type="text" placeholder="<?php echo TODAYSTR; ?>" ng-model="contests[cid].date" ng-blur="save(cid)"></td>
          <td>
            <a href="/<?php echo $tag; ?>/{{ cid | removeHead }}/edit" class="btn btn-default">コンテスト結果を編集</a>
            <button class="btn btn-defautl btn-danger" ng-click="remove(cid)">削除</button>
          </td>
        </tr>
      </tbody>
    </table>
    <button class="btn btn-default" ng-click="append()">新しいコンテストを追加</button>

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<script>
app.controller('GroupEditCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.group = null;
    $scope.contests = null;
    $scope.pageLoaded = false;

    var authData = ref.getAuth();
    var gid;
    if (!authData) {
        location.href = '/<?php echo $tag; ?>/auth?next=/<?php echo $tag; ?>/edit';
    } else {
        ref.child('groups').child('<?php echo $tag; ?>').once('value', function(snapGroup) {
            var Group = snapGroup.val();
            gid = Group.gid;
            if (authData.uid != gid) {
                location.href = '/<?php echo $tag; ?>/auth?next=/<?php echo $tag; ?>/edit';
            }
        ref.child('contests').child(gid).on('value', function(snapContests) {
            var Contests = snapContests.val();

            $timeout(function() {
                $scope.group = Group;
                $scope.contests = Contests;
                $scope.pageLoaded = true;
            });
        });
        });
    }

    // 新しいコンテスト追加
    $scope.append = function() {
        ref.child('contests').child(gid).push({
            'name': '新しいコンテスト',
            'date': '<?php echo TODAYSTR; ?>',
            'createdAt': Firebase.ServerValue.TIMESTAMP
        });
    };

    // コンテストを保存
    $scope.save = function(cid) {
        ref.child('contests').child(gid).child(cid).update({
            'name': $scope.contests[cid].name,
            'date': $scope.contests[cid].date
        });
    };

    // コンテストを削除
    $scope.remove = function(cid) {
        if (window.confirm('削除します')) {
            ref.child('contests').child(gid).child(cid).set(null);
            ref.child('results').child(gid).child(cid).set(null);
        }
    };
}]);
</script>

</body>
</html>
