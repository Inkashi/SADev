<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
</head>
<body>
	<form action="register" method="post">
		@csrf
		<input type="text" name="username" placeholder="username">
		<input type="text" name="email" placeholder="email">
		<input type="text" name="password" placeholder="password">
		<input type="text" name="c_password" placeholder="c_password">
        <input type="date" name="birthday" placeholder="birthday">
		<input type="submit">
	</form>
</body>
</html>