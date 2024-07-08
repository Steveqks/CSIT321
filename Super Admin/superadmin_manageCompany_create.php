<?php
session_start();

	//create company
	if(isset($_POST['companyName'])){
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

		$companyName = $_POST['companyName'];
		$planType = $_POST['planType'];
		$UEN = $_POST['UEN'];
		
		//check if company exists
		if(isCompanyExists($companyName, $db)){
			$_SESSION['message'] = "<p>Company \"".$companyName."\" already exists in database</p>";
		}
		//doesn't exist, add to db
		else
		{
			$result = mysqli_query($db,"INSERT INTO company (CompanyID, CompanyName, CompanyUEN, PlanID, Status) VALUES (NULL, '$companyName', '$UEN', '$planType', '1')") or die("Select Error");
			$_SESSION['message'] = "<p>Company \"".$companyName."\" added to database.</p>";
		}
		
	}
		
		
	function isCompanyExists(string $cname, mysqli $db):bool{
		$sql = "SELECT * FROM company WHERE CompanyName = '$cname'";
		$qres = mysqli_query($db, $sql); 

		$num_rows=mysqli_num_rows($qres);

		// dont exists
		if($num_rows > 0){
			return true; 
		}
		// exists
		else{
			return false; 
		}
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">

    <title>TrackMySchedule</title>
</head>
<body>

    <!-- Top Section -->
	<div style="border: 1px solid black; height: 20vh; overflow: hidden; text-align: left;">
        <img src="tms.png" alt="TrackMySchedule Logo" style="height: 100%; width: auto;">
    </div>

    <!-- Middle Section -->
    <div style="display: flex; border: 1px solid black; height: 80vh;">
        
        <!-- Left Section (Navigation) -->
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "" id = "CreateCompany" method = "post">
				<h2>Create Company</h2>
				<p>Company Name: 
					<input id = "companyName" name = "companyName" type = "text" placeholder = "Company Name" maxlength='16'required>
				</p>
				<p>Plan Type: 
					<input id = "planType" name = "planType" type = "text" placeholder = "Plan Type" maxlength='1' required>
				</p>
				
				<p>UEN: 
					<input id = "uen" name = "UEN" type = "text" placeholder = "UEN" maxlength='10' required>
				</p>
				<input type = 'button' value='Create' onclick='confirmDiag();'>
			</form>
			
			<?php   
					if(@$_SESSION['message'])
					{
						echo $_SESSION['message'];
					}
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create Company?");
					if (result)
					{
						document.getElementById('CreateCompany').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


