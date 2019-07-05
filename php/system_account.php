<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from account where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
		
	}
	
	else if ($action=="list_table") {
			global $conn;
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'account', 
			1 => 'employee',
			2 => 'username',
			3 => 'status',
			4 => 'account'
		);

		$sql = "select t1.account_id as 'account', concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'employee',t1.username, t1.status from account t1 inner join employee t2 on t1.employee_id = t2.employee_id where t1.isactive=1";
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select t1.account_id  as 'account', concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'employee',t1.username, t1.status from account t1 inner join employee t2 on t1.employee_id = t2.employee_id where t1.isactive=1";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( t1.account_id  LIKE '%".$requestData['search']['value']."%' ";
			$sql.="  OR  concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname)  LIKE '%".$requestData['search']['value']."%')";    
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["account"];
			$nestedData[] = $row["employee"];
			$nestedData[] = $row["username"];
			$nestedData[] = $row["status"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["account"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["account"]."\")'>Delete</button> ";
			
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
		
	else if ($action == "employee_id"){
	 	global $conn;
		$employee_id =$_POST['employee_id'];
	  	$sql = $conn->query("select concat(employee_id,'-',title,'. ',firstname,' ',lastname) as 'employee_id' from employee where isactive=1 &&  concat(employee_id,'-',title,'. ',firstname,' ',lastname) LIKE '%$employee_id%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['employee_id'];
						$row_array['value'] = $row['employee_id'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}

	else if($action == "add"){
			global $conn;
			$id = $_POST['id'];
			$employee_id = $_POST['employee_id'];
			$username = $_POST['username'];
			$password = $_POST['password'];
			$status = $_POST['status'];
	
			$sql = "INSERT INTO account ( account_id,employee_id, username, password, status, create_datetime, lastupdate, isactive)VALUES('$id','$employee_id','$username', '$password','$status', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "error";
				}
		}

	else if ($action == "edit") {
		
			$id = $_POST['id'];
			$employee_id = $_POST['employee_id'];
			$username = $_POST['username'];
			$password = $_POST['password'];
			$status = $_POST['status'];
			$sql='';
			if($password != '')
			{
			$sql = "UPDATE account SET employee_id='$employee_id', username='$username', status ='$status', lastupdate = now() WHERE account_id='$id'";
			}
			else{
				$sql = "UPDATE account SET employee_id='$employee_id', username='$username', password='$password', status ='$status', lastupdate = now() WHERE account_id='$id'";
				}
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Update!!!";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
	}
	
	else if ($action == "delete") {
		
			global $conn;
			$id = $_POST['id'];
			
	 $sql = "UPDATE account SET isactive=0 WHERE account_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Delete!!!";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
	}
	
	else if ($action == "editdata") {
		
			global $conn;
			$id = $_POST['id'];
				$sql = $conn->query("select t1.account_id , concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'employee',t1.username, t1.status from account t1 inner join employee t2 on t1.employee_id = t2.employee_id where t1.account_id = '$id' ");
	
					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['account_id'] = $row['account_id'];
						$row_array['status'] = $row['status'];
						$row_array['employee_id'] = $row['employee'];
						$row_array['username'] = $row['username'];
						array_push($json_response,$row_array);
						
					}
					
						echo json_encode($json_response);
						
	}

?>