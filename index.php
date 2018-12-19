<?php

// Database Connection
$host   = 'localhost';
$dbName = 'task';
$dbUser = 'root';
$dbPass = '';

try {
	$dbConn = new PDO("mysql:host=$host; dbname=$dbName", $dbUser, $dbPass);
	$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo 'Connection Error: '.$e->getMessage();
}

// To get users data
function getUserData($dbConn) {
	$query = 'SELECT * FROM user';
	$stmt  = $dbConn->prepare($query);

	if ($stmt->execute() && $stmt->rowCount() == 0) {
		return 'There is no data till now.';
	} elseif ($stmt->execute() && $stmt->rowCount() > 0) {
		$data = [];
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	} else {
		return 'Something wrong!';
	}
}

// Load Users Data
$usersData = getUserData($dbConn);

// Validator
function validate($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
// Insert User Data
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$name  = validate($_POST['name']);
	$age   = validate($_POST['age']);
	$query = 'INSERT INTO user (name, age) VALUES (:name, :age)';
	$stmt  = $dbConn->prepare($query);
	$stmt->execute([
			':name' => $name,
			':age'  => $age,
		]);
}
?>
<!-- Start Html -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Task</title>

	<style type="text/css" media="screen">
		* {
			font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		}
		.user-show-data {
			position: relative;
			width: 840px;
			margin:auto;
		}
		#user-table {
		    border-collapse: collapse;
		    width: 810px;
		    margin:auto;
		    margin-bottom: 20px;
		}
		#user-table caption {
			background-color: #3c3c3c;
			color: #f9f9f9;
			padding:10px 0;
			font-weight: bold;
		}
		#user-table th {
		    text-align: left;
		    background-color: #3c3c3c;
		    color: white;
		}
		#user-table td, #user-table th {
		    border: 1px solid #d9d9d9;
		    padding: 10px;
		}
		#user-table tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		#add-user-btn, .user-form-inner form button  {
			position: absolute;
			right: 30px;
			background: none;
			background-color: #3c3c3c;
			color: #f8f8f8;
			border: 1px solid #f1f1f1;
			border-radius: 4px;
			padding: 8px 18px;
		}
		#add-user-btn:hover, .user-form-inner form button:hover  {
			background-color: #f8f8f8;
			color: #080808;
			border: 1px solid #3c3c3c;
			cursor: pointer;
		}
		.user-form {
			position: fixed;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			background-color: #3f3f3fb3;
			z-index: 9999;
			display: none;
		}
		.user-form .user-form-inner {
			position: inherit;
			width: 600px;
			background-color: #f9f9f9;
			left: 30%;
			bottom: 50%;
			border-radius: 10px;
		}
		.user-form-inner form {
			position: relative;
			height: 200px;
			padding:10px;
		}
		.user-form-inner form .field {
			margin: 10px 0;
			padding:0 20px;
		}
		.user-form-inner form .field label {
			display: block;
			margin-bottom: 8px;
		}
		.user-form-inner form .field input {
			display: block;
			width: 100%;
			background: none;
			background-color: #fff !important;
			border: 1px solid #c9c9c9;
			padding: 6px;
		}
		.user-form-inner form .field input:focus {
			outline: 1px solid #C1C1C1;
		}
		.show {
			background-color: #8eff71;
			width: 400px;
			height: 37px;
			margin: auto;
			margin-bottom: 20px;
			text-align: center;
			color: #FFF;
			line-height: 2.3;
			font-weight: 700;
			transition: all 0.5s ease-in-out;
		}
	</style>

	<!-- JQuery CDN -->
	<script
  			src="https://code.jquery.com/jquery-3.3.1.min.js"
  			integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  			crossorigin="anonymous">
  	</script>

</head>
<body>
	<div class="container">
		<!-- To show message coming from ajax response -->
		<div class="message"></div>

		<!-- User Table -->
		<div class="user-show-data">
			<table id="user-table">
				<caption>User Data</caption>
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Age</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if (is_array($usersData)) {
						foreach ($usersData as $user) {?>
									<tr>
										<td><?=$user['id']?></td>
										<td><?=$user['name']?></td>
										<td><?=$user['age']?></td>
									</tr>
							<?php }
					} else {?>
							<tr>
								<td colspan="3" ><?=$usersData?></td>
							</tr>
						<?php }
				?>
				</tbody>
		    </table>

			<!--Button to show form -->
			<button id="add-user-btn" type="button" data-popup="#pop-form">Add User</button>
		</div>
		<!--Include form file -->
	 <?php include ('form.php');?> 
	</div>


	<script>
		$(function () {

			// To show the form
			$('#add-user-btn').click(function() {
				$($(this).data('popup')).fadeIn(300);
			});

			// To stop propagation of event
			$('.user-form .user-form-inner').click(function(e) {
				e.stopPropagation();
			});

			// To hide the form when click outside form box
			$('.user-form').click(function() {
				$(this).fadeOut(300);
			});

			// Ajax
			var form = $('#ajax-form');
			$(form).submit(function(e) {
				e.preventDefault();
				var formData = $(form).serialize();
				$.ajax({
					type:'post',
					url:$(form).attr('action'),
					data:formData,
					success: function() {
						$('#pop-form').fadeOut(300);
						$(".message").addClass('show').html("<p>User Added Successfully.</p>").delay(2000).fadeOut(250, function() {
							window.location.reload();
						});

					},
					error: function() {
						console.log('Something Wrong!');
					}
				});
			});
		});
	</script>
</body>
</html>