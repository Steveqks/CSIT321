<?php
session_start();

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	//required files
	require '../Unregistered Users/phpmailer/src/Exception.php';
	require '../Unregistered Users/phpmailer/src/PHPMailer.php';
	require '../Unregistered Users/phpmailer/src/SMTP.php';

	include '../Session/session_check_companyadmin.php';

	include 'db_connection.php';


	$_SESSION['message1'] = '';

	if(isset($_POST['delete'])=='yes')
	{
		$userID = $_POST['userID'];
		$result = mysqli_query($db,	"DELETE FROM existinguser WHERE UserID = '$userID' ") or die("Select Error");
		
		$_SESSION['message1'] = $_POST['fname'] . " ". $_POST['lname'] ." deleted successfully";
	}

	if (isset($_POST['toggleStatus'])) 
	{
		$userID = $_POST['userID'];
		
		if ($_POST['status'] == 0)
		{
			$result = mysqli_query($db,"UPDATE existinguser SET Status = '1' WHERE UserID = '$userID' ") or die("update Error");
			$_SESSION['message1'] = $_POST['fname'] . " ". $_POST['lname'] ." Status set to Active";
		}
		else
		{
			$result = mysqli_query($db,"UPDATE existinguser SET Status = '0' WHERE UserID = '$userID' ") or die("update Error");
			$_SESSION['message1'] = $_POST['fname'] . " ". $_POST['lname'] ." Status set to Suspended";
		}
	}
	
	if (isset($_POST['resetPassword']) == 'yes') {
		$userID = $_POST['userID'];
		$email = $_POST['email'];
		$currentDateTime = date('YmdHis');
		//$currentDateTime = date('Y-m-d H-i-s');
		//echo $currentDateTime;
		
		sendEmail($email, $currentDateTime);
		
		$result = mysqli_query($db,	"UPDATE existinguser SET Password = '$currentDateTime' WHERE UserID = '$userID' ") or die("Select Error");

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
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
					<h2>View User Accounts</h2>

  
			<?php     
				echo $_SESSION['message1'];

			
				$companyID = $_SESSION['companyID'];;

				$result = mysqli_query($db,	"SELECT 
												eu.UserID,
												eu.FirstName,
												eu.LastName,
												eu.Gender,
												eu.Email AS EmailAddress,
												s.SpecialisationName,
												s.SpecialisationID,
												eu.Role,
												eu.Status
											FROM 
												existinguser eu
											JOIN 
												specialisation s ON eu.SpecialisationID = s.SpecialisationID
											WHERE 
												eu.CompanyID = '$companyID'
											ORDER BY
												FirstName;
											") or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Gender</th>
										<th>Email Address</th>
										<th>Specialisation</th>
										<th>Role</th>
										<th>Status</th>
										</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['Gender'] . "</td>"
					."<td>" . $Row['EmailAddress'] . "</td>" 
					."<td>" . $Row['SpecialisationName'] . "</td>" 
					."<td>" . $Row['Role'] . "</td>" ;
					
					if ($Row['Status'] == '1') $accountsTable .=  "<td> Active </td>";
					else $accountsTable .=  "<td> Suspended </td>";
					


					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='status' value='" . $Row['Status'] . "'/>
						<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
						<input type='submit' name='toggleStatus' value='Activate/Suspend'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
						<input type='hidden' name='delete' value='yes'/>
						<input type='button' value='Delete' onclick='confirmDiag(event, this.form)'>
						</form></td>";
						
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='email' value='" . $Row['EmailAddress'] . "'/>
						<input type='hidden' name='resetPassword' value='yes'/>
						<input type='button' value='Reset Password' onclick='confirmDiag2(event, this.form);'>
						</form></td>";
						
					$accountsTable.= "</tr>";
					

				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
				// if password has been reset, show message.

				if (@$_SESSION['pwmessage'] == 'yes') {
					echo"<script>alert('Password has successfully been reset! New Password have been sent via email to user.');</script>";
				}

				$_SESSION['pwmessage'] = 'no';

			?>
        </div>
    </div>
			<script>
				function confirmDiag(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete User? User & all activity related will be deleted permanently, And it will not be recoverable.");
					if (result)
					{
						form.submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				
				function confirmDiag2(event, form){
					console.log('confirmDiag2() executing');
					let result = confirm("Confirm Reset Password? Email will be sent to the user's email address.");
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


