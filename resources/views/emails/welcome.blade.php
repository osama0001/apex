<html>
	<head>
		<title>Welcome Email</title>
	</head>
	<body>
		<table>
			<tr><td>Dear {{ $name }}</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>Your account has been successfully activated</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>Your information is as below:</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>Email: {{ $email }}</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>Password: ****** (as chosen by you) </td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>Thanks & Regards</td></tr>
			<tr><td>E-Com Website</td></tr>
		</table>
	</body>
</html>