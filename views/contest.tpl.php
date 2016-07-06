<?php
/**
 * templates/contest.tpl.php
 */
?>

<!DOCTYPE html>
<html lang="ja" ng-app="app">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>

  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="/assets/main.css">

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
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
  <div class="container" style="padding-right: 5px; padding-left: 5px;">

    <h1><?php echo $title; ?></h1>
 
    <p>
      開催日: <?php echo $obj['date']; ?>
    </p>

    <hr>

    <div id="table-results-container" class="table-container">
      <h2><?php echo $obj['event_name']; ?></h2>
      <table id="table-results" class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Name</th>
            <th class="col-result">Result</th>
            <th class="col-record col-desktop">1st</th>
            <th class="col-record col-desktop">2nd</th>
            <th class="col-record col-desktop">3rd</th>
            <th class="col-record col-desktop">4th</th>
            <th class="col-record col-desktop">5th</th>
            <th class="col-records col-mobile">Records</th>
            <th>Puzzle</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($obj['results'] as $index => $result) { ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo $result['name']; ?></td>
              <td data-order="<?php echo $result['result_val']; ?>"><b><?php echo $result['result']; ?></b></td>
              <?php foreach ($result['records'] as $record) { ?>
                <td class="col-record col-desktop">
                  <?php echo $record; ?>
                </td>
              <?php } ?>
              <td class="col-records col-mobile">
                <?php foreach ($result['records'] as $record) { ?>
                  <?php echo $record; ?>
                <?php } ?>
              <td>
                <a href="https://store.tribox.com/products/detail.php?product_id=<?php echo $result['puzzle']['id']; ?>" target="_blank">
                  <?php echo $result['puzzle']['name']; ?>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div><!-- /#table-results-container -->

    <div id="table-scrambles-container" class="table-container">
      <h3>スクランブル <?php echo $obj['scramble_info']; ?></h3>
      <table id="table-scrambles" class="table table-sm">
        <tbody>
          <?php foreach ($obj['scrambles'] as $index => $scramble) { ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo $scramble; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div><!-- /#table-scrambles-container -->

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

$(document).ready(function() {
    $('#table-results').DataTable({
        'bPaginate': false, 'order': [[2, 'asc']],
        'columnDefs': [{'orderable': false, 'targets': [0, 8, 9]}]
    });
});
</script>

</body>
</html>
