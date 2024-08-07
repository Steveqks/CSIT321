<?php
	//array is empty 
	if (!isset($_SESSION['cadminID']) || empty($_SESSION['cadminID']) || !isset($_SESSION['companyID']) || empty($_SESSION['companyID']) || $_SESSION['Role'] != 'Company Admin')
	{
		// Initialize the session.
		// If you are using session_name("something"), don't forget it now!
		session_start();

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		// Finally, destroy the session.
		session_destroy();
		header("Location:../Session/illegal_access.php"); 
		exit;
	} 
	else {
		
		$cadminID = $_SESSION['cadminID'];
		$companyID = $_SESSION['companyID'];
		
		//check company status
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$sql = "SELECT * FROM company WHERE Status = '1' AND CompanyID = $companyID";
		$qres = mysqli_query($db, $sql); 
		
		$num_rows=mysqli_num_rows($qres);
				
		// company not suspended
		if($num_rows == 1){
			//check for CAdmin status
			$sql = "SELECT * FROM companyadmin WHERE Status = '1' AND CAdminID = $cadminID";
			$qres = mysqli_query($db, $sql); 
			
			$num_rows=mysqli_num_rows($qres);
					
				// suspended companyadmin
				if($num_rows == 0)
				{
					// Initialize the session.
					// If you are using session_name("something"), don't forget it now!
					session_start();

					// Unset all of the session variables.
					$_SESSION = array();

					// If it's desired to kill the session, also delete the session cookie.
					// Note: This will destroy the session, and not just the session data!
					if (ini_get("session.use_cookies")) {
						$params = session_get_cookie_params();
						setcookie(session_name(), '', time() - 42000,
							$params["path"], $params["domain"],
							$params["secure"], $params["httponly"]
						);
					}

					// Finally, destroy the session.
					session_destroy();
					header("Location:../Session/suspended_companyadmin.php"); 
					exit;
				}
		}
		else 	//suspended company
		{
			// Initialize the session.
			// If you are using session_name("something"), don't forget it now!
			session_start();

			// Unset all of the session variables.
			$_SESSION = array();

			// If it's desired to kill the session, also delete the session cookie.
			// Note: This will destroy the session, and not just the session data!
			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					$params["path"], $params["domain"],
					$params["secure"], $params["httponly"]
				);
			}

			// Finally, destroy the session.
			session_destroy();
			header("Location:../Session/suspended_company.php"); 
			exit;
		}
		if ($_SESSION['cadminID'] == NULL) echo "no id";
	}

	
?>