<?php
session_start();

	$_SESSION['message'] ='';

	if (isset($_POST['editPlan'])) {
		$_SESSION['PlanID'] = $_POST['PlanID'];
		header('Location: superadmin_SubscriptionPlans_view_edit.php');
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh">
        
        <!-- Left Section (Navigation) -->
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
						
			<h2>View Subscription Plans</h2>

  
			<?php     
					echo $_SESSION['message'];
		
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
					$qres = mysqli_query($db,	"SELECT * FROM plans ") or die("Select Error");
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>Plan Name</th>
												<th>Price</th>
												<th>User Access</th>
												<th>Customer Support</th>
											</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['PlanName'] . "</td>" 
						."<td>" . $Row['Price'] . "</td>" 
						."<td>" . $Row['UserAccess'] . "</td>" 
						."<td>" . $Row['CustomerSupport'] . "</td>" ;
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='PlanID' value='" . $Row['PlanID'] . "'/>
							<input type='submit' name='editPlan' value='Edit'>
							</form></td>";
						
						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					


			?>
        </div>
    </div>

</body>
</html>


