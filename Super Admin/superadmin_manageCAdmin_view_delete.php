<?php
session_start();

	include_once('superadmin_manageCAdmin_view_functions.php');

	$_SESSION['message'] = '';

	if(isset($_POST['delete']) == 'yes')
	{
		$cAdminID = $_POST['cAdminID'];
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"DELETE FROM companyadmin WHERE CAdminID = '$cAdminID' ") or die("Select Error");
		$_SESSION['message'] = "Company Admin \"" .$_POST['fname']. " ". $_POST['lname'] . "\" deleted successfully";
	}

	if(isset($_POST['activateSuspend']))
	{
		$cAdminID = $_POST['cAdminID'];
		$status = $_POST['status'];
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		
		if($status == 1){
			$result = mysqli_query($db,	"UPDATE companyadmin SET Status = 0 WHERE CAdminID = '$cAdminID'") or die("Select Error");
			$_SESSION['message'] = "Company Admin ". $_POST['fname']. ' '. $_POST['lname']. " status set to Suspended.";
		}
		else if($status == 0){
			$result = mysqli_query($db,	"UPDATE companyadmin SET Status = 1 WHERE CAdminID = '$cAdminID'") or die("Select Error");
			$_SESSION['message'] = "Company Admin ". $_POST['fname']. ' '. $_POST['lname']. " status set to Active.";
		}
	}

	if (isset($_POST['resetPassword']) == 'yes') {

		$cAdminID = $_POST['cAdminID'];
		$currentDateTime = date('YmdHis');
		//$currentDateTime = date('Y-m-d H-i-s');
		//echo $currentDateTime;
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"UPDATE companyadmin SET Password = '$currentDateTime' WHERE CAdminID = '$cAdminID' ") or die("Select Error");

		$_SESSION['pwmessage'] = "yes";
		//$abc = "new password is: " . $currentDateTime;
		$_SESSION['pwcontent'] =  $currentDateTime;
		header('Location: superadmin_manageCAdmin_view_delete.php');
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh;">
        
        <!-- Left Section (Navigation) -->
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
  			<h2>Manage Company Admins</h2>

			<?php     
				echo $_SESSION['message'];

				$view = new userAccount();
						$qres = $view->viewCAdmin();
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>First Name</th>
												<th>Last Name</th>
												<th>Company Name</th>
												<th>Email</th>
												<th>Status</th>
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['FirstName'] . "</td>" 
						."<td>" . $Row['LastName'] . "</td>"
						."<td>" . $Row['CompanyName'] . "</td>" 
						."<td>" . $Row['Email'] . "</td>";
						
						if ($Row['Status'] == '1') 
						$accountsTable.= "<td>Active</td>";
						else $accountsTable.= "<td>Suspended</td>";
					
						
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
							<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
							<input type='hidden' name='status' value='" . $Row['Status'] . "'/>
							<input type='submit' name='activateSuspend' value='Activate/Suspend'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
							<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
							<input type='hidden' name='delete' value='yes'/>
							<input type='button' value='Delete' onclick='confirmDiag(event, this.form);'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='resetPassword' value='yes'/>
							<input type='button' value='Reset Password' onclick='confirmDiag2(event, this.form);'>
							</form></td>";
						$accountsTable.= "</tr>";
					}
					
					//last row, blank row
					$accountsTable.= "<td> - </td>" 
					."<td> - </td>" 
					."<td> - </td>" 
					."<td> - </td>" 
					."<td> - </td>" 
					."<td> - </td>" 
					."<td> - </td>"
					."<td> - </td>" ;
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					// if password has been reset, show message.
					if ($_SESSION['pwmessage'] == 'yes') {
						$pwcontent = $_SESSION['pwcontent'];
						echo"<script>alert('New Password is: ' + $pwcontent);</script>";
						$msg = 'password';
						mail("steveqks@gmail.com","My subject",$msg);
					}

					$_SESSION['pwmessage'] = 'no';
					$_SESSION['pwcontent'] = '';

			?>
        </div>
    </div>
			<script>
				function confirmDiag(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Company Admin?");
					if (result)
					{
						form.submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				
				function confirmDiag2(event, form){
					console.log('confirmDiag2() executing');
					let result = confirm("Confirm Reset Password?");
					if (result)
					{
						form.submit();
						console.log('result = pos');
						alertpw('abc');
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				
				function alertpw(stringx){
					alert('new password is ' + stringx);
				}
			</script>
</body>
</html>



