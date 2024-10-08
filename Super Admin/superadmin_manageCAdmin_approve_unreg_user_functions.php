<?php    
include '../Session/session_check_superadmin.php';

include 'db_connection.php';


class userAccount{
	public $conn;
	function __construct($servername, $username, $password, $dbname){
		$this->conn=mysqli_connect($servername,$username,$password);
		if(mysqli_connect_errno()){
			echo "failed to connect";
			mysqli_connect_error();
			exit();
		}
		mysqli_select_db($this->conn,$dbname);
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
	
    public function approveAccount(string $fname, string $companyUEN, string $lname, string $email, string $password, string $cname, string $planID):int{
		//1.1	check company exist
		//1.2	check company admin exists
		//2.1 	create company
		//2.2 	create company admin
				
		// check company exists
		while($this->isCompanyExists($cname)){
			// company don't exist yet
			// check company admin exists
			while ($this->isCompanyAdminExists($email)){
				// company admin don't exist yet
				// proceed with creating company & company admin...	
				$this->createCompany($cname, $companyUEN, $planID);
				$companyID = $this->getCompanyID($cname);
				$this->createCompanyAdmin($companyID,  $fname, $lname, $email, $password);
				
				//create manager specialisation				
				$Sql = "INSERT INTO specialisation (SpecialisationID, SpecialisationName, CompanyID) VALUES (NULL, 'Manager', '$companyID')";
				$qres = mysqli_query($this->conn, $Sql); 
				
				
				//remove company application from list		
				$Sql2 = "DELETE FROM unregisteredusers  WHERE CompanyUEN = '$companyUEN' AND Email = '$email'";
				$qres2 = mysqli_query($this->conn, $Sql2); 

				
				return 3; // created company & company admin
			}
			return 2; // error, company admin email already exists 
		}
		return 1; // company already exists
    }
	
	public function isCompanyAdminExists(string $email):bool{
		
		$sql = "SELECT * FROM companyadmin WHERE Email = '$email'";
		$qres = mysqli_query($this->conn, $sql); 

		$num_rows=mysqli_num_rows($qres);
		
		// dont exists
		if($num_rows == 0){
		    return true; 
        }
		// exists
        else{
            return false; 
        }
	}
	
	public function isCompanyExists(string $cname):bool{
		
		$sql = "SELECT * FROM company WHERE CompanyName = '$cname'";
		$qres = mysqli_query($this->conn, $sql); 

		$num_rows=mysqli_num_rows($qres);
		
		// dont exists
		if($num_rows == 0){
		    return true; 
        }
		// exists
        else{
            return false; 
        }
	}
	
	public function createCompany(string $cname, string $companyUEN, string $planID):bool{
		$sql = "INSERT INTO company (CompanyID, CompanyName, CompanyUEN, PlanID, Status) VALUES (NULL, '$cname', '$companyUEN', '$planID', 1);";
		$qres = mysqli_query($this->conn, $sql); 
		if($qres === false){
		    return false; 
        }
        else{
            return true; 
        }
	}
	
	public function createCompanyAdmin(string $companyID, string $fname, string $lname, string $email, string $password):bool{
		$sql = "INSERT INTO companyadmin (CAdminID, CompanyID, FirstName, LastName, Email, Password, Status) VALUES (NULL, '$companyID', '$fname', '$lname', '$email', '$password', '1');";
		$qres = mysqli_query($this->conn, $sql); 
		if($qres === false){
		    return false; 
        }
        else{
            return true; 
        }
	}
	
	public function getCompanyID(string $cname):string{
		$sql = "SELECT * FROM company WHERE CompanyName = '$cname'";
		$qres = mysqli_query($this->conn, $sql); 
		$row = $qres->fetch_assoc();
			
		return $row['CompanyID'];
	}
	
}
?>