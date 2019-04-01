<!-- Instruction to the web browser that this page contains HTML-->
<!DOCTYPE html>
<html>
	<head>
	    <!-- Meta tags used to provide machine parsable information -->
		<meta charset="UTF-8">
		<meta name="keywords" content="Theatre Booking System"/>
		<meta name="description" content="A booking system to book performances
		of all your favorite shows"/>
		<meta name="author" content="Lewis Saunders">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Title of the website -->
		<title>Theatre Booking System</title>
		
		<!-- Linking of stylesheets to provide CSS styling and fonts -->
		<link rel="stylesheet" type="text/css" href="mystyles.css">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans">
		
		<!-- JQuery import -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		
		<!-- Javascript to allow fade in of the mainframe upon initial loading of the page -->
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#mainframe').fadeIn(400);
			});
		</script>
	</head>

	<body>
		
		<!-- div used to seperate and style specific HTML segments -->
		<div id = "mainframe">
		
		<h1> Theatre CO887 </h1>
		<h2> Upcoming Shows </h2>
		
		<!-- PHP code that links to 'connect.php' to allow connection to SQL database-->
		<?php require 'connect.php';
		 try {
				//Connection to SQL database and execution of SQL query.
				$conn = connect();
				$sql = "SELECT * FROM Production";
				$handle = $conn->prepare($sql);
				$handle->execute();
				
				//Close the connection.
				$conn = null;		
				
				//Fetch all rows from the executed query.
				$res = $handle->fetchAll(); 
				
				//Results placed into a table
				echo "<table><tr><th>Title</th>"." "."<th>Tickets From</th></tr>";
				
					/*For loop used to print each row from the SQL output. Form used with GET method to submit the selected Title and subsequent
					  Basic Ticket Price to perf.php*/
					foreach($res as $row) {
						echo "<tr><td>".$row["Title"]."</td>"." "."<td>£".
						$row["BasicTicketPrice"]."</td>"." ".
						"<td><form method='GET' action='perf.php'>
						<input type=hidden name='title' value=".$row["Title"].">
						<input type=hidden name='price' value=".$row["BasicTicketPrice"].">
						<button type='submit'>Show Performances</button></form></td></tr>";						
					}
				
			//Error thrown if code is not executed
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		?> 
		</table></div>
	</body>
</html>
