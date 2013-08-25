<?

$original = file_get_contents("mutescript.php");
$original = str_replace(" ", "", $original);
$original = str_replace("){", ")\n{", $original);
$original =  trim(preg_replace('/\t+/', '', $original));

$operations = explode("\n", $original);


echo "<h1>Operations</h1>";

echo "<pre>";
print_r($operations);
echo "</pre>";

echo "<h1>Program</h1>";

foreach ($operations as $key => $value) {

	$next = $key+1;
	$var = preg_split('/[^a-z0-9]/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$ops = preg_split('/[a-z0-9]/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	// replace the variables
	foreach ($var as $key => $value) {
		if( $save[$value] ){
			$var[$key] = $save[$value];
		}
	}

	// ======================
	// Log
	// ======================

// echo "<pre>";
// print_r($var);
// echo "</pre>";
// echo "<pre>";
// print_r($ops);
// echo "</pre>";


	// ======================
	// Equal
	// ======================
	if( count($ops) == 1 && $ops[0] == "=" ){
		$save[$var[0]] = $var[1];
	}

	// ======================
	// Render
	// ======================

	if( count($ops) == 2 && $ops[0] == "\"" && $ops[1] == "\"" ){
		print $save[$var[0]];
	}

	// ======================
	// Print
	// ======================
	if( count($ops) == 2 && $ops[0] == "'" && $ops[1] == "'" ){
		print $var[0];
	}

	// ======================
	// Condition
	// ======================

	if( count($ops) == 3 && $ops[0] == "(" && $ops[2] == ")" ){
		if($ops[1]==">"){
			if($var[0] > $var[1]){
				$prevCondition = 1;
			}
		}
	}

	// ======================
	// Operation
	// ======================
	if( $ops[0] == "{'" && $ops[1] == "'}" ){
		if($prevCondition > 0){
			
			$prevCondition -1;
			echo "true";


		}
	}




}

echo "<h1>Storage</h1>";

echo "<pre>";
print_r($save);
echo "</pre>";



function patternDetect($block){


	

	return $items[0];

}


?>