<?php
session_save_path("/student/smiths61/wwwsess/ww/");
session_start();

$state = &$_SESSION['state'];
$authenticated = &$_SESSION['authenticated'];
$user_name = &$_SESSION['user_name'];
$user_id = &$_SESSION['userid'];
$user_highscore = &$_SESSION['highscore'];

$errormessage = "";

$dbconn = NULL;


function dbConnect() {
	global $dbconn;	
	if (!isset($dbconn)) {
		$dbconn = pg_connect("host=localhost dbname=smiths61 user=smiths61 password=59882") or die("Could not connect: " . pg_last_error());
	}
}

function dbClose() {
	global $dbconn;
	if (isset($dbconn)) {
		pg_close($dbconn);
	}
}




// First visit
if (!isset($state)) {
	$state = "home_guest";
}


// Determine current state to serve content
$action = &$_POST['action'];
switch ($state) {
	case "home_guest":
		// Homepage for unauthenticated user
		$view = "home.php";
		$authenticated = false;
		
		if ($action == "login") {
			// User is logging in; check credentials against the database.
			$cred_username = $_POST['login_username'];
			$cred_password = $_POST['login_password'];

			dbConnect();
			// NOTE(sdsmith): Used delayed pg_prepare in future.
			$prepare_ret = pg_prepare($dbconn, "credential_check", 'SELECT * FROM appuser, appuser_passwords WHERE appuser.name = $1 and appuser_passwords.password = $2');
			if ($prepare_ret) {
				$resultobj = pg_execute($dbconn, "credential_check", array($cred_username, $cred_password));
				if ($resultobj) {
					$result_as_array = pg_fetch_array($resultobj);
					if ($result_as_array != false) {
						// There was a result, so we are authenticated.

						// Sanity check: confirm there was only one credential match.
						if ($result_as_array = pg_fetch_array($result)) {
							$authenticated = false;
							die("Multiple user credential matches");
						}

						var_dump($autheduserdata);
						// Properly authenticated; do post authentication setup below
						$authenticated = true;
						$state = "home_authenticated";
						$user_id = $autheduserdata['id'];
						$user_name = $cred_username;
						$user_highscore = $autheduserdata['highscore'];
					} else {
						// Did not authenticate
						$errormessage = "Invalid Credentials";
					}
				} else {
					die("Query failed: " . pg_last_error());
				}
			} else {
				die("Prepared statement failed: " . pg_last_error());
			}

			dbClose();
			break;

		} elseif ($action == "register_user") {
			// NOTE(sdsmith): this relies on it falling through.
			// TODO(sdsmith): make determin state function and call it when new state needs to be decoded.	
			$state = "registration";
		}

	case "home_authenticated":
		// Homepage for authenticated user
		$view = "home.php";

		if ($action == "logout") {
			$authenticated = false;
			session_destroy();
			$state = "home_guest";
		}

		break;

	case "registration":
		// Registration page
		$view = "register.php";
		
		if ($action == "registration_submit") {
			
		} elseif ($action == "home") {
			$view = "home.php";
		}

		break;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Warehouse Wars Online</title>
	</head>
	<body>
		<h1>Warehouse Wars Online</h1>
		<hr/>

		<?php if ($errormessage != "") : ?>
		<div id='errormessage'>
			<?php echo $errormessage ?>
		</div>
		<?php endif ?>

		<?php include($view) ?>

	</body>
</html>
