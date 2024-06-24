<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel = "stylesheet" href="HomePage.css">
</head>
<body>
   <!-- PHP CODE HERE -->
   <?php
   
		session_start();
   	
		//connect to the database
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		
		
		//Insert User credentials
		if(isset($_POST['submit']))
		{
			
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$companyuen = $_POST['companyuen'];
			$companyname = $_POST['companyname'];
			$planschoice = $_POST['plans'];
			
			$verify_query = mysqli_query($db, "SELECT email FROM unregisteredusers where Email = '$email'");
			
			//Regex validation for UEN:
			if(strlen($companyuen) == 9)
			{
				//Check for 9 character UEN
				if(mysqli_num_rows($verify_query) != 0)
				{
					echo "<div class='message'>
						  <p>This email is used, Try another One Please!</p>
					  </div> <br>";
				}
				else
				{
				mysqli_query($db,"INSERT INTO unregisteredusers(Email,Password,UEN,CompanyName,FirstName,LastName,PlanID) VALUES('$email','$password','$companyuen','$companyname','$firstname','$lastname','$planschoice')") or die("Error Occured");

					echo "<div class='message'>
						  <p>Credentials entered successfully!</p>
					  </div> <br>";
				}
			}
			else if(strlen($companyuen) == 10)
			{
				//Check for 10 character UEN
				//Check for 9 character UEN
				if(mysqli_num_rows($verify_query) != 0)
				{
					echo "<div class='message'>
						  <p>This email is used, Try another One Please!</p>
					  </div> <br>";
				}
				else
				{
				mysqli_query($db,"INSERT INTO unregisteredusers(Email,Password,UEN,CompanyName,FirstName,LastName,PlanID) VALUES('$email','$password','$companyuen','$companyname','$firstname','$lastname','$planschoice')") or die("Error Occured");

					echo "<div class='message'>
						  <p>Credentials entered successfully!</p>
					  </div> <br>";
				}
			}
			else
			{
				echo "<div class='message'>
						  <p>Invalid UEN Length, Please enter a UEN which is 9 or 10 characters!</p>
					  </div> <br>";
			}
			
		}
		
    
    
    
   ?>
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
                    <h2>Empower Your Team, Simplify Your Schedule</h2>
					<form action = "" method = "post">
						<input id = "firstname" name = "firstname" type = "text" placeholder = "First Name">
						<input id = "lastname" name = "lastname" type = "text" placeholder = "Last Name">
						<input id = "email" name = "email" type = "text" placeholder = "Email Address">
						<input id = "password" name = "password" type = "password" placeholder = "Password">
						<input id = "companyuen" name = "companyuen" type = "text" placeholder = "Company UEN(Unique Entity Number)">
						<input id = "companyname" name = "companyname" type = "text" placeholder = "Company Name">
						<select id = "planschoice" name = "plans">
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