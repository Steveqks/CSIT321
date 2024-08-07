<?php
session_start();

	include 'db_connection.php';

	$_SESSION['message'] ='';

	//create company
	if(isset($_POST['fname'])){
		$companyID = $_POST['companyID'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$emailadd = $_POST['emailadd'];
		$password = $_POST['password'];
		
		//check if company exists
		if(isCompanyExists($companyID, $db)){
			//check if email already exists in companyadmin
			if(isEmailExists($emailadd, $db)){
				$_SESSION['message'] = "<p>email already exists in database.</p>";
			}
			else{
			$result = mysqli_query($db,"INSERT INTO companyadmin (CAdminID, CompanyID, FirstName, LastName, Email, Password, Status) VALUES (NULL, '$companyID', '$fname', '$lname', '$emailadd', '$password', '1')") or die("Select Error");
			$_SESSION['message'] = "<p>Company admin\"".$fname." ".$lname."\" added to database.</p>";
			}
		}
		//doesn't exist, add to db
		else
		{
			$_SESSION['message'] = "<p>Company id \"".$companyID."\" does exists in database</p>";
		}
	}
		
	function isCompanyExists(string $companyID, mysqli $db):bool{
		$sql = "SELECT * FROM company WHERE CompanyID = '$companyID'";
		$qres = mysqli_query($db, $sql); 

		$num_rows=mysqli_num_rows($qres);

		// exists
		if($num_rows > 0){
			return true; 
		}
		// dont exists
		else{
			return false; 
		}
	}
	function isEmailExists(string $emailadd, mysqli $db):bool{
		$sql = "SELECT * FROM companyadmin WHERE Email = '$emailadd'";
		$qres = mysqli_query($db, $sql); 

		$num_rows=mysqli_num_rows($qres);

		// exists
		if($num_rows > 0){
			return true; 
		}
		// dont exists
		else{
			$sql1 = "SELECT * FROM existinguser WHERE Email = '$emailadd'";
			$qres1 = mysqli_query($db, $sql1); 

			$num_rows=mysqli_num_rows($qres1);
			if($num_rows > 0){
				return true;
			}
			else{
				$sql2 = "SELECT * FROM superadmin WHERE Email = '$emailadd'";
				$qres2 = mysqli_query($db, $sql2); 

				$num_rows=mysqli_num_rows($qres2);
				if($num_rows > 0){
					return true;
				}
				else return false;
			}
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
		
            <form action = "" id='create' method = "post" style='
																				flex: 0 0 48%;
																				display: inline-flex;
																				justify-content: space-between;
																				padding: 8px;
																				border: 1px solid #ddd;
																				border-radius: 4px;
																				box-sizing: border-box;
																				width: 80%;
																				margin-bottom: 15px;
																				margin-bottom: 5px;
																				display: flex;
																				flex-direction: column;
																				margin-bottom: 15px;
																				background-color: #f0f0f0;
																				padding: 20px;
																				border-radius: 5px;
																				max-width: 600px;
																				display: flex;
																				flex-direction: column;
																					'>
				<h2>Create Company Admin</h2>
				<?php
					$sql = "SELECT * FROM company";
					$qres = mysqli_query($db, $sql); 
					$select = "<table>
									<tr>
										<td>
											<label for='Company'>Company:</label>
											<select name='companyID' id=''>";		
					while ($Row = $qres->fetch_assoc()) {
							$select .= "<option value ='".$Row['CompanyID']."'> ID:". $Row['CompanyID']. ", " . $Row['CompanyName']. " </option>";
						}
						$select .= "</select> <br>";
						echo $select;
					
				?>
					
						
								First Name: <input name = "fname" type = "text" placeholder = "first name"  maxlength='16' required> <br>
								
								Last Name: <input name = "lname" type = "text" placeholder = "last name"  maxlength='16' required> <br>
								
							</td>
							<td>
								Email address: <input name = "emailadd" type = "text" placeholder = "email address"  maxlength='32' required> <br>
								
								Password: <input name = "password" type = "password" placeholder = "password" maxlength='16' required> <br>
								
								<input type = "button" value='Create' onclick='confirmDiag();' >
							</td>
			</form>
			
			<?php   
				echo $_SESSION['message'];
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create Company Admin?");
					if (result)
					{
						document.getElementById('create').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


