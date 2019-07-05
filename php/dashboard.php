<?php

include('connection.php');

$action =$_POST['action'];

	if ($action=='startview') {
		$id = $_POST['id'];
		global $conn;
		$q2 = $conn->query("select (select count(*) from listing where status ='Available' limit 1 ) as 'alllisting',(select count(*) from listing where status ='Available' && employee_id ='$id' limit 1 ) as 'mylisting', (select count(*) from closing where status ='Listing Closed' &&  MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'allclosing',(select count(*) from closing where status ='Listing Closed' && employee_id ='$id' &&  MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'myclosing',(select count(*) from hold where status ='Listing Hold' &&  MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'allhold',(select count(*) from hold where status ='Listing Hold' && employee_id ='$id' &&  MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'myhold',(select count(*) from customer where MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'allcustomer',(select count(*) from customer where employee_id ='$id' &&  MONTH(create_datetime)=MONTH(now()) limit 1 ) as 'mycustomer'  from listing limit 1");
		
					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['listing'] = $sumcol[1].'/'.$sumcol[0];
					$row_array['closing'] = $sumcol[3].'/'.$sumcol[2];
					$row_array['hold'] = $sumcol[5].'/'.$sumcol[4];
					$row_array['customer'] = $sumcol[7].'/'.$sumcol[6];
					array_push($json_response,$row_array);
					echo json_encode($json_response);

	}
	
	else if ($action=="newlisting") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'listing', 
			2 => 'sell_rent',
			3 => 'listing_category',
			4 => 'price',
			5 =>'listing'			
		);

		$sql = "select concat(t1.listing_id,'-',t1.listing_name) as 'listing',t1.sell_rent, t1.listing_category,t1.price from listing t1 inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.status='Available'  &&  MONTH(t1.create_datetime)=MONTH(now())" ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t1.listing_id,'-',t1.listing_name) as 'listing',t1.sell_rent, t1.listing_category,t1.price from listing t1 inner join address t3 on t1.address_id = t3.address_id  where t1.isactive=1 && t1.status='Available'  &&  MONTH(t1.create_datetime)=MONTH(now())";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.listing_id,'-',t1.listing_name)  LIKE '%".$requestData['search']['value']."%' ";  
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
		
	else if ($action=="hotlisting") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'listing_id', 
			2 => 'sell_rent',
			3 => 'listing_category',
			4 => 'price',
			5 =>'listing'			
		);

		$sql = "select concat(t2.listing_id,'-',t2.listing_name) as 'listing_id', t2.sell_rent, t2.listing_category,t2.price from hotlisting t1 inner join listing t2 on t1.listing_id = t2.listing_id where t1.isactive=1 " ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t2.listing_id,'-',t2.listing_name) as 'listing_id', t2.sell_rent, t2.listing_category,t2.price from hotlisting t1 inner join listing t2 on t1.listing_id = t2.listing_id where t1.isactive=1 ";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t1.listing_id,'-',t1.listing_name)  LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (t1.listing_category)  LIKE '%".$requestData['search']['value']."%' )"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["listing_id"];
			$nestedData[] = $row["sell_rent"];
			$nestedData[] = $row["listing_category"];
			$nestedData[] = $row["price"];
			$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["listing_id"]."\")'>Detail</button> ";
			
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
		
	else if ($action=="newclosing") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'listing', 
			2 => 'employees',
			3 => 'customer',
			4 => 'payment',
			5 =>'listing'			
		);

		$sql = "select concat(t2.listing_id,'-',t2.listing_name) as 'listing',concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname) as 'employees', concat(t4.title,'. ',t4.firstname,' ',t4.lastname) as 'customer',t1.payment from closing t1 inner join listing t2 on t1.listing_id = t2.listing_id inner join employee t3 on t1.employee_id = t3.employee_id inner join customer t4 on t1.customer_id = t4.customer_id  where t1.isactive=1 && t1.status='Listing Closed'  &&  MONTH(t1.create_datetime)=MONTH(now())" ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t2.listing_id,'-',t2.listing_name) as 'listing',concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname) as 'employees', concat(t4.title,'. ',t4.firstname,' ',t4.lastname) as 'customer',t1.payment from closing t1 inner join listing t2 on t1.listing_id = t2.listing_id inner join employee t3 on t1.employee_id = t3.employee_id inner join customer t4 on t1.customer_id = t4.customer_id  where t1.isactive=1 && t1.status='Listing Closed'  &&  MONTH(t1.create_datetime)=MONTH(now())";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t2.listing_id,'-',t2.listing_name) LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname))  LIKE '%".$requestData['search']['value']."%' )"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["listing"];
			$nestedData[] = $row["employees"];
			$nestedData[] = $row["customer"];
			$nestedData[] = $row["payment"];
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
		
		
	else if ($action=="newhold") {
			global $conn;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'listing', 
			2 => 'employees',
			3 => 'customer',
			4 => 'payment',
			5 =>'hold_deadline'			
		);

		$sql = "select concat(t2.listing_id,'-',t2.listing_name) as 'listing',concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname) as 'employees', concat(t4.title,'. ',t4.firstname,' ',t4.lastname) as 'customer',t1.hold_deadline from hold t1 inner join listing t2 on t1.listing_id = t2.listing_id inner join employee t3 on t1.employee_id = t3.employee_id inner join customer t4 on t1.customer_id = t4.customer_id  where t1.isactive=1 && t1.status='Listing Hold'  &&  MONTH(t1.create_datetime)=MONTH(now())" ;
		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get satu");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  

		$sql ="select concat(t2.listing_id,'-',t2.listing_name) as 'listing',concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname) as 'employees', concat(t4.title,'. ',t4.firstname,' ',t4.lastname) as 'customer',t1.hold_deadline from hold t1 inner join listing t2 on t1.listing_id = t2.listing_id inner join employee t3 on t1.employee_id = t3.employee_id inner join customer t4 on t1.customer_id = t4.customer_id  where t1.isactive=1 && t1.status='Listing Hold'  &&  MONTH(t1.create_datetime)=MONTH(now())";

		if( !empty($requestData['search']['value']) ) {  
			$sql.="  AND ( concat(t2.listing_id,'-',t2.listing_name) LIKE '%".$requestData['search']['value']."%' ";  
			$sql.="  OR  (concat(t3.employee_id,'-',t3.title,'. ',t3.firstname,' ',t3.lastname))  LIKE '%".$requestData['search']['value']."%' )"; 			
		}
		$query=mysqli_query($conn, $sql)or die("employee-grid-data.php: get dua");;

		$totalFiltered = mysqli_num_rows($query); 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("employee-grid-data.php: get employees");;

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {  
			$nestedData=array(); 

			$nestedData[] = $row["listing"];
			$nestedData[] = $row["employees"];
			$nestedData[] = $row["customer"];
			$nestedData[] = $row["hold_deadline"];
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
?>