<?php
session_start();

	//create company
	if(isset($_POST['fname'])){
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

		$companyID = $_POST['companyID'];
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$emailadd = $_POST['emailadd'];
		$password = $_POST['password'];
		
		//check if company exists
		if(isCompanyExists($companyID, $db)){
			//check if email already exists in companyadmin
			if(isCAEmailExists($emailadd, $db)){
				echo "<p style='color: red;'>email already exists.</p>";
			}
			else{
			$result = mysqli_query($db,"INSERT INTO companyadmin (CAdminID, CompanyID, FirstName, LastName, Email, Password) VALUES (NULL, '$companyID', '$fname', '$lname', '$emailadd', '$password')") or die("Select Error");
			$_SESSION['message'] = "<p>Company admin\"".$fname." ".$lname."\" added to database.</p>";
			}
		}
		//doesn't exist, add to db
		else
		{
			$_SESSION['message'] = "<p>Company id \"".$companyID."\" does exists in database</p>";
		}
		header('Location: superadmin_manageCAdmin_view_delete.php');
		exit;
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
	function isCAEmailExists(string $emailadd, mysqli $db):bool{
		$sql = "SELECT * FROM companyadmin WHERE Email = '$emailadd'";
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
		
            <form action = "" id='create' method = "post">
				<h2>Create Company Admin</h2>
				<?php
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
					$sql = "SELECT * FROM company";
					$qres = mysqli_query($db, $sql); 
					$select = "<h4><label for='Company'>Company:</label>
									<select name='companyID' id=''>";		
					while ($Row = $qres->fetch_assoc()) {
							$select .= "<option value ='".$Row['CompanyID']."'> ID:". $Row['CompanyID']. ", " . $Row['CompanyName']. " </option>";
						}
						$select .= "</select></h4>";
						echo $select;
					
				?>
					
					<h4>First Name: <input name = "fname" type = "text" placeholder = "first name" required>
					</h4>
					<h4>Last Name: <input name = "lname" type = "text" placeholder = "last name" required>
					</h4>
					<h4>Email address: <input name = "emailadd" type = "text" placeholder = "email address" required>
					</h4>
					<h4>Password: <input name = "password" type = "text" placeholder = "password" required>
					</h4>
					<input type = "button" value='Create' onclick='confirmDiag();' >
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


