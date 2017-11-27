<?php 
require_once("Config/Autoload.php");
date_default_timezone_set('UTC');
//echo "<pre>";
//echo $_POST['edit'];
//var_dump($_POST);


if(isset($_GET['APItoDB'])){
	$worker_from_API = new worker();
	$worker_from_API->APItoDB(json_decode(file_get_contents("https://staging.tempbuddy.com/public/api/jobs"),true));
}


if(isset($_GET['delete_id'])){
	$event_to_delete = new time_event();
	$event_to_delete->deleteEvent($_GET['delete_id']);
}



include("Views/Menu.php");

include("Views/list_workers.php");

if(isset($_GET['userID']))
	include("Views/times_table.php");


?>