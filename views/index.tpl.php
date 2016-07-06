<?php
/**
 * views/index.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php
include dirname(__FILE__) . '/head.tpl.php';
?>
<body>
  <div class="container" ng-controller="IndexCtrl">

    <h1><i class="fa fa-flask"></i> <?php echo $title; ?></h1>

    <div class="row">
      <div class="md-12" ng-repeat="(tag, group) in groups">
        <h2>{{ group.name }}</h2>
        <ul>
          <li ng-repeat="(cid, contest) in contests[group.gid]">
            <a href="/{{ tag }}/{{ cid | removeHead }}">{{ contest.name }} ({{ contest.date }})</a>
          </li>
        </ul>
      </div>
    </div><!-- /.row -->

<?php
include dirname(__FILE__) . '/footer.tpl.php';
?>

  </div><!-- /.container -->

<script>
app.controller('IndexCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    $scope.contests = null;

    ref.child('groups').once('value', function(snapGroups) {
        var Groups = snapGroups.val();
    ref.child('contests').once('value', function(snapContests) {
        var Contests = snapContests.val();

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
