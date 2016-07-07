<?php
/**
 * views/auth.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="<?php echo NG_APP; ?>">
<?php include dirname(__FILE__) . '/head.tpl.php'; ?>
<body>
  <?php include dirname(__FILE__) . '/header.tpl.php'; ?>

  <div class="container" ng-controller="AuthCtrl"><div class="loading-area" ng-hide="pageLoaded">
    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
    <span class="sr-only">Loading...</span>
    Loading...
  </div><div ng-show="pageLoaded">

    <form name="authForm" ng-submit="login()">
      <div class="form-group">
        <label for="inputPassword">{{ group.name }} のパスワードは？</label>
        <input type="password" class="form-control" id="inputPassword" placeholder="Password" ng-model="password">
      </div>
      <p class="text-danger" ng-show="error">{{ error }}</p>
      <button type="submit" class="btn btn-default btn-primary">認証</button>
    </form>

  <?php include dirname(__FILE__) . '/footer.tpl.php'; ?>

  </div></div><!-- /.container -->

<script>
app.controller('AuthCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.pageLoaded = false;
    $scope.group = null;

    ref.child('groups').child('<?php echo $tag; ?>').once('value', function(snapGroup) {
        var Group = snapGroup.val();

        $timeout(function() {
            $scope.group = Group;
            $scope.pageLoaded = true;
        });
    });

    $scope.error = null;
    $scope.login = function() {
        if ($scope.password == null) {
            $scope.error = 'パスワードを入力してください。';
        } else {
            ref.authWithPassword({
                email: '<?php echo sprintf(FIREBASE_USER, $tag); ?>',
                password: $scope.password
            }, function(error, authData) {
                if (error) {
                    console.error(error);
                    $scope.error = error.code;
                } else {
                    console.log('Logged in as: ' + authData.uid);
                    <?php if ($next_url) { ?>
                        location.href = '<?php echo $next_url; ?>';
                    <?php } ?>
                }
            });
        }
    };
}]);
</script>

</body>
</html>
