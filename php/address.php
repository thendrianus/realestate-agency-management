<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from address where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
		
	}
	
	else if ($action == "location_id"){
	 	global $conn;
		$location =$_POST['location'];
	  	$sql = $conn->query("select concat(location_id,'-',location_name) as 'location' from location where isactive=1 &&  concat(location_id,'-',location_name) LIKE '%$location%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['location'];
						$row_array['value'] = $row['location'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}
	
	else if ($action=="list_table") {
			global $conn;
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'address', 
			1 => 'location',
			2 => 'note',
			3 => 'address',
			4 => 'address'			
		);

		$sql = "select concat(t1.address_id,'-',t1.address_name) as 'address',concat(t2.location_id,'-',t2.location_name) as 'location', t1.note from address t1 inner join location t2 on t1.location_id = t2.location_id where t1.isactive=1";
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t1.address_id,'-',t1.address_name) as 'address',concat(t2.location_id,'-',t2.location_name) as 'location', t1.note from address t1 inner join location t2 on t1.location_id = t2.location_id where t1.isactive=1";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.address_id,'-',t1.address_name)  LIKE '%".$requestData['search']['value']."%' ";
			$sql.="  OR  concat(t2.location_id,'-',t2.location_name)  LIKE '%".$requestData['search']['value']."%')";    
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["address"];
			$nestedData[] = $row["location"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["address"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["address"]."\")'>Delete</button> ";
			
			$data[] = $nestedData;
		}

		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalData ),  
					"recordsFiltered" => intval( $totalFiltered ), 
					"data"            => $data   
					);

		echo json_encode($json_data); 

		}
		
	else if($action == "add"){
			global $conn;
			$id = $_POST['id'];
			$address = $_POST['address'];
			$location = $_POST['location'];
			$note = $_POST['note'];
	
			$sql = "INSERT INTO address ( address_id, address_name, location_id, note, create_datetime, lastupdate, isactive)VALUES('$id','$address','$location', '$note', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "error";
				}
		}
		
	else if ($action == "edit") {
		
			global $conn;
			$id = $_POST['id'];
			$address = $_POST['address'];
			$location = $_POST['location'];
			$note = $_POST['note'];	
			
	 $sql = "UPDATE address SET address_name='$address', location_id='$location', note='$note', lastupdate=now() WHERE address_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Update!!!";
				
				}else {
					echo "error";
				}
	}
	
	else if ($action == "delete") {
		
			global $conn;
			$id = $_POST['id'];
			
	 $sql = "UPDATE address SET isactive=0 WHERE address_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Delete!!!";
				
				}else {
					echo "error";
				}
	}
	
	else if ($action == "editdata") {
		
			global $conn;
			$id = $_POST['id'];
				$sql = $conn->query("select t1.address_id as 'address_id',t1.address_name as 'address_name',concat(t2.location_id,'-',t2.location_name) as 'location', t1.note as 'note' from address t1 inner join location t2 on t1.location_id = t2.location_id where t1.address_id = '$id'");
	
					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['address_id'] = $row['address_id'];
						$row_array['address_name'] = $row['address_name'];
						$row_array['location'] = $row['location'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
						
					}
					
						echo json_encode($json_response);
						
	}
?>