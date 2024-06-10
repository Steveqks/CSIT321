<?php    
class viewAccountController{
	public function viewAccount():bool|mysqli_result
	{
		$viewacc = new userAccount();
		$qres = $viewacc->viewAccount();
		
		if($qres === false){
			return false; 
		}
		else{
			return $qres; 
		}
	}
}

class approveAccountController{
	public function approveAccount():bool
	{
		$approve = new userAccount();
		if($approve->approveAccount($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['cname'], $_POST['planID'] )){
			return true;
		}
		else{
			return false;
		}
	}
}

class userAccount{
	public $conn;
	function __construct(){
		$this->conn=mysqli_connect("localhost","root","");
		if(mysqli_connect_errno()){
			echo "failed to connect";
			mysqli_connect_error();
			exit();
		}
		mysqli_select_db($this->conn,"TMS");
	}
	
	public function viewAccount():bool|mysqli_result{
        $Sql = "SELECT * FROM unregisteredusers";
        $qres = mysqli_query($this->conn, $Sql); 
        if($qres === false){
            return false; 
        }
        else{
            return $qres; 
        }
    }
	
    public function approveAccount(string $fname, string $lname, string $email, string $cname, string $PlanID):bool{
		//1. 	check company
		//1.1 	create company
		//2. 	check company admin
		//2.1 	create company admin
				
		while($this->notCompanyExists($cname)){
			while($this->CreateCompany($cname)){
				return true;
			}
			return false;
			;
		}
		
		
		
		return false;
    }
	
	public function isIDExists(string $id):bool{
		$sql = "SELECT * FROM existinguser WHERE UserID = $id";
		$qres = mysqli_query($this->conn, $sql); 
		
		echo "checking id..".$id."...";
		
		// don't exists
		if($qres === false){
			echo "id don't exists...";
		    return false; 
        }
		// exists
        else{
			echo "id exists...";
            return true; 
        }
	}
	
	public function notCompanyExists(string $cname):bool{
		
		$sql = "SELECT * FROM company WHERE CompanyName = '$cname'";
		$qres = mysqli_query($this->conn, $sql); 

		$num_rows=mysqli_num_rows($qres);
		
		// exists
		if($num_rows>0){
		    return false; 
        }
		// don't exists
        else{
            return true; 
        }
	}
	
	public function CreateCompany(string $cname):bool{
		$sql = "INSERT INTO company (CompanyID, CompanyName, PlanID, Status) VALUES (NULL, '$cname', 1, 1);";
		$qres = mysqli_query($this->conn, $sql); 
		if($qres === false){
		    return false; 
        }
        else{
            return true; 
        }
	}
	
	public function createCompanyAdmin(string $id):bool{
		$sql = "SELECT * FROM existinguser WHERE User.ID = $id";
		$qres = mysqli_query($this->conn, $sql); 
		if($qres === false){
		    return false; 
        }
        else{
            return true; 
        }
	}
}
?>