<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message1'] = "";
	$_SESSION['message2'] = "";

	if(isset($_POST['newPoolName']))
		{
			$newPoolName = $_POST['newPoolName'];
			$poolID= $_SESSION['poolID'];



			
			// if pool name changed
			if(@$_POST['oldPoolName'] != $_POST['newPoolName']){
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

				//check if team name exists
				$result = mysqli_query($db,	"SELECT * FROM specialisationpoolinfo WHERE PoolName = '$newPoolName' AND CompanyID = '$companyID' ") or die("Select Error");
	
				$num_rows=mysqli_num_rows($result);
				// dont exists
				if($num_rows == 0){
					$result2 = mysqli_query($db,"UPDATE specialisationpoolinfo SET PoolName = '$newPoolName' WHERE MainPoolID = '$poolID'") or die("update Error");
					$_SESSION['message2'] = "<p>Specialisation Pool name has been changed to " . $newPoolName . "</p>";
				}
				// exists
				else{
					$_SESSION['message2'] = "<p>Specialisation Pool name is already in use </p>";
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
			<h2>Edit Specialisation Pool</h2>
								
  
			<?php     
				
				$companyID = $_SESSION['companyID'];;
				$poolID= $_SESSION['poolID'];
								
				$poolName = '';
				
				//get selected team data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"
										SELECT PoolName
										FROM 
											specialisationpoolinfo
										WHERE 
											specialisationpoolinfo.MainPoolID = '$poolID'; ") or die("Select Error");

				while ($Row = $result->fetch_assoc()) {
					$poolName =	$Row['PoolName'];
				}

				// fill and get necessary fields
				$form = "<form action'' id='ModifyAccount' method='POST'>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br><br>
						Team Name: <input type='text' name='oldPoolName' value='" . $poolName . "' readonly>
						<br><br>
						</td>
							
						<td style='border: 2px solid black; border-collapse: collapse;'> 
						TO
						<br><br>
						Team Name: <input type='text' name='newPoolName' value='" . $poolName . "' maxlength='32' >
						<br>";
						
				
				
				$form .= "<br></td></tr> </table><input type='button' value='Update' onclick='confirmDiag()'></form>";
			
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
						document.getElementById('ModifyAccount').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


