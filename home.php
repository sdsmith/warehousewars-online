<?php if (isset($authenticated) and $authenticated) : ?>
<div id="userpanel">
	<?php
	echo "Welcome $user_name<br>Highscore: $user_highschool";
	?>
	<form id="logout" method="post">
		<input type="hidden" name="action" value="logout" />
		
	</form>
</div>
<?php else : ?>
<div id="authentication">
	
	<form id="login" method="post">
		<input type="hidden" name="action" value="login" />
		Username: <input type="text" name="login_username" value="<?php echo $_POST['login_username']; ?>" /><br/>
		Password: <input type="password" name="login_password" /><br/>
		<input type="submit" value="Login" />
	</form>
	
	<form id="register" method="post">
		<input type="hidden" name="action" value="register_user" />
		<input type="submit" value="Register" />
	</form>
</div>
<?php endif ?>


<hr/>
<h3>Highscores - Top 10</h3>
<ul>
	<?php
	
	?>
</ul>