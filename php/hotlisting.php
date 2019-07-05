<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from hotlisting where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

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
			0 =>'hotlisting_id', 
			2 => 'listing_id',
			3 => 'hotlisting_id',	
		);

		$sql = "select t1.hotlisting_id, concat(t2.listing_id,'-',t2.listing_name) as 'listing_id' from hotlisting t1 inner join listing t2 on t1.listing_id = t2.listing_id where t1.isactive=1";
		$query=mysqli_query($conn, $sql) or die("customer-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select t1.hotlisting_id, concat(t2.listing_id,'-',t2.listing_name) as 'listing_id' from hotlisting t1 inner join listing t2 on t1.listing_id = t2.listing_id where t1.isactive=1";

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

			$nestedData[] = $row["hotlisting_id"];
			$nestedData[] = $row["listing_id"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["hotlisting_id"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["hotlisting_id"]."\")'>Delete</button> ";
			
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
			$listingid = $_POST['listingid'];
	
			$sql = "INSERT INTO hotlisting ( hotlisting_id, listing_id, create_datetime, lastupdate, isactive)VALUES('$id','$listingid', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "error";
				}
		}
		
	else if ($action == "editdata") {
		
			global $conn;
			$id = $_POST['id'];
				$sql = $conn->query("select t1.hotlisting_id, concat(t2.listing_id,'-',t2.listing_name) as 'listing_id' from hotlisting t1 inner join listing t2 on t1.listing_id = t2.listing_id where t1.isactive=1 && hotlisting_id = '$id' ");
	
					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['hotlisting_id'] = $row['hotlisting_id'];
						$row_array['listing_id'] = $row['listing_id'];
						array_push($json_response,$row_array);
						
					}
					
						echo json_encode($json_response);
						
	}
	
	else if ($action == "edit") {
		
			global $conn;
			$id = $_POST['id'];
			$listingid = $_POST['listingid'];
			
	 $sql = "UPDATE hotlisting SET listing_id='$listingid', lastupdate=now() WHERE hotlisting_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Update!!!";
				
				}else {
					echo "error";
				}
	}
	
	else if ($action == "delete") {
		
			global $conn;
			$id = $_POST['id'];
			
	 $sql = "UPDATE hotlisting SET isactive=0 WHERE hotlisting_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Di Delete!!!";
				
				}else {
					echo "error";
				}
	}

?>