<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='get_id') {

		global $conn;
		$q2 = $conn->query("select COUNT(*) from listing where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);

	}
	
	
	else if ($action == "customer"){
	 	global $conn;
		$customer =$_POST['customer'];
	  	$sql = $conn->query("select concat(customer_id,'-',title,'. ',firstname,' ',lastname) as 'customer' from customer where isactive=1 &&  concat(customer_id,'-',title,'. ',firstname,' ',lastname) LIKE '%$customer%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['customer'];
						$row_array['value'] = $row['customer'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}
	
	else if ($action == "location"){
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

	else if ($action == "address"){
	 	global $conn;
	
		$location =split('-',$_POST['location']);
		$address =$_POST['address'];
	  	$sql = $conn->query("select concat(address_id,'-',address_name) as 'address' from address where isactive=1 &&  concat(address_id,'-',address_name) LIKE '%$address%' && location_id = '$location[0]'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['address'];
						$row_array['value'] = $row['address'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	}	
	
	else if ($action == "listingdetail"){
	 	global $conn;
		
		$id =$_POST['id'];
	  	$sql = $conn->query("select t1.listing_id, t1.listing_name, concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer_id', concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname) as 'employee_id', t1.listing_category,t1.sertifikat, t1.end_of_contract, t1.sell_rent, t1.price, t1.description,(select concat(location_id,'-',location_name) from location where location_id = t4.location_id)as 'location_id', concat(t4.address_id,'-',t4.address_name) as 'address_id',t1.address, t1.picture, t1.note from listing t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join employee t3 on t1.employee_id = t3.employee_id inner join address t4 on t1.address_id = t4.address_id where t1.isactive=1 && t1.listing_id = '$id' limit 1");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['listing_id'] = $row['listing_id'];
						$row_array['listing_name'] = $row['listing_name'];
						$row_array['customer_id'] = $row['customer_id'];
						$row_array['employee_id'] = $row['employee_id'];
						$row_array['listing_category'] = $row['listing_category'];
						$row_array['sertifikat'] = $row['sertifikat'];
						$row_array['end_of_contract'] = $row['end_of_contract'];
						$row_array['sell_rent'] = $row['sell_rent'];
						$row_array['price'] = $row['price'];
						$row_array['description'] = json_encode($row['description'], JSON_UNESCAPED_UNICODE);
						$row_array['location_id'] = $row['location_id'];
						$row_array['address_id'] = $row['address_id'];
						$row_array['address'] = $row['address'];
						$row_array['picture'] = json_encode($row['picture'], JSON_UNESCAPED_UNICODE);
						$row_array['note'] = $row['note'];
						
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
	
				
	}	
	
		else if($action == "add"){
			global $conn;
			$id = $_POST['id'];
			$name = $_POST['name'];
			$jualsewa = $_POST['jualsewa'];
			$listing = $_POST['listing'];
			$customer = $_POST['customer'];
			$employee = $_POST['employee'];
			$contract = $_POST['contract'];
			$harga = $_POST['harga'];
			$description = $_POST['description'];
			$sertifikat = $_POST['sertifikat'];
			$address =split('-',$_POST['address']);
			$alamat_lengkap = $_POST['alamat_lengkap'];
			$note = $_POST['note'];
			$picture = $_POST['picture'];
	
			$sql = "INSERT INTO listing ( listing_id,listing_name, customer_id, employee_id, listing_category,sertifikat, end_of_contract, sell_rent,price, description, address_id, address, picture, note, status, create_datetime, lastupdate, isactive)VALUES('".$id."','".$name."','".$customer."','".$employee."','".$listing."','".$sertifikat."','".$contract."','".$jualsewa."','".$harga."',\"".$description."\",'".$address[0]."','".$alamat_lengkap."',\"".$picture."\",'".$note."','Need Approval', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				echo "Data Berhasil Dimasukkan!!!";
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
		
	else if ($action=="list_table") {
		global $conn;
		$requestData= $_REQUEST;
		$dutaid =$_POST['dutaid'];
		$columns = array( 
			0 =>'listing', 
			1 => 'sell_rent',
			2 => 'listing_category',
			3 => 'price',
			4 => 'end_of_contract',
			5 => 'address',
			6 => 'status',
			7 => 'note'			
		);

		$sql = "select concat(t1.listing_id,'-',t1.listing_name) as 'listing',concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',sell_rent, listing_category,price,end_of_contract, t3.address_name as 'address',t1.status, t1.note from listing t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.employee_id = '$dutaid'" ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t1.listing_id,'-',t1.listing_name) as 'listing',concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',sell_rent, listing_category, price,end_of_contract, t3.address_name as 'address',t1.status, t1.note from listing t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.employee_id = '$dutaid'";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.listing_id,'-',t1.listing_name)  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
			$sql.="  OR  (t3.address_name)  LIKE '%".$requestData['search']['value']."%'"; 
	$sql.="  OR  (t1.listing_category)  LIKE '%".$requestData['search']['value']."%' )"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["listing"];
			$nestedData[] = $row["customer"];
			$nestedData[] = $row["sell_rent"];
			$nestedData[] = $row["listing_category"];
			$nestedData[] = $row["price"];
			$nestedData[] = $row["end_of_contract"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["status"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='editbutton(\"".$row["listing"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["listing"]."\")'>Delete</button> ";
			
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

	else if ($action=="list_table2") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'listing', 
			1 => 'customer',
			2 => 'sell_rent',
			3 => 'listing_category',
			4 => 'price',
			5 => 'end_of_contract',
			6 => 'address',
			7 => 'status',
			8 => 'note'			
		);

		$sql = "select concat(t1.listing_id,'-',t1.listing_name) as 'listing',concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',sell_rent, listing_category,price,end_of_contract, t3.address_name as 'address',t1.status, t1.note from listing t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.status<>'Need Approval'" ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t1.listing_id,'-',t1.listing_name) as 'listing',concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',sell_rent, listing_category, price,end_of_contract, t3.address_name as 'address',t1.status, t1.note from listing t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.status<>'Need Approval'";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.listing_id,'-',t1.listing_name)  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(t2.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
			$sql.="  OR  (t3.address_name)  LIKE '%".$requestData['search']['value']."%'"; 
	$sql.="  OR  (t1.listing_category)  LIKE '%".$requestData['search']['value']."%' )"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["listing"];
			$nestedData[] = $row["sell_rent"];
			$nestedData[] = $row["listing_category"];
			$nestedData[] = $row["price"];
			$nestedData[] = $row["end_of_contract"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["status"];
			$nestedData[] = $row["note"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["listing"]."\")'>Detail</button> ";
			
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
	
		else if($action == "edit"){
			global $conn;
			$id = $_POST['id'];
			$name = $_POST['name'];
			$jualsewa = $_POST['jualsewa'];
			$listing = $_POST['listing'];
			$customer = $_POST['customer'];
			$employee = $_POST['employee'];
			$contract = $_POST['contract'];
			$harga = $_POST['harga'];
			$description = $_POST['description'];
			$sertifikat = $_POST['sertifikat'];
			$address =split('-',$_POST['address']);
			$alamat_lengkap = $_POST['alamat_lengkap'];
			$note = $_POST['note'];
			$picture = $_POST['picture'];
			
			$q2 = $conn->query("select status from listing where listing_id='$id' limit 1");
			$result = $q2;
			$sumcol = $result->fetch_row();
			$statuslisting = $sumcol[0];
			
			$sql = "UPDATE listing SET listing_name='".$name."', customer_id='".$customer."', employee_id='".$employee."', listing_category='".$listing."',sertifikat='".$sertifikat."', end_of_contract='".$contract."', sell_rent='".$jualsewa."',price='".$harga."', description=\"".$description."\", address_id='".$address[0]."', address='".$alamat_lengkap."', picture=\"".$picture."\", note='".$note."', status='Need Approval',lastupdate=now() WHERE listing_id='$id'";
			
			if ($conn->query($sql) === TRUE) {
				if($statuslisting == 'Need Approval')
				{echo "Data Berhasil Di Edit, Silahkan Tunggu Approval dari Admin";}
				else{echo "oke";}
				
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
		}
		
	else if ($action == "delete"){
			$id = split('-',$_POST['id']);
			global $conn;
			$sql = "UPDATE listing SET lastupdate=now(),isactive=0 WHERE listing_id='$id[0]'";
			
			if ($conn->query($sql) === TRUE) {
					
					echo"Data berhasil di Hapus";
					
				}else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
	}
?>
