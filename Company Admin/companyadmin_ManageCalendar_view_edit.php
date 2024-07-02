<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	if(isset($_POST['newDate'])){
		$newDate = $_POST['newDate'];
		$newDateName = $_POST['newDateName'];
		$calendarID = $_SESSION['calendarID'];
		
		//check if there are changes in date name
		if ($_POST['oldDateName'] != $_POST['newDateName']){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

			//check if date name exists
			$result = mysqli_query($db,	"SELECT * FROM calendar WHERE DateName = '$newDateName'  AND CompanyID = '$companyID' ") or die("Select Error");

			$num_rows=mysqli_num_rows($result);
			// dont exists
			if($num_rows == 0){
				$result2 = mysqli_query($db,"UPDATE calendar SET DateName = '$newDateName' WHERE CalendarID = '$calendarID'") or die("update Error");
				$_SESSION['message1'] = "<p>Calender name entry has been changed to " . $newDateName . "</p>";
				$_SESSION['dateName'] = $newDateName;
			}
			// exists
			else{
				$_SESSION['message1'] = "<p>Calendar name entry already exists</p>";
			}
		}
		else $_SESSION['message1'] = "";
		
		// if date changed
		if(@$_POST['oldDate'] != $_POST['newDate']){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

			//check if date already exists
			$result = mysqli_query($db,	"SELECT * FROM calendar WHERE Date = '$newDate' AND CompanyID = '$companyID' ") or die("Select Error");

			$num_rows=mysqli_num_rows($result);
			// dont exists
			if($num_rows == 0){
				$result2 = mysqli_query($db,"UPDATE calendar SET Date = '$newDate' WHERE CalendarID = '$calendarID'") or die("update Error");
				$_SESSION['message2'] = "<p>Calender date entry has been changed to " . $newDate . "</p>";
				$_SESSION['date'] = $newDate;
			}
			// exists
			else{
				$_SESSION['message2'] = "<p>Calender date entry already exists </p>";
			}
		}
		else $_SESSION['message2'] = "";
		
		header('Location: companyadmin_ManageCalendar_view_edit.php');
		exit;
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
		
			<h2>Edit Calendar Entry</h2>

			<?php   
				
				$companyID = $_SESSION['companyID'];
			
				$form = "<form action'' id='ModifyEntry' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						<b>FROM: </b>
						<br>
							Date Name: <input type='text' name='oldDateName' value='" . $_SESSION['dateName']. "' readonly><br>
							Date: <input type='date' name='oldDate' value=" . $_SESSION['date'] . " readonly> <br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'> 
							
							
						<b>TO:</b>
						<br>
							Date Name: <input type='text' name='newDateName' value='" . $_SESSION['dateName'] . "' ><br>
							Date: <input type='date' name='newDate' value=" . $_SESSION['date'] . " > <br>
						<input type='button' value='Update' onclick='confirmDiag()'>
						</form>
							</td>
						</tr>
						</table>
							";
				echo $form;

				echo $_SESSION['message1'];
				echo $_SESSION['message2'];
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Submit Changes?");
					if (result)
					{
						document.getElementById('ModifyEntry').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


