<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Login</title>
</head>
<body>
	<form action="login" method="post">
		@csrf
		<input type="text" name="username" placeholder="username">
        <input type="text" name="password" placeholder="password">
        <input type="submit">
	</form>
</body>
</html>