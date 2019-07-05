<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
	else if($action == "add"){
			
			$id = $_POST['id'];
			$name = $_POST['name'];
	
			$sql = "INSERT INTO location ( location_id, location_name , create_datetime, lastupdate, isactive)VALUES('$id', '$name', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				global $conn;
				$viewdatavar = viewdata('add');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo "error";
				}
		}
	else if ($action == "editdata"){
			$id = $_POST['id'];
			global $conn;
			
		$q2 = $conn->query("select location_id,location_name from location where isactive=1 && location_id ='$id' limit 1");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['location_id'] = $sumcol[0];
					$row_array['location'] = $sumcol[1];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	
	else if ($action == "edit") {
			$id = $_POST['id'];
			$name = $_POST['name'];		
	 $sql = "UPDATE location SET location_name='$name', lastupdate=now() WHERE location_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				global $conn;
				$viewdatavar = viewdata('edit');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	else if ($action == "delete"){
			$id = $_POST['id'];
			
			$sql = "UPDATE location SET lastupdate=now(),isactive=0 WHERE location_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				global $conn;
				$viewdatavar = viewdata('delete');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	
function viewdata($data){
				global $conn;
				
				$q = $conn->query("select location_id,location_name as 'name' from location where isactive=1");
				$q2 = $conn->query("select COUNT(*) from location where MONTH(create_datetime)=".date('n')." && YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['location_id'] = $row['location_id'];
						$row_array['name'] = $row['name'];
						
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['location_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['name'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['name'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['name'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['name'] = $data;
							}
						array_push($json_response,$row_array);
					
						
						return $json_response;
				
	}

?>