<?php
session_start();

	include '../Session/session_check_companyadmin.php';
	
	include 'db_connection.php';

	
	$_SESSION['message'] = '';

	if(isset($_POST['specialisationID'])){
		$specialisationName = $_POST['specialisationName'];
		$specialisationID = $_POST['specialisationID'];
		
		//check if exists already.
		$result = mysqli_query($db,	"SELECT SpecialisationName FROM specialisation WHERE specialisation.SpecialisationName = '$specialisationName'; ") or die("Select Error");

		$num_rows=mysqli_num_rows($result);
		// dont exists
		if($num_rows == 0){
			$result = mysqli_query($db,"UPDATE specialisation SET SpecialisationName = '$specialisationName' WHERE specialisation.SpecialisationID = '$specialisationID'") or die("update Error");
			$_SESSION['message'] = "<p>specialisation name changed.</p>";
			$_SESSION['specialisationName'] = $specialisationName;
			$_SESSION['specialisationID'] = $specialisationID;
		}
		// exists
		else{
			$_SESSION['message'] = "<p>specialisation already exists!</p>";
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
		
			<h2>Edit Specialisation</h2>

			<?php   
				
				$form = "<form action'' id='ModifySpecialisation' method='POST'>
				
						<input type='hidden' value='" . $_SESSION['specialisationName'] . "' readonly>
						
						
						Specialisation Name: <input type='text' name='specialisationName' value='" . $_SESSION['specialisationName'] . "' maxlength='32'>
						<input type='hidden' name='specialisationID' value=" . $_SESSION['specialisationID'] . " >
						<input type='button' value='Update' onclick='confirmDiag()' >
						</form>";
				echo $form;
				
				echo $_SESSION['message'];
				
				

				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Submit Changes?");
					if (result)
					{
						document.getElementById('ModifySpecialisation').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


