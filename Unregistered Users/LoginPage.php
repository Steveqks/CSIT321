<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel = "stylesheet" href="LoginPage.css">
</head>
<body>
   <?php
		//connect to the database
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		
		session_start();
		
		if(isset($_POST['submit']))
		{		
			$email = $_POST['email'];
			$password = $_POST['password'];

			$result = mysqli_query($db,"SELECT * FROM existinguser WHERE Email='$email' AND Password='$password' ") or die("Select Error");
			$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
			$count = mysqli_num_rows($result);
			
				
			if($count == 1)
			{
				//Session Related to Exisitng user
				$_SESSION['UserID'] = $row['UserID'];
				$_SESSION['Email'] = $row['Email'];
				$_SESSION['FirstName'] = $row['FirstName'];
				$_SESSION['Role']= $row['Role'];
				
				if($row['Role'] == "FT")
				{
					//Route the user based on the role FT, PT, Manager
					header("Location:../Existing Users/FT_Homepage.php");
				}
				if($row['Role'] == "PT")
				{
					//Route the user based on the role FT, PT, Manager
					header("Location:PartTimers.php");
				}
				if($row['Role'] == "Manager")
				{
					//Route the user based on the role FT, PT, Manager
					header("Location:Managers.php");
				}
				
			}
			else
			{
				$sql1 = "SELECT * FROM companyadmin WHERE Email='$email' AND Password='$password'";
				//Check for Company Admin
				$result1 = mysqli_query($db,$sql1);
				$row1 = mysqli_fetch_array($result1,MYSQLI_ASSOC);
				$count1 = mysqli_num_rows($result1);
				if($count1 == 1)
				{
					//Session Related to Company Admin
					$_SESSION['cadminID'] = $row1['CAdminID'];
					$_SESSION['companyID'] = $row1['CompanyID'];
					$_SESSION['Email'] = $row1['Email'];
					$_SESSION['FirstName'] = $row1['FirstName'];
					
					echo "<script> alert('Login For Company Admin Successful')</script>";
					//Route to Company Admin HomePage Here
					header("Location:../Company Admin/companyadmin_homepage.php");
				}
				else
				{
					//Check for Company Admin
					$result2 = mysqli_query($db,"SELECT * FROM superadmin WHERE Email='$email' AND Password='$password' ") or die("Select Error");
					$row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);
					$count2 = mysqli_num_rows($result2);
					
					if($count2 == 1)
					{
						//Session Related to Super Admin
						$_SESSION['SAdminID'] = $row2['SAdminID'];
						$_SESSION['Email'] = $row2['Email'];
						$_SESSION['FirstName'] = $row2['FirstName'];
						
						//Route to Super Admin HomePage Here
						header("Location:../Super Admin/superadmin_homepage.php");
					}
					else
					{
						echo "<script> alert('invalid username/password')</script>";
					}						
				}
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
               <div id = "LoginForm">
					<form action = "", method = "post">
						<h2>Login for Existing Users</h2>
						<input id = "email" name = "email" type = "text" placeholder = "Email Address" required>
						<input id = "password" name = "password" type = "password" placeholder = "Password" required>  
						<button id = "submitBtn" name = "submit">Login</button>
					</form>
                </div>
            </div>
        <div class="grid-item">
            <img id = "group" src = "Images/group.png">
        </div>
    </div>
 
</body>
</html>