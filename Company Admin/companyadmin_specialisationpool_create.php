<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message'] = '';
	
	//create company
	if(isset($_POST['pname'])){
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

		$pname = $_POST['pname'];
		$specialisationID = $_POST['specialisationID'];
	
		
		//check if pool exists
		if(isPoolExists($companyID, $pname, $db)){
			//exist already
			$_SESSION['message'] = "<p>Specialisation Pool name \"". $pname . "\" is already in use.</p>";
		}
		
		//doesn't exist, add to db
		else
		{
			$result = mysqli_query($db,"
										INSERT INTO specialisationpoolinfo(MainPoolID, SpecialisationID, CompanyID, PoolName)
										VALUES (NULL, '$specialisationID', '$companyID', '$pname')
										") or die("Select Error");
			
			$_SESSION['message'] = "<p>Specialisation Pool \"". $pname . "\" Created.</p>";
		}		
	}
		
	function isPoolExists(string $companyID, string $pname, mysqli $db):bool{
		$sql = "SELECT * FROM specialisationpoolinfo WHERE CompanyID = '$companyID' AND PoolName = '$pname'";
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
		
            <form action = "" id='createPool' method = "post">
				<h2>Create Specialisation Pool</h2>
				
					
					<h4>Specialisation Pool Name: <input name = "pname" type = "text" placeholder = "Specialisation Pool Name" required maxlength='32'>
					</h4>

					<h4>
					<?php
					$companyID = $_SESSION['companyID'];
					
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$sql = "SELECT * FROM specialisation WHERE CompanyID = '$companyID' ";
						$qres = mysqli_query($db, $sql); 
						
						$select = 	"<label for='Specialisation'>Specialisation:</label>
									<select name='specialisationID' id=''>";		
						while ($Row = $qres->fetch_assoc()) {
							$select .= "<option value ='".$Row['SpecialisationID']."'> ". $Row['SpecialisationName'] . " </option>";
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
					let result = confirm("Create Specialisation Pool?");
					if (result)
					{
						document.getElementById('createPool').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


