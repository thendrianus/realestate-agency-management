<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from customer where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
		
	}
	
	else if ($action=="list_table") {
			global $conn;
			$dutaid =$_POST['dutaid'];
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'customer', 
			2 => 'contact',
			3 => 'address',
			4 => 'note'			
		);

		$sql = "select concat(customer_id,'-',title,'. ',firstname,' ',lastname) as 'customer',concat(phone_number,'-',email) as 'contact',address, note from customer where isactive=1 && employee_id='$dutaid'";
		$query=mysqli_query($conn, $sql) or die("customer-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(customer_id,'-',title,'. ',firstname,' ',lastname) as 'customer',concat(phone_number,'-',email) as 'contact',address, note from customer where isactive=1 && employee_id='$dutaid'";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(customer_id,'-',title,'. ',firstname,' ',lastname)  LIKE '%".$requestData['search']['value']."%' )";    
		}
		$query=mysqli_query($conn, $sql)or die("customer-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("customer-grid-data.php: get customers");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["customer"];
			$nestedData[] = $row["contact"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["customer"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["customer"]."\")'>Delete</button> ";
			
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
			$employee = $_POST['employee'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$note = $_POST['note'];
	
			$sql = "INSERT INTO customer ( customer_id, title, firstname,lastname,employee_id,phone_number,email,address, note, create_datetime, lastupdate, isactive)VALUES('$id','$status','$firstname','$lastname','$employee','$phone','$email','$address', '$note', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "error";
				}
		}
		
	else if ($action == "editdata") {
		
			global $conn;
			$id = $_POST['id'];
				$sql = $conn->query("select t1.customer_id as 'customer_id', t1.title as 'title', t1.firstname as 'firstname', t1.lastname as 'lastname',concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as'employee', t1.phone_number as 'phone_number', t1.email as 'email', t1.address as 'address', t1.note as 'note' from customer t1 inner join employee t2 on t1.employee_id = t2.employee_id where customer_id = '$id' ");
	
					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['customer_id'] = $row['customer_id'];
						$row_array['title'] = $row['title'];
						$row_array['firstname'] = $row['firstname'];
						$row_array['lastname'] = $row['lastname'];
						$row_array['employee'] = $row['employee'];
						$row_array['phone_number'] = $row['phone_number'];
						$row_array['email'] = $row['email'];
						$row_array['address'] = $row['address'];
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
			$employee = $_POST['employee'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$address = $_POST['address'];
			$note = $_POST['note'];
			
	 $sql = "UPDATE customer SET title='$status', firstname='$firstname',lastname='$lastname',employee_id='$employee',phone_number='$phone',email='$email',address='$address', note='$note',  lastupdate=now() WHERE customer_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Update!!!";
				
				}else {
					echo "error";
				}
	}
	
	else if ($action == "delete") {
		
			global $conn;
			$id = $_POST['id'];
			
	 $sql = "UPDATE customer SET isactive=0 WHERE customer_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Delete!!!";
				
				}else {
					echo "error";
				}
	}

?>