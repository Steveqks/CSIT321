<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message1'] = "";
	$_SESSION['message2'] = "";

	if(isset($_POST['newGroupName']))
		{
			$newGroupName = $_POST['newGroupName'];
			$groupID= $_SESSION['groupID'];



			
			// if group name changed
			if(@$_POST['oldGroupName'] != $_POST['newGroupName']){
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

				//check if team name exists
				$result = mysqli_query($db,	"SELECT * FROM specialisationgroupinfo WHERE GroupName = '$newGroupName' AND CompanyID = '$companyID' ") or die("Select Error");
	
				$num_rows=mysqli_num_rows($result);
				// dont exists
				if($num_rows == 0){
					$result2 = mysqli_query($db,"UPDATE specialisationgroupinfo SET GroupName = '$newGroupName' WHERE MainGroupID = '$groupID'") or die("update Error");
					$_SESSION['message2'] = "<p>Specialisation Group name has been changed to " . $newGroupName . "</p>";
				}
				// exists
				else{
					$_SESSION['message2'] = "<p>Specialisation Group name is already in use </p>";
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
			<h2>Edit Specialisation Group</h2>
								
  
			<?php     
				
				$companyID = $_SESSION['companyID'];;
				$groupID= $_SESSION['groupID'];
								
				$groupName = '';
				
				//get selected team data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"
										SELECT GroupName
										FROM 
											specialisationgroupinfo
										WHERE 
											specialisationgroupinfo.MainGroupID = '$groupID'; ") or die("Select Error");

				while ($Row = $result->fetch_assoc()) {
					$groupName =	$Row['GroupName'];
				}

				// fill and get necessary fields
				$form = "<form action'' id='ModifyAccount' method='POST'>
						<table >
						<tr>
				
						<input type='hidden' name='oldGroupName' value='" . $groupName . "' readonly>
							
						Group Name: <input type='text' name='newGroupName' value='" . $groupName . "' maxlength='32' >
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


