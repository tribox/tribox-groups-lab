<?php
/**
 * templates/head.tpl.php
 */
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>

  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <?php if ($contest_page) { ?>
  <link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
  <?php } ?>
  <link rel="stylesheet" href="/assets/main.css">

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <?php if ($contest_page) { ?>
  <script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <?php } ?>
  <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
  <script src="http://cdn.firebase.com/js/client/2.2.4/firebase.js"></script>
  <script src="http://cdn.firebase.com/libs/angularfire/1.2.0/angularfire.min.js"></script>

<script>
/**
 * FIREBASE
 */
var app = angular.module('<?php echo NG_APP; ?>', ['firebase']);
var ref = new Firebase('https://<?php echo FIREBASE_APP; ?>.firebaseio.com');
</script>

</head>
