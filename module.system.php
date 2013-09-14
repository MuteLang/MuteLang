<?

// To Do: Use the parser values

if($program["<system>"]["attr"]["logs"] == "on"){
	print "<table style='font-size:11px; font-family:courier; border:1px; margin:20px 0px'>";
	print "<tr><th>#</th><th>Line</th><th>Operaion</th><th>Time</th></tr>";
	foreach ($logs as $key => $value) {
		print "<tr><td>".$key."</td><td><b>".$value['line']."</b></td><td>".$value['message']."</td><td>".$value['time']."</td></tr>";
	}
	print "</table>";
}

if($program["<system>"]["attr"]["memory"] == "on"){
	print "<pre style='padding:10px; border:1px dashed #000; font-size:11px; line-height:10px'>";
	$test = print_r($program,true);
	echo htmlentities($test);
	print "</pre>";
}

?>