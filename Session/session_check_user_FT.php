<?php
	if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID']) || !isset($_SESSION['CompanyID']) || empty($_SESSION['CompanyID'] || $_SESSION['Role'] != 'FT')) 
	{
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
	else 
	{
		$UserID = $_SESSION['UserID'];
		$CompanyID = $_SESSION['CompanyID'];
		
		//check company status
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$sql = "SELECT * FROM company WHERE Status = '1' AND CompanyID = $CompanyID";
		$qres = mysqli_query($db, $sql); 
		
		$num_rows=mysqli_num_rows($qres);
				
		// company not suspended
		if($num_rows == 1){
			//check for user status
			$sql = "SELECT * FROM existinguser WHERE Status = '1' AND UserID = $UserID";
			$qres = mysqli_query($db, $sql); 
			
			$num_rows=mysqli_num_rows($qres);
					
				// suspended user
				if($num_rows == 0)
				{
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
					header("Location:../Session/suspended_user.php"); 
					exit;
				}
		}
		else 	// suspended company
		{
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
	}
?>