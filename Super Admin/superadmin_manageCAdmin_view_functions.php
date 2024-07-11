<?php    

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
	
	public function createCompanyAdmin(string $companyID, string $fname, string $lname, string $email, string $password):bool{
		$sql = "INSERT INTO companyadmin (CAdminID, CompanyID, FirstName, LastName, Email, Password) VALUES (NULL, '$companyID', '$fname', '$lname', '$email', '$password');";
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

	public function viewCAdmin():bool|mysqli_result{
	    $Sql = "SELECT 
					companyadmin.*, 
					company.CompanyName
				FROM 
					companyadmin
				JOIN 
					company 
				ON 
					companyadmin.CompanyID = company.CompanyID;
				";
        $qres = mysqli_query($this->conn, $Sql); 
        if($qres === false){
            return false; 
        }
        else{
            return $qres; 
        }	
	}
}
?>