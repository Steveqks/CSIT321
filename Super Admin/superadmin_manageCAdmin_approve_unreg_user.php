<?php
session_start();
	
	include_once('superadmin_manageCAdmin_approve_unreg_user_functions.php');
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	//required files
	require '../Unregistered Users/phpmailer/src/Exception.php';
	require '../Unregistered Users/phpmailer/src/PHPMailer.php';
	require '../Unregistered Users/phpmailer/src/SMTP.php';
	
	$_SESSION['message'] = '';
	
	if(isset($_POST['approve']) == 'yes')
	{
		$email = $_POST['email'];
		$fname = $_POST['fname'];
		$cname = $_POST['cname'];
		
		$aprrove = new userAccount();

		switch ($aprrove->approveAccount($_POST['fname'], $_POST['companyUEN'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['cname'], $_POST['planID'])){
			//company exists
			case 1 : $_SESSION['message'] = "company already exists in system"; 
			break;
			
			//company admin exists
			case 2 : $_SESSION['message'] = "company admin email already exists in system"; 
			break;
			
			//create both
			case 3 : $_SESSION['message'] = "company and company admin created."; 
			sendEmail($email, $fname, $cname);
			$_SESSION['approvemsg'] = "yes";

			break;
			
			default : $_SESSION['message'] = "nothing happened"; 
			break;
			
		}

	}

	if(isset($_POST['delete']) == 'yes')
	{
		$applicationID = $_POST['applicationID'];

		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"DELETE FROM unregisteredusers  WHERE ApplicationID = '$applicationID' ") or die("Select Error");
		
		$_SESSION['message'] = "Application  for \"" .$_POST['cname']. "\" deleted successfully";
	}
	
	function sendEmail($email, $fname, $cname)
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
			$mail->Subject = "TrackMySchedule Application Approved";   // Email subject headings
			$mail->Body    = "Good day " . $fname . " from " . $cname . ", <br><br>Your application has been approved, <br>you may login TrackMySchedule app using your registered email and password."; // Email message

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

			<h2>Approve Company</h2>

				<?php   
						echo $_SESSION['message'];

						$view = new userAccount();
						$qres = $view->viewAccount();
						
						if($qres){
							$accountsTable = "<table border = 1 class='center'>";
							$accountsTable .= "	<tr>
													<th>Email</th>
													<th>First Name</th>
													<th>Last Name</th>
													<th>Plan ID</th>
													<th>Company Name</th>
													<th>Company UEN</th>
													</tr>\n";
							$accountsTable .= "<br/>";
							}
						while ($Row = $qres->fetch_assoc()) {
							$accountsTable.= "<tr>\n";
							$accountsTable .= "<td>" . $Row['Email'] . "</td>";
							$accountsTable .= "<td>" . $Row['FirstName'] . "</td>";
							$accountsTable .= "<td>" . $Row['LastName'] . "</td>";
							$accountsTable .= "<td>" . $Row['PlanID'] . "</td>";
							$accountsTable .= "<td>" . $Row['CompanyName'] . "</td>";
							$accountsTable .= "<td>" . $Row['CompanyUEN'] . "</td>";
						
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='approveID' value='" . $Row['ApplicationID'] . "'/>
								<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
								<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
								<input type='hidden' name='email' value='" . $Row['Email'] . "'/>
								<input type='hidden' name='password' value='" . $Row['Password'] . "'/>
								<input type='hidden' name='cname' value='" . $Row['CompanyName'] . "'/>
								<input type='hidden' name='companyUEN' value='" . $Row['CompanyUEN'] . "'/>
								<input type='hidden' name='planID' value='" . $Row['PlanID'] . "'/>
								<input type='hidden' name='approve' value='yes'/>
								<input type='button' value='Approve' onclick='confirmDiag2(event, this.form);'>
								</form></td>";
								
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='applicationID' value='" . $Row['ApplicationID'] . "'/>
								<input type='hidden' name='cname' value='" . $Row['CompanyName'] . "'/>
								<input type='hidden' name='delete' value='yes'/>
								<input type='button' value='Delete' onclick='confirmDiag(event, this.form);'>
								</form></td>";
							$accountsTable.= "</tr>";
						}
						$accountsTable.= "</table>";
						echo $accountsTable;
						
					// show alert
					if (@$_SESSION['approvemsg'] == 'yes') {
						echo"<script>alert('Application approved email sent to Applicant.');</script>";
					}
					
					$_SESSION['approvemsg'] = 'no';
				?>
        </div>
    </div>
			<script>
				function confirmDiag(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Company Entry?");
					if (result)
					{
						form.submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
				
				function confirmDiag2(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Approve Company?");
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


