<?php
session_start();

if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_specialisation_edit.php');
	exit;
}
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
			  <a href="#">Home</a>
			  <a href="#">Link 1</a>
			  <a href="#">Link 2</a>
			  <a href="#">Link 3</a>
			  <a href="#">Link 4</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Edit Specialisation</h2>

			<?php   
				echo "Change specialisation:<br>";
				$form = "<form action'' method='POST'>
						FROM 
						<input type='text' value=" . $_SESSION['specialisationName'] . " readonly>
						TO
						<input type='text' name='specialisationName' value=" . $_SESSION['specialisationName'] . ">
						<input type='hidden' name='specialisationID' value=" . $_SESSION['specialisationID'] . ">
						<input type='submit' name='submitSpecialisation' value='Update'>
						</form>";
				echo $form;
				
				if ($_SESSION['message']){
				echo $_SESSION['message'];
				}
				
				if(isset($_POST['submitSpecialisation'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

					$specialisationName = $_POST['specialisationName'];
					$specialisationID = $_POST['specialisationID'];
					
					//check if exists already.
					$result = mysqli_query($db,	"SELECT SpecialisationName FROM specialisation WHERE specialisation.SpecialisationName = '$specialisationName'; ") or die("Select Error");
		
					$num_rows=mysqli_num_rows($result);
					// dont exists
					if($num_rows == 0){
						$result = mysqli_query($db,"UPDATE specialisation SET SpecialisationName = '$specialisationName' WHERE specialisation.SpecialisationID = '$specialisationID'") or die("update Error");
						$_SESSION['message'] = "<p style='color: green;'>specialisation name changed.</p>";
					}
					// exists
					else{
						$_SESSION['message'] = "<p style='color: red;'>specialisation already exists!</p>";
					}
				}
				
			?>
        </div>
    </div>

</body>
</html>

