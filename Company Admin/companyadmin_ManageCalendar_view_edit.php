<?php
session_start();

	include '../Session/session_check_companyadmin.php';
	
	include 'db_connection.php';

	$_SESSION['message1'] = '';
	$_SESSION['message2'] = '';

	if(isset($_POST['newDate'])){
		$newDate = $_POST['newDate'];
		$newDateName = $_POST['newDateName'];
		$calendarID = $_SESSION['calendarID'];
		
		//check if there are changes in date name
		if ($_POST['oldDateName'] != $_POST['newDateName']){
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
			
				$form = "<form action'' id='ModifyEntry' method='POST'style='
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
						<br>
						<table >
						<tr>
							 <input type='hidden' name='oldDateName' value='" . $_SESSION['dateName']. "' readonly><br>
							<input type='hidden' name='oldDate' value=" . $_SESSION['date'] . " readonly> <br>
							</td>
							<td> 
							
							
							Date Name: <input type='text' name='newDateName' maxlength='32' value='" . $_SESSION['dateName'] . "' ><br> <br>
							Date: <input type='date' name='newDate' value=" . $_SESSION['date'] . " > <br> <br>
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


