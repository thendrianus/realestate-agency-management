<?php
include('connection.php');
			global $conn;
			$id = $_POST['id'];
			$status = $_POST['id'];
				
	
					$json_response = array();
					
						
						$row_array['id'] = $id;
						$row_array['status'] = $status;
						array_push($json_response,$row_array);
					
			$sql = "INSERT INTO test (apaaja)VALUES('".json_encode($json_response)."')";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error ."".json_encode($json_response)."".$sql;
				}
				
				
			
?>