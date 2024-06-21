<?php 
	$id = $_POST['id'];


	//connect to the database
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	
	$sql = "DELETE FROM leaves WHERE LeaveID = $id";
	
	if(mysqli_query($db, $sql))
	{
			mysqli_close($db);
			header('Location: FT_CancelLeaves.php');
			exit;
	}
	else{
		echo "Error Deleting Record";
	}
?>