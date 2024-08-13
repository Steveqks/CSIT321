<?php
session_start();
				
	include_once('../Session/session_check_companyadmin.php');

	include 'db_connection.php';

	$companyID = $_SESSION['companyID'];
	$_SESSION['message1'] = "";



	//create user
	if(isset($_POST['fname'])){
		
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$emailadd = $_POST['emailadd'];
		$gender = $_POST['gender'];
		$password = $_POST['password'];
		
		$role = $_POST['role'];
		$specialisation = $_POST['specialisationID'];
		
		//check if email exists in existinguser
		if(isUserEmailExists($emailadd, $db)){
			$_SESSION['message1'] = "<p>Email Address\"".$emailadd."\" already in use.</p>";
		}
		//doesn't exist, add to db
		else
		{	
			//check if company has reach maximum limit of user accounts
			$result = mysqli_query($db, "SELECT 
											COUNT(eu.UserID) AS user_count,
											p.UserAccess
										FROM 
											existinguser eu
										JOIN 
											company c ON eu.CompanyID = c.CompanyID
										JOIN 
											plans p ON c.PlanID = p.PlanID
										WHERE 
											eu.CompanyID = '$companyID'
										GROUP BY 
											p.UserAccess") or die("Reached Max Capacity"); 
						
			while ($Row = $result->fetch_assoc()) 
			{
				$userCount = $Row['user_count'];
				$userAccessLimit = $Row['UserAccess'];
			}
			
			$num_rows=mysqli_num_rows($result);

			// exists
			if($userCount < $userAccessLimit)
			{
				$result = mysqli_query($db,"INSERT INTO existinguser 
											(UserID, CompanyID, SpecialisationID, Role, FirstName, LastName, Gender, Email, Password, Status) 
									VALUES 	(NULL, '$companyID', '$specialisation', '$role', '$fname', '$lname', '$gender', '$emailadd', '$password', '1')") or die("Insert Error");
				$_SESSION['message1'] = "<p>User Account for \"".$fname." ".$lname."\" created.</p>";
			}
			else $_SESSION['message1'] = "<p>Company has reached maximum allowed user accounts. Delete unnecessary accounts or upgrade subscription plan.</p>";
		}
	}
		
	function isUserEmailExists(string $emailadd, mysqli $db):bool{
		$sql = "SELECT * FROM existinguser WHERE Email = '$emailadd'";
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
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "" id='create' method = "post" onload='isManager()' style='
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
				<h2>Create User Account</h2>

					<table>
						<tr>
							<td>
								First Name: <br><input name = "fname" type = "text" placeholder = "first name" maxlength='16' required> <br>
								Last Name: <br><input name = "lname" type = "text" placeholder = "last name" maxlength='16'  required> <br>
								Email address: <br><input name = "emailadd" type = "text" placeholder = "email address" maxlength='32'  required> <br>
								<br>
													<input type='button' value='Create' onclick='confirmDiag()'>

							</td> <td>				
								Gender: <br><input name = "gender" type = "text" placeholder = "gender" maxlength='5'  required> <br>
							
								Password: <br><input name = "password" type = "password" placeholder = "password" maxlength='16'  required> <br>
					
								<label for="Role">Role: <br></label>
								<select name="role" id="RoleSelect" onchange='isManager()' required>
  <option disabled selected value> -- select a role -- </option>									<option value="Manager">Manager</option>
									<option value="FT">FT</option>
									<option value="PT">PT</option>
						  
					  </select>						  

					
					
						<?php
							
							//find manager specialisation id
							$sql = "SELECT * FROM specialisation WHERE CompanyID = '$companyID' AND SpecialisationName = 'Manager'";
							$qres = mysqli_query($db, $sql); 
							while ($Row = $qres->fetch_assoc()) 
							{
								$mid = $Row['SpecialisationID'];			
								$_POST['specialisationID'] = $Row['SpecialisationID'];			
							}
														
							//find all specialisation and omit manager
							@$sql = "SELECT * FROM specialisation WHERE CompanyID = '$companyID' AND SpecialisationID != '$mid'";
							$qres = mysqli_query($db, $sql); 
							
							$select = 	"<br><label for='Specialisation'>Specialisation:<br></label>
										<select name='specialisationID' id='SelectSpecialisation'>";		
							while ($Row = $qres->fetch_assoc()) 
							{
								$select .= "<option value ='" . $Row['SpecialisationID'] . "'> " 
										. $Row['SpecialisationName'] . " </option>";
							}
							$select .= "</select> <br>";
							
							$encodedselect= json_encode($select);
	
						?><span id="span tag"> </span>

					<br><br>
					</td> 
			</form>
			
			<?php   
			echo $_SESSION['message1'];
			
				
				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create User?");
					if (result)
					{
						document.getElementById('create').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				
				function isManager(){
					var RSelect = document.getElementById("RoleSelect");
					var SSelect = document.getElementById('SelectSpecialisation');
					var spantag = document.getElementById('span tag');
					var specialisationID2 = <?php echo json_encode($mid); ?>; 
					
					//show specialisation option if role is not manager
					if(RSelect.value != "Manager")
					{
						spantag.innerHTML = <?php echo $encodedselect; ?>;
					}
					else
					{
						spantag.innerHTML = "<input type='hidden' name='specialisationID' value='" + specialisationID2.toString() + "'>";
					}
					
					
				}
				
			</script>
</body>
</html>


