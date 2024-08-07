<?php    

	include '../Session/session_check_companyadmin.php';

	include 'db_connection.php';


	class userAccount{
		public $conn;
    	function __construct($servername, $username, $password, $dbname) {
			$this->conn=mysqli_connect($servername,$username,$password);
			if(mysqli_connect_errno()){
				echo "failed to connect";
				mysqli_connect_error();
				exit();
			}
			mysqli_select_db($this->conn,$dbname);
		}
		
		public function approveAccount(string $fname, string $lname, string $email, string $password, string $cname, string $PlanID):int{
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
					
					$this->createCompany($cname);
					$companyID = $this->getCompanyID($cname);
					$this->createCompanyAdmin($companyID, $fname, $lname, $email, $password);
					return 3;
				}
				
				
				//while($this->createCompany($cname)){
				//	while($this->createCompanyAdmin($fname, $lname, $email, $password )){
		
				return 2;
			}
			
			
			
			return 1; //exists
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
		
		public function createCompany(string $cname):bool{
			$sql = "INSERT INTO company (CompanyID, CompanyName, PlanID, Status) VALUES (NULL, '$cname', 1, 1);";
			$qres = mysqli_query($this->conn, $sql); 
			if($qres === false){
				return false; 
			}
			else{
				return true; 
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
		
		public function deleteSpecialisation(string $specialisationID){
			$sql = "DELETE FROM specialisation WHERE SpecialisationID = '$specialisationID'";
			$qres = mysqli_query($this->conn, $sql); 
		}
		
}
?>