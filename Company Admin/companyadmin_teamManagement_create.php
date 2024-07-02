<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	//create company
	if(isset($_POST['tname'])){
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

		$tname = $_POST['tname'];
		$managerID = $_POST['managerID'];
	
		
		//check if team exists
		if(isTeamExists($companyID, $tname, $db)){
			//exist already
			$_SESSION['message'] = "<p>Team name \"". $tname . "\" in use.</p>";
		}
		
		//doesn't exist, add to db
		else
		{
			$result = mysqli_query($db,"
										INSERT INTO teaminfo(MainTeamID, ManagerID, CompanyID, TeamName)
										VALUES (NULL, '$managerID', '$companyID', '$tname')
										") or die("Select Error");
			
			$_SESSION['message'] = "<p>Team \"". $tname . "\" Created.</p>";
		}
		
		header('Location: companyadmin_teamManagement_create.php');
		exit();
		
	}
		
	function isTeamExists(string $companyID, string $tname, mysqli $db):bool{
		$sql = "SELECT * FROM teaminfo WHERE CompanyID = '$companyID' AND TeamName = '$tname'";
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
		
            <form action = "" id='createTeam' method = "post">
				<h2>Create Team</h2>
				
					
					<h4>Team Name: <input name = "tname" type = "text" placeholder = "team name" required maxlength='32'>
					</h4>

					<h4>
					<?php
					$companyID = $_SESSION['companyID'];
					
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$sql = "SELECT * FROM existinguser WHERE Role = 'Manager' AND CompanyID = '$companyID' ";
						$qres = mysqli_query($db, $sql); 
						
						$select = 	"<label for='Manager'>Manager:</label>
									<select name='managerID' id=''>";		
						while ($Row = $qres->fetch_assoc()) {
							$select .= "<option value ='".$Row['UserID']."'> ". $Row['FirstName'] . " " . $Row['LastName']  ." </option>";
						}
						$select .= "</select>";
						echo $select;
						

					?>
					</h4>
					<input type='button' value = "Create" onclick='confirmDiag()'>
			</form>
			
			<?php   
				if(@$_SESSION['message']) echo $_SESSION['message'];
			?>
				
			
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create Team?");
					if (result)
					{
						document.getElementById('createTeam').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


