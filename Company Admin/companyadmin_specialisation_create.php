<?php
session_start();

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
			
			<form action = "", method = "post">
				<h2>Create Specialisation</h2>
				<input id = "specialisation" name = "specialisation" type = "text" placeholder = "Specialisation Name" required>
				<button id = "submitBtn" name = "submit">Create</button>
			</form>
			
				<?php   
					
					$companyID = $_SESSION['companyID'];
							
					if(isset($_POST['submit'])){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$specialisation = $_POST['specialisation'];

						//check if specialisation exists
						if(isSpecialisationExists($specialisation, $companyID , $db)){
							echo "<p style='color: red;'> Specialisation \"" . $specialisation . "\" already exists.</p>";
						}
						// don't exists create new
						else{
							$result = mysqli_query($db,"INSERT INTO specialisation (SpecialisationID, SpecialisationName, companyID) VALUES (NULL, '$specialisation', '$companyID')") or die("Select Error");
							echo "<p style='color: green;'> Specialisation \"" . $specialisation . "\" created.</p>";
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
        </div>
    </div>

</body>
</html>


