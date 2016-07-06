<?php
/**
 * templates/index.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php
include dirname(__FILE__) . '/head.tpl.php';
?>
<body>
  <div class="container" style="padding-right: 5px; padding-left: 5px;" ng-controller="IndexCtrl">

    <h1><?php echo $title; ?></h1>

    <pre>{{ groups | json }}</pre>

    <ul>
        <li><a href="<?php echo ABSOLUTE_URL ?>/1">第1回 (2016-07-01)</a></li>
    </ul>

<?php
include dirname(__FILE__) . '/footer.tpl.php';
?>

  </div><!-- /.container -->

<script>
app.controller('IndexCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.groups = null;
    ref.child('groups').once('value', function(snap) {
        $timeout(function() {
            $scope.groups = snap.val();
        });
    });
}]);
</script>

</body>
</html>
