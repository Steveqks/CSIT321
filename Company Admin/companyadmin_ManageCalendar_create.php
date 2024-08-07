<?php
session_start();
				
	include '../Session/session_check_companyadmin.php';

	include 'db_connection.php';

	$_SESSION['message1'] = '';
				
	$companyID = $_SESSION['companyID'];
	
	//create user
	if(isset($_POST['date'])){

		$dateName = $_POST['dateName'];
		$date = $_POST['date'];
		
		//check if an entry on date exists
		$result = mysqli_query($db,	"SELECT * FROM calendar WHERE CompanyID = '$companyID' AND Date = '$date' ") or die("Select Error");

		$num_rows=mysqli_num_rows($result);
		// dont exists
		if($num_rows == 0){
			
			//check if entry on name exists
			$result = mysqli_query($db,	"SELECT * FROM calendar WHERE CompanyID = '$companyID' AND DateName = '$dateName' ") or die("Select Error");
			$num_rows=mysqli_num_rows($result);
			
			// dont exists
			if($num_rows == 0){
				//check if entry on name exists
				$result2 = mysqli_query($db,"INSERT INTO calendar(CalendarID, CompanyID, DateName, Date) 
											VALUES (NULL, '$companyID', '$dateName', '$date')") or die("update Error");
				$_SESSION['message1'] = "<p>New Calendar entry created. Date: ". $date . ", Name: ". $dateName . " </p>";
			}
			// exists
			else{
				$_SESSION['message1'] = "<p>A Calendar entry with the entered Name already exists.</p>";
			}
		}
		// exists
		else{
			$_SESSION['message1'] = "<p>An Calendar entry with the selected date already exists.</p>";

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
		
            <form action = "" id='CalenderEntry' method = "post">
				<h2>Create Calendar Entry</h2>

					<h4>Date Name: <input name = "dateName" type = "text" placeholder = "date name"  maxlength='32'  required>
					</h4>
					<h4>Date: <input name = "date" type = "date" placeholder = "date" required>
					</h4>
					<input type='button' value='Create' onclick='confirmDiag()'>
			</form>
			
			<?php   
				echo $_SESSION['message1'];
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Create Calender Entry?");
					if (result)
					{
						document.getElementById('CalenderEntry').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


