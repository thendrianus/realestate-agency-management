<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from employee where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
		
	}
	
	else if ($action == "job_id"){
	 	global $conn;
		$job_id =$_POST['job_id'];
	  	$sql = $conn->query("select concat(job_id,'-',name) as 'job_id' from job where isactive=1 &&  concat(job_id,'-',name) LIKE '%$job_id%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['job_id'];
						$row_array['value'] = $row['job_id'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}
	
	else if ($action=="list_table") {
			global $conn;
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'employee', 
			1 => 'job_id',
			2 => 'contact',
			3 => 'address',
			4 => 'status',
			5 => 'note'			
		);

		$sql = "select concat(t1.employee_id,'-',t1.title,'. ',t1.firstname,' ',t1.lastname) as 'employee',concat(t2.job_id,'-',t2.name) as 'job_id',concat(t1.phone_number,'-',t1.email) as 'contact',t1.address,t1.status ,t1.note from employee t1 inner join job t2 on t1.job_id = t2.job_id where t1.isactive=1";
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t1.employee_id,'-',t1.title,'. ',t1.firstname,' ',t1.lastname) as 'employee',concat(t2.job_id,'-',t2.name) as 'job_id',concat(t1.phone_number,'-',t1.email) as 'contact',t1.address,t1.status , t1.note from employee t1 inner join job t2 on t1.job_id = t2.job_id where t1.isactive=1";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.employee_id,'-',t1.title,'. ',t1.firstname,' ',t1.lastname)  LIKE '%".$requestData['search']['value']."%' )";    
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["employee"];
			$nestedData[] = $row["job_id"];
			$nestedData[] = $row["contact"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["status"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["employee"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["employee"]."\")'>Delete</button> ";
			
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
			$status = $_POST['status'];
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$job_id = $_POST['job_id'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$status = $_POST['status'];
			$note = $_POST['note'];
	
			$sql = "INSERT INTO employee ( employee_id, title, firstname,lastname,job_id,phone_number,email,address, status, note, create_datetime, lastupdate, isactive)VALUES('$id','$status','$firstname','$lastname','$job_id','$phone','$email','$address','$status', '$note', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "error";
				}
		}
		
	else if ($action == "editdata") {
		
			global $conn;
			$id = $_POST['id'];
				$sql = $conn->query("select t1.employee_id as 'employee_id', t1.title as 'title', t1.firstname as 'firstname', t1.lastname as 'lastname', concat(t2.job_id,'-',t2.name) as 'job_id', t1.phone_number as 'phone_number', t1.email as 'email', t1.address as 'address', t1.status as 'status', t1.note as 'note' from employee t1 inner join job t2 on t1.job_id = t2.job_id where t1.employee_id = '$id' ");
	
					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['employee_id'] = $row['employee_id'];
						$row_array['title'] = $row['title'];
						$row_array['firstname'] = $row['firstname'];
						$row_array['lastname'] = $row['lastname'];
						$row_array['job_id'] = $row['job_id'];
						$row_array['phone_number'] = $row['phone_number'];
						$row_array['email'] = $row['email'];
						$row_array['address'] = $row['address'];
						$row_array['status'] = $row['status'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
						
					}

				echo json_encode($json_response);
				
			
						
	}
	
	else if ($action == "edit") {
		
			global $conn;
			$id = $_POST['id'];
			$status = $_POST['status'];
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$job_id = $_POST['job_id'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$address = $_POST['status'];
			$note = $_POST['note'];
			
	 $sql = "UPDATE employee SET title='$status', firstname='$firstname',lastname='$lastname',job_id='$job_id',phone_number='$phone',email='$email',address='$address',status='$status', note='$note',  lastupdate=now() WHERE employee_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Update!!!";
				
				}else {
					echo "error";
				}
	}
	
	else if ($action == "delete") {
		
			global $conn;
			$id = $_POST['id'];
			
	 $sql = "UPDATE employee SET isactive=0 WHERE employee_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Delete!!!";
				
				}else {
					echo "error";
				}
	}

?>