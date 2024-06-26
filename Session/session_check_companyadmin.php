<?php
	$cadminID = $_SESSION['cadminID'];
	$companyID = $_SESSION['companyID'];
	
	//check company status
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$sql = "SELECT * FROM company WHERE Status = '1' AND CompanyID = $companyID";
	$qres = mysqli_query($db, $sql); 
	
	$num_rows=mysqli_num_rows($qres);
			
	// company not suspended
	if($num_rows == 1){
		//check for CAdmin status
		$sql = "SELECT * FROM companyadmin WHERE Status = '1' AND CAdminID = $cadminID";
		$qres = mysqli_query($db, $sql); 
		
		$num_rows=mysqli_num_rows($qres);
				
			// suspended companyadmin
			if($num_rows == 0)
			{
				header("Location:../Session/suspended_companyadmin.php"); 
				exit;
			}
	}
	else 	//suspended company
	{
		header("Location:../Session/suspended_company.php"); 
		exit;
	}
?>