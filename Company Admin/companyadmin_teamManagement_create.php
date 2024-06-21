<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="a.css">

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
			<div class="vertical-menu" style="border-right: 1px solid black; padding: 0px;">
				<a href="companyadmin_homepage.php">Home</a>
				<a href="companyadmin_ManageAccount.php">Manage Account</a>
				<a href="companyadmin_ManageUserAccounts_create.php">Manage User Accounts > Create</a>
				<a href="companyadmin_ManageUserAccounts_view.php">Manage User Accounts > View</a>
				<a href="companyadmin_specialisation_create.php">Manage Specialisation > Create </a>
				<a href="companyadmin_specialisation_view_delete.php">Manage Specialisation > View</a>
				<a href="companyadmin_teamManagement_create.php">Manage Team > Create </a>
				<a href="companyadmin_teamManagement_view_delete.php">Manage Team > View</a>
				<a href="Logout.php">Logout</a>

			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "", method = "post">
				<h2>Create Team</h2>
				
					
					<h4>Team Name: <input name = "tname" type = "text" placeholder = "team name" required >
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
							$select .= "<option value ='".$Row['UserID']."'> ID:" . $Row['UserID']. ", ". $Row['FirstName'] . " " . $Row['LastName']  ." </option>";
						}
						$select .= "</select>";
						echo $select;
						
					?>
					</h4>
					<button id = "submitBtn" name = "submit">Create</button>
			</form>
			
			<?php   
		
				//create company
				if(isset($_POST['submit'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

					$tname = $_POST['tname'];
					$managerID = $_POST['managerID'];
				
					
					//check if team exists
					if(isTeamExists($companyID, $tname, $db)){
						//exist already
						echo "<p style='color: red;'>Team name \"". $tname . "\" in use.</p>";
					}
					
					//doesn't exist, add to db
					else
					{
						$result = mysqli_query($db,"
													INSERT INTO teaminfo(MainTeamID, ManagerID, CompanyID, TeamName)
													VALUES (NULL, '$managerID', '$companyID', '$tname')
													") or die("Select Error");
						
						echo "<p style='color: green;'>Team \"". $tname . "\" Created.</p>";
					}
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
        </div>
    </div>

</body>
</html>


