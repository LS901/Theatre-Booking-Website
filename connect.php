<!-- Connection to my mysql database -->
<?php		
		function connect(){
			
			$host = "dragon.ukc.ac.uk";
			$dbname = "ls668";
			$user = "ls668";
			$pwd = "jeranun";
			
			$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pwd);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		}
		
?>