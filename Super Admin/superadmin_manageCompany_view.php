<?php
session_start();

	include '../Session/session_check_superadmin.php';

	include 'db_connection.php';


	$_SESSION['message'] ='';

	if(isset($_POST['delete']) == 'yes')
	{
		$companyID = $_POST['companyID'];
		
		$result = mysqli_query($db,	"DELETE FROM company WHERE CompanyID = '$companyID' ") or die("Select Error3");
		
		$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" deleted successfully";
	}

	if(isset($_POST['activateSuspend']))
	{
		$companyID = $_POST['companyID'];
		$status = $_POST['Status'];
		
		
		if($status == 1){
			$result = mysqli_query($db,	"UPDATE company SET Status = 0 WHERE company.CompanyID = '$companyID'") or die("Select Error");
			$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" status set to Suspended.";
		}
		else if($status == 0){
			$result = mysqli_query($db,	"UPDATE company SET Status = 1 WHERE company.CompanyID = '$companyID'") or die("Select Error");
			$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" status set to Active.";
		}
	}

	if (isset($_POST['editCompany'])) {
		$_SESSION['companyID'] = $_POST['companyID'];
		$_SESSION['companyName'] = $_POST['companyName'];
		$_SESSION['companyUEN'] = $_POST['companyUEN'];
		$_SESSION['planID'] = $_POST['planID'];
		
		header('Location: superadmin_manageCompany_view-edit.php');
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
						
			<h2>View Companies</h2>

  
			<?php     
					echo $_SESSION['message'];

					$qres = mysqli_query($db,	"SELECT * FROM company ORDER BY CompanyName") or die("Select Error");
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>Company Name</th>
												<th>Company UEN</th>
												<th>Subscription Plan</th>
												<th>Status</th>
												
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['CompanyName'] . "</td>" 
						."<td>" . $Row['CompanyUEN'] . "</td>" 
						."<td>" . $Row['PlanID'] . "</td>" ;
						
						if($Row['Status'] == '1')
						$accountsTable.= "<td> Active </td>";
						else $accountsTable.= "<td> Suspended </td>";
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='companyName' value='" . $Row['CompanyName'] . "'/>
							<input type='hidden' name='companyUEN' value='" . $Row['CompanyUEN'] . "'/>
							<input type='hidden' name='planID' value='" . $Row['PlanID'] . "'/>
							<input type='submit' name='editCompany' value='Edit'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='companyName' value='" . $Row['CompanyName'] . "'/>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='Status' value='" . $Row['Status'] . "'/>
							<input type='submit' name='activateSuspend' value='Activate/Suspend'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='companyName' value='" . $Row['CompanyName'] . "'/>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='delete' value='yes'/>
							<input type='button' value='Delete' onclick='confirmDiag(event,  this.form);'>
							</form></td>";
							

						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					


			?>
        </div>
    </div>
			<script>
				function confirmDiag(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Company? All Accounts and activity related to this company will be deleted permanently.");
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


