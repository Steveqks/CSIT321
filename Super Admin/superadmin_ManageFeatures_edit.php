<?php
session_start();
	include '../Session/session_check_superadmin.php';

	include 'db_connection.php';

	$_SESSION['message1'] ='';

	if(isset($_POST['newFeatureID'])){
		$newFeatureID = $_POST['newFeatureID'];			
		$oldFeatureID = $_POST['oldFeatureID'];			
		$name = $_POST['name'];
		$description = $_POST['description'];
		$image = $_POST['image'];
		$icon = $_POST['icon'];
		
		//check if featureID is changed
		//is changed
		if($newFeatureID != $oldFeatureID)
		{
			//check if featureid exists.
			$result = mysqli_query($db,	"SELECT FeatureID FROM features WHERE FeatureID = '$newFeatureID'") or die("Select Error");

			$num_rows=mysqli_num_rows($result);
			// dont exists
			if($num_rows == 0){
				$result2 = mysqli_query($db,"UPDATE features SET FeatureID = '$newFeatureID', Name = '$name', Description = '$description', Image = '$image', Icon = '$icon'  WHERE FeatureID = '$oldFeatureID' ") or die("update Error");
				$_SESSION['message1'] = "<p >Feature id updated</p>";
				$_SESSION['FeatureID'] = $newFeatureID;
			}
			else{
				$_SESSION['message1'] = "S/N already exists";
			}
		}
		else 
		{
				$result2 = mysqli_query($db,"UPDATE features SET Name = '$name', Description = '$description', Image = '$image', Icon = '$icon' WHERE FeatureID = '$newFeatureID' ") or die("update Error");
							
				$_SESSION['message1'] = "<p >Feature updated</p>";
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh">
        
        <!-- Left Section (Navigation) -->
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
						
			<h2>Edit Features Page</h2>

  
			<?php     
					
					$FeatureID = $_SESSION['FeatureID'];
		
					$result = mysqli_query($db,	"SELECT * FROM features WHERE FeatureID = '$FeatureID'") or die("Select Error");
				
					$form ='';
					while($Row = $result->fetch_assoc()){
					$form = "<form  action='' id='ModifyFeature'  method='POST' style='
																				flex: 0 0 48%;
																				display: inline-flex;
																				justify-content: space-between;
																				padding: 8px;
																				border: 1px solid #ddd;
																				border-radius: 4px;
																				box-sizing: border-box;
																				width: 80%;
																				margin-bottom: 15px;
																				margin-bottom: 5px;
																				display: flex;
																				flex-direction: column;
																				margin-bottom: 15px;
																				background-color: #f0f0f0;
																				padding: 20px;
																				border-radius: 5px;
																				max-width: 600px;
																				display: flex;
																				flex-direction: column;
																					'>
							<br>
								<table >
									<tr>
										<td>
											S/N: <input type='text' name='newFeatureID' value='" . $Row['FeatureID'] . "' ><br>
											Name: <input type='text' name='name' value='" . $Row['Name'] . "' > <br>
											Description:  <input type='text' name='description' value='" . $Row['Description'] . "' > 
										</td>
											<td>
											Icon: <input type='text' name='icon' value='" . $Row['Icon'] . "' > <br>
											Image: <input type='text' name='image' value='" . $Row['Image'] . "' > <br>
											<input type='hidden' name='oldFeatureID' value='" . $FeatureID . "' > <br>
											</td>
									</tr>
								</table>
								<input type='button' value='Save Changes' onclick='confirmDiag();' style='horizontal-align: right; width: 20%;'>
							</form>
								";
					}
					echo $form;
					
					echo $_SESSION['message1'];


			?>
        </div>
    </div>

</body>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Submit Changes?");
					if (result)
					{
						document.getElementById('ModifyFeature').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</html>


