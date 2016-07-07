<?php
/**
 * views/unauth.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="UnauthCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <pre>{{ authData | json }}</pre>

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<script>
app.controller('UnauthCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.authData = null;
    $scope.pageLoaded = true;

    ref.unauth();
    ref.onAuth(function(authData) {
        $timeout(function() {
            $scope.authData = authData;
        });
    });
}]);
</script>

</body>
</html>
