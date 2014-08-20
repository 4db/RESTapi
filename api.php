<?php
include_once('classes/db.php');
include_once('controllers/RouteController.php');
$route = new RouteController;
$route->map();