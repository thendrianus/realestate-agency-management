<?php

include('connection.php');

$action =$_POST['action'];

			if($action == "add"){
			
			
			global $conn;
			
			
			$q2 = $conn->query("select COUNT(*) from contact_us where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();

			$num =$sumcol[0]+1;
			$date = "CNT".date("ym");
			$id= "".$date."".$num;			
			$name = $_POST['name'];
			$email = $_POST['email'];
			$contact = $_POST['contact'];
			$message = $_POST['message'];
			
	
			$sql = "INSERT INTO contact_us ( contact_us_id,name, email, contact_number, message, create_datetime,lastupdate, isactive)VALUES('".$id."','".$name."','".$email."','".$contact."','".$message."', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "oke";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
	else if ($action=="list_table") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 => 'contact_us_id', 
			1 => 'name',
			2 => 'email',
			3 => 'contact_number',
			4 => 'message',	
			5 => 'create_datetime',	
		);

		$sql = "SELECT contact_us_id,name, email, contact_number, message, create_datetime from contact_us where isactive=1";
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="SELECT contact_us_id,name, email, contact_number, message, create_datetime from contact_us where isactive=1";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(contact_us_id,'-',name)  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(email,'-',contact_number))  LIKE '%".$requestData['search']['value']."%')"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["contact_us_id"];
			$nestedData[] = $row["name"];
			$nestedData[] = $row["email"];
			$nestedData[] = $row["contact_number"];
			$nestedData[] = $row["message"];
			$nestedData[] = $row["create_datetime"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["contact_us_id"]."\")'>Delete</button> ";
			
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
		
	else if ($action == "delete"){
			$id = $_POST['id'];
			global $conn;
			$sql = "UPDATE contact_us SET lastupdate=now(),isactive=0 WHERE contact_us_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
					
					echo"Data berhasil di Hapus";
					
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
	}
?>