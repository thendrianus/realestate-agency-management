<?php
session_start(); 
include('connection.php');
$action =$_POST['action'];

	if ($action=='login') {
		
		$username =$_POST['username'];
		$password =$_POST['password'];
		global $conn;
		
		$sql=  "select * from account where username='$username' AND password='$password'";
		
		$result =  mysqli_query($conn, $sql);
		$check_result = mysqli_num_rows($result);
		$fetchrow = $result->fetch_row();
		$dutaid =$fetchrow[1];
		$dutauser =$fetchrow[2];
		$account_status=$fetchrow[4];
		if($check_result>0){
			
							echo "oke";
							$_SESSION['dutaid'] = $dutaid;
							$_SESSION['dutauser'] = $dutauser;
							$_SESSION['account_status'] = $account_status;
						
			}

			else {

			echo "Silahkan cek lagi Username dan Password";

		}
			
	}


?>