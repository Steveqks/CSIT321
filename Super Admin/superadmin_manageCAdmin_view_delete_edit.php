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
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Edit Company Admin</h2>
			<?php   
				$form = "<form action'' id='ModifyAccount' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br><br>
						Company ID: <input type='text' value=" . $_SESSION['cAdminID'] . " readonly><br>
						First Name: <input type='text' name='oldfname' value='" . $_SESSION['fname'] . "' readonly> <br>
						Last Name: <input type='text' name='oldlname' value='" . $_SESSION['lname'] . "' readonly> <br>
						Email Address: <input type='text' name='oldemailAdd' value=" . $_SESSION['emailAdd'] . " readonly> <br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'> 
						TO
						<br><br>
						Company ID: <input type='text' name='cAdminID' value=" . $_SESSION['cAdminID'] . " readonly> <br>
						First Name: <input type='text' name='newfname' value='" . $_SESSION['fname'] . " ' maxlength='16'><br>
						Last Name: <input type='text' name='newlname' value='" . $_SESSION['lname'] . "' maxlength='16'><br>
						Email Address: <input type='text' name='newemailAdd' value=" . $_SESSION['emailAdd'] . " maxlength='32'><br>
						<input type='button' value='Update'onclick='confirmDiag();'>
						</form>
							</td>
						</tr>
						</table>
							";
				echo $form;
				
				echo @$_SESSION['message1'];
				echo @$_SESSION['message2'];
				echo @$_SESSION['message3'];
				
				
				
				if(isset($_POST['newlname'])){
					$newfname = $_POST['newfname'];			
					$newlname = $_POST['newlname'];
					$cAdminID = $_POST['cAdminID'];
					$newemailAdd = $_POST['newemailAdd'];
					
					if ($_POST['oldemailAdd'] != $_POST['newemailAdd']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

						//check if email exists.
						$result = mysqli_query($db,	"SELECT * FROM companyadmin WHERE Email = '$newemailAdd'") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result);
						// dont exists
						if($num_rows == 0){
							$result2 = mysqli_query($db,"UPDATE companyadmin SET Email = '$newemailAdd' WHERE CAdminID = '$cAdminID'") or die("update Error");
							$_SESSION['message1'] = "Email address changed";
							$_SESSION['emailAdd'] = $newemailAdd;
						}
						// exists
						else{
							$_SESSION['message1'] = "Email address already exists";
						}
					}
					else $_SESSION['message1'] = "";
					
					// if first name changed
					if(@$_POST['oldfname'] != @$newfname){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET FirstName = '$newfname' WHERE CAdminID = '$cAdminID'") or die("update Error");
						$_SESSION['message2'] = "<p>First Name updated.</p>";
						$_SESSION['fname'] = $newfname;
					}
					else $_SESSION['message2'] = "";
					
					// if last name changed
					if(@$_POST['oldlname'] != @$newlname){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET LastName = '$newlname' WHERE CAdminID = '$cAdminID'") or die("update Error");
						$_SESSION['message3'] = "<p>Last Name updated.</p>";
						$_SESSION['lname'] = $newlname;
					}
					else $_SESSION['message3'] = "";
					
					
					header('Location: superadmin_manageCAdmin_view_delete_edit.php');
					exit;
				}
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


