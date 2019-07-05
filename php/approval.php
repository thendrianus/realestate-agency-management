<?php

include('connection.php');

$action =$_POST['action'];

if ($action=="list_table") {
			global $conn;
		$requestData= $_REQUEST;

		$columns = array( 
			0 =>'approval_id', 
			1 => 'category',
			2 => 'target',
			3 => 'employee',
			4 => 'status',
			5 => 'approval_id'
		);

		$sql = "select t1.approval_id, t1.approval_category as 'category',t1.target_id as 'target',concat(t1.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'employee',t1.status from approval t1 inner join employee t2 on t1.employee_id = t2.employee_id where t1.isactive=1 && t1.status ='Need Approval'";
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql = "select t1.approval_id, t1.approval_category as 'category',t1.target_id as 'target',concat(t1.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'employee',t1.status from approval t1 inner join employee t2 on t1.employee_id = t2.employee_id where t1.isactive=1 && t1.status ='Need Approval'";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( t1.approval_category  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(t1.employee_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%')"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["approval_id"];
			$nestedData[] = $row["category"];
			$nestedData[] = $row["target"];
			$nestedData[] = $row["employee"];
			$nestedData[] = $row["status"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detail(\"".$row["target"]."-".$row["category"]."-".$row["approval_id"]."\")'>Detail</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='approval(\"".$row["target"]."-".$row["category"]."-".$row["approval_id"]."\")'>Approve</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='notapprove(\"".$row["target"]."-".$row["category"]."-".$row["approval_id"]."\")'>Not Approve</button> ";
			
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
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from approval where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();

			$num =$sumcol[0]+1;
			$date = "APP".date("ym");
			$approval_id= "".$date."".$num;
			$category = $_POST['category'];
			$employee = $_POST['employee'];
			$target = $_POST['target'];
			$status = $_POST['status'];
	
			$sql = "INSERT INTO approval ( approval_id, approval_category, employee_id, target_id, status, create_datetime, lastupdate, isactive)VALUES('".$approval_id."','".$category."','".$employee."','".$target."','".$status."', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!! Silahkan Tunggu Approval dari Admin";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
		
		else if($action == "approval"){
			
		global $conn;
		
			$id = $_POST['id'];
			$target = $_POST['target'];
			$approve = $_POST['approve'];
			$sql2 = "";
			$sql3 = "";
			$sql = "";
			$q = "UPDATE approval SET status='Approved' WHERE approval_id='$approve'";
				if($target == 'Add Listing')
					{
						$sql.= "UPDATE listing SET status='Available' WHERE listing_id='$id'";
						$sql2.= "select status from listing WHERE listing_id='$id' LIMIT 1";
						$sql3.= "select status from listing WHERE listing_id='$id' LIMIT 1";
					}
					else if($target == 'Add Closing')
					{
						 $sql.= "UPDATE closing SET status='Listing Closed' WHERE closing_id='$id'";
						  $s= "select listing_id from closing WHERE closing_id='$id' LIMIT 1";
						 $result =$conn->query($s);
						$sumcol = $result->fetch_row();
						$sql2.= "select status from listing WHERE listing_id='$sumcol[0]' LIMIT 1";
						$sql3.= "UPDATE listing SET status='Listing Closed' WHERE listing_id='$sumcol[0]'";
					}
					else if($target == 'Add Hold')
					{
						 $sql .= "UPDATE hold SET status='Listing Hold' WHERE hold_id='$id'";
						 $s= "select listing_id from hold WHERE hold_id='$id' LIMIT 1";
						 $result =$conn->query($s);
						$sumcol = $result->fetch_row();
						$sql2.= "select status from listing WHERE listing_id='$sumcol[0]' LIMIT 1";
						$sql3.= "UPDATE listing SET status='Listing Hold' WHERE listing_id='$sumcol[0]'";
					}
						$result =$conn->query($sql2);
						$sumcol = $result->fetch_row();
						
							if($sumcol[0] == 'Available' || $sumcol[0] =='Need Approval')
								{
									if ($conn->query($sql) === TRUE) {
										if ($conn->query($q) === TRUE) {
											if ($conn->query($sql3) === TRUE) {
											echo "Data Sudah Di Approve";
											}else {
											echo "Error: " . $q . "<br>" . $conn->error;
											}
										}else {
										echo "Error: " . $q . "<br>" . $conn->error;
										}
										
									}else {
										echo "Error: " . $sql . "<br>" . $conn->error;
									}
								}else
								{
									echo "Silahkan Cek Status Listing!!!";
								}
					
		}
		else if($action == "notapprove"){
			
		global $conn;
		
			$id = $_POST['id'];
			$target = $_POST['target'];
			$approve = $_POST['approve'];
			$sql = "";
			//echo "$id+$target+$approve";
			$q = "UPDATE approval SET status='Not Approved' WHERE approval_id='$approve'";
				if($target == 'Add Listing')
					{
						$sql.= "UPDATE listing SET status='Not Approved' WHERE listing_id='$id'";
						
						
					}
					else if($target == 'Add Closing')
					{
						 $sql.= "UPDATE closing SET status='Not Approved' WHERE closing_id='$id'";
						
					}
					else if($target == 'Add Hold')
					{
						 $sql.= "UPDATE hold SET status='Not Approved' WHERE hold_id='$id'";
						
					}
						if ($conn->query($sql) === TRUE) {
							if ($conn->query($q) === TRUE) {
								echo "Data Not Approved";
							}else {
							echo "Error: " . $sql . "<br>" . $conn->error;
							}
							
						}else {
							echo "Error: " . $sql . "<br>" . $conn->error;
						}

		}
?>