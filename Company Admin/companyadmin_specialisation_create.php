<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	include 'db_connection.php';


	$_SESSION['message1'] = '';

	if(isset($_POST['specialisation'])){
		$specialisation = $_POST['specialisation'];

		//check if specialisation exists
		if(isSpecialisationExists($specialisation, $companyID , $db)){
			$_SESSION['message1'] = "<p> Specialisation \"" . $specialisation . "\" already exists.</p>";
		}
		// don't exists create new
		else{
			$result = mysqli_query($db,"INSERT INTO specialisation (SpecialisationID, SpecialisationName, companyID) VALUES (NULL, '$specialisation', '$companyID')") or die("Select Error");
			$_SESSION['message1'] = "<p> Specialisation \"" . $specialisation . "\" created.</p>";
		}
	}
						
	function isSpecialisationExists(string $specialisation, string $companyID, mysqli $db):bool{
		$sql = "SELECT * FROM specialisation WHERE SpecialisationName = '$specialisation' AND CompanyID = '$companyID'";
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
			
			<form action = "" id='submitSpecialisation'  method = "post">
				<h2>Create Specialisation</h2>
				<input id = "specialisation" name = "specialisation" type = "text" placeholder = "Specialisation Name" maxlength='32' required>
				<input type='button' value='Create' onclick='confirmDiag()'>
			</form>
			
				<?php   
					
					$companyID = $_SESSION['companyID'];
							
					if(@$_SESSION['message1'])
					echo $_SESSION['message1'];
				?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create Specification?");
					if (result)
					{
						document.getElementById('submitSpecialisation').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


