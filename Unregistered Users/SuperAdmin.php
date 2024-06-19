<?php 

	session_start();

	if(isset($_SESSION['Email']))
	{
		echo("{$_SESSION['Email']}"."<br />");;
		echo("{$_SESSION['FirstName']}"."<br />");;
		echo("{$_SESSION['SAdminID']}"."<br />");;
	}
	else
	{
		header("Location:LoginPage.php");
	}







?>