<?php
// Start the session
session_start();

// Move the selected performance time/date to a SESSION variable so we don't have to create another hidden input in this script's form.
$_SESSION['perfTime'] = $_GET['perfTime'];
$_SESSION['perfDate'] = $_GET['perfDate'];
?>

<!-- Instruction to the web browser that this page contains HTML-->
<!DOCTYPE html>
<html>
	<head>
		<!-- Meta tags used to provide machine parsable information -->
		<meta charset="UTF-8">
		<meta name="keywords" content="Theatre Booking System"/>
		<meta name="description" content="List of seats for the selected performance"/>
		<meta name="author" content="Lewis Saunders">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Title of the website -->
		<title>Theatre Booking System</title>
		
		<!-- Linking of stylesheets to provide CSS styling and fonts -->
		<link rel="stylesheet" href="mystyles.css">
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans">
		
		<!-- JQuery Import -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		
		<!-- Javascript to allow fade in of the mainframe upon initial loading of the page -->
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#mainframe').fadeIn(400);
			});
			var totalPrice = 0;
			var seatsSelected = [];
			
			//Adjusts the variables totalPrice and seatsSelected when a checkbox is checked or unchecked.
			function totalSummary(cost,seats,index){					
				var checkBox = document.getElementById("check" + index);
				if(checkBox.checked == true){
					totalPrice = totalPrice + cost;
					seatsSelected.push(seats);
				}else{
					totalPrice = totalPrice - cost;
					var ind = seatsSelected.indexOf(seats);
					seatsSelected.splice(ind,1);
				};
			};
			
			//Displays the seats selected and subsequent subtotal in an alert window.
			function displayTotal(){
				alert('Total Cost: £' + totalPrice + ' Seats selected: ' + seatsSelected.toString());
			};
			
			//Client-side validation
			function validateForm(){
				var emailInput = document.forms["seatForm"]["email"].value;
				var at = "@";
				if(emailInput == ""){
					alert("Email box is empty! Please enter a valid email address");
					return false;
				}else if(emailInput.includes(at) == false){
					alert("Please enter a valid email address");
					return false;
				}else{
					var title = document.getElementById("title").value;
					var perfTime = document.getElementById("perfTime").value;
					var perfDate = document.getElementById("perfDate").value;
					if(confirm("You have selected:\nShow: " + 
					title + "\nPerformance Time & Date: " + 
					perfTime + ' ' + perfDate + "\nSeats Selected: " + 
					seatsSelected.toString() + "\nAre you sure you want to proceed?")){
						return true;
					}else{								
						return false;
					};
				};
			};
			
			//Sends the user to the previous page.
			function back(){
				window.history.back();
			}
			
			//Sends the user back to the start.
			function startOver() {
				document.location.href="index.php";
			}
		</script>
	</head>

	<body>
	
		<!-- div used to seperate and style specific HTML segments -->
		<div id="mainframe">
		
		<h1> Theatre CO887 </h1>
		<h2> Seats for <?php echo $_SESSION['title']." - ".$_SESSION['perfTime']." - ".$_SESSION['perfDate']?> </h2>
		
		<!-- PHP code that links to 'connect.php' to allow connection to SQL database-->
		<?php require 'connect.php';
		 try {
			//Connection to SQL database and execution of SQL query. Parameterized query used ('?') to prevent SQL injection.
			$conn = connect();
			$sql = "SELECT Seat.RowNumber, CAST((Zone.PriceMultiplier * 15.00) AS decimal(5,2)) AS 'Price' 
			FROM Seat, Zone WHERE Zone.Name=Seat.Zone AND Seat.RowNumber NOT IN 
			(SELECT Booking.RowNumber FROM Booking WHERE Booking.PerfTime=? AND Booking.PerfDate=?)";
			$handle = $conn->prepare($sql);
			
			//Binding the parameters to the correct index in the above query.
			$handle->bindParam(1, $_SESSION['perfTime'], PDO::PARAM_STR);
			$handle->bindParam(2, $_SESSION['perfDate'], PDO::PARAM_STR);
			$handle->execute();
			
			//Close the connection.
			$conn = null;			

			//Fetch all rows from the executed query.
			$res = $handle->fetchAll(); 
			
			/*Number of rows returned to be stored in a session variable to be accessed on book.php. This is so all 
			  the selected seats can be placed into an array on book.php to then be inserted into 'Booking' */
			$_SESSION["row_count"] = $handle->rowCount();
			
			//Results placed into a table.
			echo "<table><tr><th>Seat</th>"." "."<th>Price</th></tr>";
			
			//Incrementer used in this for loop so that each selected seat can be accessed on book.php by a linked index.
			$i = 0;
		
				/*For loop used to print each row from the SQL output. Form used with POST as the submitted data
				  is being inserted into a table. Selected seats are being submitted. */
				foreach($res as $row) {
					$rowNumber = $row["RowNumber"];
					$price = $row["Price"];
					echo "<tr><td>".$row["RowNumber"]."</td>"." "."<td>£".
					$row["Price"]."</td><td><form name='seatForm' onsubmit=\"return validateForm()\" method='POST' action='book.php'>
					<input type=hidden name='rowNumber".$i."' value=".$row["RowNumber"].">
					<input type='checkbox' name='$i' id='check$i' onclick=\"totalSummary($price,'$rowNumber',$i);\">
					</td></tr>";
					$i++;
				};

			echo "</table>";
			
			//div used to style 'insert email' and place it next to text box.
			echo"<div id='insertemail' style='display:inline'>Insert Email:</div> <input type='text' name='email'>";
			
			//'Check' button shows all selected seats and the subtotal.
			//'Book' submits the selected seat data and the inputted email to book.php
			//Navigation aids for if the user wants to go back a page or restart the booking.
			echo"<button type='button' name='check' onclick='displayTotal();'>Check</button>
			<button type='submit' name='book'>Book</button></form>
			<button onclick='back()' class='returnbuttons'>Back</button>
			<button onclick='startOver()' class='returnbuttons'>Start Over</button>";
				
			//allows the validateForm() function to access the required session variables.
			echo 
	        " <input type=hidden id='title' value='".$_SESSION['title']."'>
			  <input type=hidden id='perfDate' value='".$_SESSION['perfDate']."'>
			  <input type=hidden id='perfTime' value='".$_SESSION['perfTime']."'>";
			  
			//Error thrown if code is not executed.
			} catch (PDOException $e) {
				echo $e->getMessage();
			}
		?> 
		</div>
	</body>
</html>
