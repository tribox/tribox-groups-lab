<?php
/**
 * templates/index.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="app">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>

  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/main.css">

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
  <script src="http://cdn.firebase.com/js/client/2.2.4/firebase.js"></script>
  <script src="http://cdn.firebase.com/libs/angularfire/1.2.0/angularfire.min.js"></script>

<script>
/**
 * FIREBASE
 */
var app = angular.module('app', ['firebase']);
var ref = new Firebase('https://tribox-groups-lab.firebaseio.com');
</script>

</head>
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
