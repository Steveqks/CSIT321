<!-- PHP CODE HERE -->
<?php

	//PHPMailer Stuff Here
	
	//Import PHPMailer classes into the global namespace
	//These must be at the top of your script, not inside a function
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	//required files
	require 'phpmailer/src/Exception.php';
	require 'phpmailer/src/PHPMailer.php';
	require 'phpmailer/src/SMTP.php';
	
	function sendEmail($email, $firstname)
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
			$mail->addReplyTo($email, $firstname); // Reply to sender email

			// Content
			$mail->isHTML(true);               // Set email format to HTML
			$mail->Subject = "Credentials Successfully Submitted!";   // Email subject headings
			$mail->Body    = "Dear $firstname, your credentials have been successfully submitted!"; // Email message

			// Success sent message alert
			$mail->send();
			echo "<script>alert('Message was sent successfully!'); </script>";
		} catch (Exception $e) {
			echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); </script>";
		}
	}
	
	//Other Session Related Stuff here
	session_start();

	//connect to the database
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	
	
	//Insert User credentials
	if(isset($_POST['submit']))
	{
		
		//Retrieve credentials from Form
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$companyuen = $_POST['companyuen'];
		$companyname = $_POST['companyname'];
		$planschoice = $_POST['plans'];
		
		//Check if Email Exists in unregistered users account
		$verify_query = mysqli_query($db, "SELECT email FROM unregisteredusers where Email = '$email'");
		
		
		if(mysqli_num_rows($verify_query) != 0)
		{
			echo "<div class='message'>
				  <p>This email is used, Try another One Please!</p>
			  </div> <br>";
		}
		else
		{
			//Check for 9 character UEN
			if(strlen($companyuen) == 9)
			{
				$charregex = '/^(?=.*\d{8})(?=.*[A-Z])[A-Z\d]{9}$/';
				if(preg_match($charregex, $companyuen))
				{
					//Upload Query
					mysqli_query($db,"INSERT INTO unregisteredusers(Email,Password,CompanyName,CompanyUEN,FirstName,LastName,PlanID) VALUES('$email','$password','$companyname','$companyuen','$firstname','$lastname','$planschoice')") or die("Error Occured");
					
					//Attempt to send email
					if(sendEmail($email, $firstname))
					{
						echo "<div class='message'>
							<p>Credentials entered successfully! Check Your inbox for confirmation email!</p>
						</div> <br>";
					}
					else
					{
						echo "<div class='message'>
							<p>Unable to send email :(</p>
						</div> <br>";
					}
				}
				else
				{
					echo "<div class='message'>
						<p>Please Enter a 9 digit UEN of the format 8 digits from 0 -> 9 and 1 uppercase letter!</p>
					</div> <br>";
				}
			}
			//Check for 10 character UEN
			else if(strlen($companyuen) == 10)
			{
				$charregex = '/\b(?:19\d{2}|20(?:[0-8]\d|9[0-9]))[1-9]{5}[A-Z]\b/';
				if(preg_match($charregex, $companyuen))
				{
					//Upload data to database
					mysqli_query($db,"INSERT INTO unregisteredusers(Email,Password,CompanyName,CompanyUEN,FirstName,LastName,PlanID) VALUES('$email','$password','$companyname','$companyuen','$firstname','$lastname','$planschoice')") or die("Error Occured");
					
					//Attempt to send email
					if(sendEmail($email, $firstname))
					{
						echo "<div class='message'>
							<p>Credentials entered successfully! Check Your inbox for confirmation email!</p>
						</div> <br>";
					}
					else
					{
						echo "<div class='message'>
							<p>Unable to send email :(</p>
						</div> <br>";
					}
				}
				else
				{
					echo "<div class='message'>
						<p>Please Enter a 10 digit UEN of the format 4 digits for year,  5digits from 0 -> 9 and 1 uppercase letter!</p>
					</div> <br>";
				}
			}
			
			//Throw Error if UEN Doesnt match
			else
			{
				echo "<div class='message'>
						  <p>Invalid UEN Length, Please enter a UEN which is 9 or 10 characters!</p>
					  </div> <br>";
			}
		}
		
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel = "stylesheet" href="EnterCredentials.css">
</head>
<body>
   <nav class="navbar">
            <div class="navdiv">
                <div class="logo"><a href="HomePage.php"><img id = "teamlogo" accesskey=""src = "Images/tms.png"></a></div>
			     <ul>
				    <li><a href="AboutUs.php">About Us</a></li>
				    <li><a href="Pricing.php">Pricing</a></li>
                    <button class = "LoginBtn"><a href="LoginPage.php">Log In</a></button>
			     </ul>
            </div>
    </nav>
    <div class = "grid-container">
        <div class="grid-item">
               <div id = "RegisterForm">
                    <h2>Enter Credentials</h2>
					<form action = "" method = "post">
						<input id = "firstname" name = "firstname" type = "text" placeholder = "First Name" required>
						<input id = "lastname" name = "lastname" type = "text" placeholder = "Last Name" required>
						<input id = "email" name = "email" type = "email" placeholder = "Email Address" required>
						<input id = "password" name = "password" type = "password" placeholder = "Password" required>
						<input id = "companyname" name = "companyname" type = "text" placeholder = "Company Name" required>
						<input id = "companyuen" name = "companyuen" type = "text" placeholder = "Company UEN(Unique Entity Number)" required>
						<select id = "planschoice" name = "plans" required>
							<option value = "1">Tier 1($9.99/month)</option>
							<option value = "2">Tier 2($29.99/month)</option>
							<option value = "3">Tier 3($59.99/month)</option>
						</select>
						<button id = "submitBtn" name = "submit">Register Now</button>
					</form>
                </div>
            </div>
        <div class="grid-item">
            <img id = "group" src = "Images/group.png">
        </div>
    </div>
    <footer>
        
    </footer>
</body>
</html>