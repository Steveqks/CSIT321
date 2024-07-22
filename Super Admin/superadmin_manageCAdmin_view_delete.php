<?php
session_start();

	include '../Session/session_check_superadmin.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	//required files
	require '../Unregistered Users/phpmailer/src/Exception.php';
	require '../Unregistered Users/phpmailer/src/PHPMailer.php';
	require '../Unregistered Users/phpmailer/src/SMTP.php';


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
		$email = $_POST['email'];
		$currentDateTime = date('YmdHis');
		//$currentDateTime = date('Y-m-d H-i-s');
		//echo $currentDateTime;
		
		sendEmail($email, $currentDateTime);
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"UPDATE companyadmin SET Password = '$currentDateTime' WHERE CAdminID = '$cAdminID' ") or die("Select Error");

		$_SESSION['pwmessage'] = "yes";
	}
	
	function sendEmail($email , $currentDateTime)
	{
		// New PHPMailer instance
		$mail = new PHPMailer(true);
		
		try {
			// Server settings
			$mail->isSMTP();                            // Send using SMTP
			$mail->Host       = 'smtp.gmail.com';       // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                   // Enable SMTP authentication
			$mail->Username   = 'TrackMySchedule@gmail.com';   // SMTP email
			$mail->Password   = 'bovpwkukeknivlgu';      // SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
			$mail->Port       = 587;                    // TCP port to connect to

			// Disable SSL certificate verification
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);

			// Recipients
			$mail->setFrom('TrackMySchedule@gmail.com', 'TMS Admin'); // Sender Email and name
			$mail->addAddress($email);     // Add a recipient email  
			$mail->addReplyTo($email, 'TrackMySchedule User'); // Reply to sender email

			// Content
			$mail->isHTML(true);               // Set email format to HTML
			$mail->Subject = "Password has Successfully been Reset!";   // Email subject headings
			$mail->Body    = "Good day TrackMySchedule User, your password have been successfully reset, your new password is \"" . $currentDateTime . "\"" . "."; // Email message

			// Success sent message alert
			$mail->send();
		} catch (Exception $e) {
			echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); </script>";
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
							<input type='hidden' name='email' value='" . $Row['Email'] . "'/>
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

					if (@$_SESSION['pwmessage'] == 'yes') {
						echo"<script>alert('Password has successfully been reset! New Password have been sent via email to user.');</script>";
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
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				

			</script>
</body>
</html>



