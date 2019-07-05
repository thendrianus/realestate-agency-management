<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {

		global $conn;
		$q2 = $conn->query("select COUNT(*) from closing where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);

	}
	
	
	else if ($action == "employee"){
	 	global $conn;
		$employee =$_POST['employee'];
	  	$sql = $conn->query("select concat(employee_id,'-',title,'. ',firstname,' ',lastname) as 'employee' from employee where isactive=1 &&  concat(employee_id,'-',title,'. ',firstname,' ',lastname) LIKE '%$employee%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['employee'];
						$row_array['value'] = $row['employee'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}
	
	else if ($action == "listing"){
	 	global $conn;
		$listing =$_POST['listing'];
	  	$sql = $conn->query("select concat(listing_id,'-',listing_name) as 'listing' from listing where isactive=1 && status='Available' && concat(listing_id,'-',listing_name) LIKE '%$listing%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['listing'];
						$row_array['value'] = $row['listing'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}
	
	

	
	else if ($action=="list_table") {
		$dutastatus = $_POST['dutastatus'];
		$dutaid = $_POST['dutaid'];
		global $conn;
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'closing', 
			1 => 'listing_id',
			2 => 'employee',
			3 => 'customer',
			4 => 'payment',
			5 => 'status',
			6 => 'note'			
		);

		$sql = "select closing_id as 'closing',employee_id as 'employee',listing_id,customer_id as 'customer', payment,status, note from closing where isactive=1";
		if( $dutastatus != 'Administrator' ) {  
			$sql.="  AND employee_id = '$dutaid' ";  		
		}
		
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select closing_id as 'closing',employee_id as 'employee',listing_id,customer_id as 'customer', payment,status, note from closing where isactive=1";
		if( $dutastatus != 'Administrator' ) {  
			$sql.="  AND employee_id = '$dutaid' ";  		
		}
		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( closing_id  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (customer_id)  LIKE '%".$requestData['search']['value']."%')"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["closing"];
			$nestedData[] = $row["listing_id"];
			$nestedData[] = $row["employee"];
			$nestedData[] = $row["customer"];
			$nestedData[] = $row["payment"];
			$nestedData[] = $row["status"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["closing"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["closing"]."\")'>Delete</button> ";
			
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
			$listing = $_POST['listing'];
			$dp = $_POST['dp'];
			$payment = $_POST['payment'];
			$employee = $_POST['employee'];
			$customer = $_POST['customer'];
			$note = $_POST['note'];
	
			$sql = "INSERT INTO closing ( closing_id,listing_id,employee_id, customer_id,payment,downpayment,status,note, create_datetime, lastupdate, isactive)VALUES('".$id."','".$listing."','".$employee."','".$customer."','".$payment."','".$dp."','Need Approval','".$note."', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
		else if($action == "edit"){
			global $conn;
			$id = $_POST['id'];
			$listing = $_POST['listing'];
			$dp = $_POST['dp'];
			$payment = $_POST['payment'];
			$employee = $_POST['employee'];
			$customer = $_POST['customer'];
			$note = $_POST['note'];
	
			$q2 = $conn->query("select status from closing where closing_id='$id' limit 1");
			$result = $q2;
			$sumcol = $result->fetch_row();
			$statusclosing = $sumcol[0];
			
			$sql = "UPDATE closing SET listing_id='".$listing."',employee_id='".$employee."', customer_id='".$customer."',payment='".$payment."',downpayment='".$dp."',status='Need Approval',note='".$note."', lastupdate = now() WHERE closing_id='$id'";

			if ($conn->query($sql) === TRUE) {
				if($statusclosing == 'Need Approval')
					{echo "Data Berhasil Di Edit, Silahkan Tunggu Approval dari Admin";}
				else{echo "oke";}
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
		
	else if ($action == "editdata"){
			$id = $_POST['id'];
			global $conn;
			
		$q2 = $conn->query("select t1.closing_id, concat(t2.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname)as'employee_id',concat(t3.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname)as'customer_id', concat(t4.listing_id,'-',t4.listing_name) as 'listing_id',t1.downpayment, t1.payment, t1.note from closing t1 inner join employee t2 on t1.employee_id = t2.employee_id inner join customer t3 on t1.customer_id = t3.customer_id inner join listing t4 on t1.listing_id = t4.listing_id where t1.isactive=1 && t1.closing_id ='$id' limit 1");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['closing_id'] = $sumcol[0];
					$row_array['employee_id'] = $sumcol[1];
					$row_array['customer_id'] = $sumcol[2];
					$row_array['listing_id'] = $sumcol[3];
					$row_array['downpayment'] = $sumcol[4];
					$row_array['payment'] = $sumcol[5];
					$row_array['note'] = $sumcol[6];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	
	else if ($action == "delete"){
			$id = $_POST['id'];
			global $conn;
			$sql = "UPDATE closing SET lastupdate=now(),isactive=0 WHERE closing_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
					
					echo"Data berhasil di Hapus";
					
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
	}
?>