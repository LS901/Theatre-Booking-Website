<?php
// Start the session
session_start();
?>

<!-- Instruction to the web browser that this page contains HTML-->
<!DOCTYPE html>
<html>
	<head>
	
		<!-- Meta tags used to provide machine parsable information -->
		<meta charset="UTF-8">
		<meta name="keywords" content="Theatre Booking System"/>
		<meta name="description" content="Booking confirmation"/>
		<meta name="author" content="Lewis Saunders">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Title of the website -->
		<title>Theatre Booking System</title>
		
		<!-- Linking of stylesheets to provide CSS styling and fonts -->
		<link rel="stylesheet" href="mystyles.css">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans">
	</head>
	<body>
	
		<!-- PHP code that links to 'connect.php' to allow connection to SQL database-->
		<?php require 'connect.php';	
		 try {
			//For loop to create an array of selected seats and set seatSelected to true if any seat is selected.
			$seatArray= [];
			$seatSelected = FALSE;
			for($x = 0; $x < $_SESSION["row_count"]; $x++)				{
				if(isset($_POST["$x"])){ array_push($seatArray,$_POST["rowNumber".$x]); $seatSelected = TRUE;};
			};
		 
			//Server-side validation
			$error = '';
			if(isset($_POST['book'])){
				if(!$seatSelected){
					$error .="No seat has been selected.\\n";
				}
				if(empty($_POST['email'])){
					$error .="Please enter an email address.\\n";
				}else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
					$error .="Not a valid email address.\\n";
				};
				if(!empty($error)){
					echo "<script type='text/javascript'>alert('".$error."'); window.history.back();</script>";
				}else{			
					//If server-side validation passes, connection to database is made and SQL query is executed.		
					$conn = connect();
					foreach($seatArray as $seat){
						
						//Insertion of selected data into the Booking table - Parameterized query used to prevent SQL injection.
						$sql = "INSERT INTO Booking (Email,PerfDate,PerfTime,RowNumber) VALUES (:email,:pdate,:ptime,:rn);";
						$handle = $conn->prepare($sql);
						$handle->bindParam(':email', $_POST['email']);
						$handle->bindParam(':pdate', $_SESSION['perfDate']);
						$handle->bindParam(':ptime', $_SESSION['perfTime']);
						$handle->bindParam(':rn', $seat);
						$handle->execute();
					};				
					//Close the connection
						$conn = null;
						
					//Tell the user that the booking was successful.
					echo "<script type='text/javascript'>alert('Your booking has been made successfully!'); document.location.href=\"index.php\";</script>";
				};
			};
			
		//Error thrown if code is not executed.
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		?> 
	</body>
</html>


