<?php
// Start the session
session_start();

// Move the selected title to a SESSION variable so it can be accessed in other scripts.
$_SESSION['title'] = $_GET["title"];
?>

<!-- Instruction to the web browser that this page contains HTML-->
<!DOCTYPE html>
<html>
	<head>
		<!-- Meta tags used to provide machine parsable information -->
		<meta charset="UTF-8">
		<meta name="keywords" content="Theatre Booking System"/>
		<meta name="description" content="List of performances for the selected show"/>
		<meta name="author" content="Lewis Saunders">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Title of the website -->
		<title>Theatre Booking System</title>
		
		<!-- Linking of stylesheets to provide CSS styling and fonts -->
		<link rel="stylesheet" href="mystyles.css">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans">
		
		<!-- JQuery import -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		
		<!-- Javascript to allow fade in of the mainframe upon initial loading of the page -->
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#mainframe').fadeIn(400);
			});
			
			//Sends the user to the previous page
			function back(){
				window.history.back();
			}
			
			//Sends the user back to the start
			function startOver() {
				document.location.href="index.php";
			}
		</script>
	</head>

	<body>
		<!-- div used to seperate and style specific HTML segments -->
		<div id="mainframe">
		
		<h1> Theatre CO887 </h1>
		<h2> Timetable for <?php echo $_SESSION["title"]." (tickets from £".$_GET["price"].")"?> </h2>
		
		<!-- PHP code that links to 'connect.php' to allow connection to SQL database-->
		<?php require 'connect.php';
		 try {
			//Connection to SQL database and execution of SQL query - Parameterized query used ('?') to prevent SQL injection.
			$conn = connect();
			$sql = "SELECT * FROM Performance WHERE Performance.Title =?";
			$handle = $conn->prepare($sql);
			$handle->execute([$_GET['title']]);
			
			//Close the connection
			$conn = null;		
			
			//Fetch all rows from the executed query.
			$res = $handle->fetchAll(); 
			
			//Results placed into a table.
			echo "<table><tr><th>Title</th>"." "."<th>Date</th>"." "."<th>Time</th></tr>";
			
				/*For loop used to print each row from the SQL output. Form used with GET method to submit the selected Performance Time and Date*/
				foreach($res as $row) {
					echo "<tr><td>".$row["Title"]."</td>"." "."<td>".
					$row["PerfDate"]."</td>"." "."<td>".$row["PerfTime"]."</td>"." ".
					"<td><form method='GET' action='seats.php'>
					<input type=hidden name='perfDate' value=".$row["PerfDate"].">
					<input type=hidden name='perfTime' value=".$row["PerfTime"].">
					<button type='submit'>Show Availability</button></form></td></tr>";
					
				}
				
			echo "</table>";
			
			//Navigation aids for if the user wants to go back a page or restart the booking.
			echo "<button onclick='back()' class='returnbuttons'>Back</button>
				  <button onclick='startOver()' class='returnbuttons'>Start Over</button>";
	
		//Error thrown if code is not executed
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
		?> 	
		</div>
	</body>
</html>
