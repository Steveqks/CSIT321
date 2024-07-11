<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	if(isset($_POST['delete'])=='yes')
	{
		$calendarID = $_POST['calendarID'];
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"DELETE FROM calendar WHERE CalendarID = '$calendarID' ") or die("Select Error");
		
		$_SESSION['message1'] = "Calendar entry for " .$_POST['date']. ", ". $_POST['dateName'] ." deleted successfully";
		header('Location: companyadmin_ManageCalendar_view.php');
		exit;
	}

	if (isset($_POST['editAccount'])) 
	{
		$_SESSION['date'] = $_POST['date'];
		$_SESSION['dateName'] = $_POST['dateName'];
		$_SESSION['calendarID'] = $_POST['calendarID'];
		
		header('Location: companyadmin_ManageCalendar_view_edit.php');
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh;">
        
        <!-- Left Section (Navigation) -->
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
					<h2>View Calendar Entries</h2>

  
			<?php     
				echo $_SESSION['message1'];

			
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				
				mysqli_query($db, "SET @row_number = 0;") or die("Error setting row number");

				$result = mysqli_query($db,	"SELECT 
												@row_number := @row_number + 1 AS SN,
												calendar.*
											FROM 
												calendar
											WHERE 
												CompanyID = '$companyID';
											") or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
										<th>S/n</th>
										<th>Date Name</th>
										<th>Date</th>
										</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['SN'] . "</td>" 
					."<td>" . $Row['DateName'] . "</td>" 
					."<td>" . $Row['Date'] . "</td>";
					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='calendarID' value='" . $Row['CalendarID'] . "'/>
						<input type='hidden' name='dateName' value='" . $Row['DateName'] . "'/>
						<input type='hidden' name='date' value='" . $Row['Date'] . "'/>
						<input type='submit' name='editAccount' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='calendarID' value='" . $Row['CalendarID'] . "'/>
						<input type='hidden' name='dateName' value='" . $Row['DateName'] . "'/>
						<input type='hidden' name='date' value='" . $Row['Date'] . "'/>
						<input type='hidden' name='delete' value='yes'/>
						<input type='button' value='Delete' onclick='confirmDiag(this.form)'>
						</form></td>";
						
					$accountsTable.= "</tr>";
				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Entry?");
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


