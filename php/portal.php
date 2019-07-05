<?php

include('connection.php');

$action= $_POST['action'];

	if($action == 'session')
	{
		
		 session_start();

		 if (isset($_SESSION['dutauser'])) {
			
			$json_response = array();
			$row_array['session'] = $_SESSION['dutauser'];
			$row_array['session1'] = $_SESSION['dutaid'];
			$row_array['session2'] = $_SESSION['account_status'];
			array_push($json_response,$row_array);
			echo json_encode($json_response);
		 } else {
			 $json_response = array();
		   $row_array['session'] = "index";
		   array_push($json_response,$row_array);
					echo json_encode($json_response);
		 }
		
		
	}
	else if($action == 'logout')
	{
		session_start();
		session_destroy();
		echo"";
	}
?>