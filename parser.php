<?

// Todo
$filename = "mute.main.txt";
$original = file_get_contents($filename);
if (preg_match_all('/"([^"]+)"/', $original, $m)) {
    $string_uncompressed = $m[1]; 
    $string_compressed = str_replace(" ", "", $string_uncompressed);
}
$logs = array();
$lineNum = 0;
console("Start: $filename");

$original = str_replace(" ", "", $original);
$original = str_replace($string_compressed, $string_uncompressed, $original);
$operations = explode("\n", $original);
$time_start = microtime(true); 

// =======================
// Parser
// =======================

foreach ($operations as $key => $value) {

	global $id;
	global $lineNum;
	$lineNum++;

	// Unset unnanmed functions
	unset($program[""]);

	// skip comments
	if(substr($value, 0,1) == "#"){ continue; }

	// Store Id
	$id = preg_split('/[^a-z0-9]/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$id = $id[0];

	$program = update($value);
	interpreter($id);

}

// =======================
// Updater
// =======================


function update($value){

	global $program;
	global $id;

	$id = "";

	if (preg_match("/^[a-zA-Z0-9\\<\\>]$/", substr($value, 0,1) )) {
	    $id = preg_split('/[^a-z0-9\\<\\>]/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$id = $id[0];
	}

	preg_match('#\<(.*?)\>#', $value, $extn);
	$extn = $extn[1];
	preg_match('#\[(.*?)\]#', $value, $attr);
	$attr = $attr[1];
	preg_match_all('#\((.*?)\)#', $value, $cond);
	$cond = $cond[1];
	preg_match_all('#\{(.*?)\}#', $value, $oper);
	$oper = $oper[1];

	$program[$id]["name"] = $id;
	if( $extn ){

	}
	if( $attr ){
		$program[$id]["attr"] = update_attr($attr);
	}
	if( $cond ){
		$program[$id]["cond"] = $cond;
	}
	if( $oper ){
		$program[$id]["oper"] = $oper;
	}

	return $program;

}


function update_attr($attr){

	$temp = explode(",", $attr);

	foreach ($temp as $key => $value) {

		// Hard Setter
		if (strpos($value, ':') !== FALSE){
			$indexAndKey = explode(":", $value);
			console("Set  : ".$indexAndKey[0]." ".$indexAndKey[1]);
			$updated[$indexAndKey[0]] = renderValue($indexAndKey[1]);
		}
		// If an array is returned
		elseif( is_array(renderValue($value)) ){
			console("Attr : Array($key)");
			$updated = renderValue($value);
		}
		// Render
		else{
			console("Attr : $key");
			$updated[$key] = renderValue($value);
		}
		
	}

	return $updated;

}



// =======================
// Interpreter
// =======================


function interpreter($id){

	global $program;

	// Multiple conditions
	if($program[$id]["cond"]){
		foreach ($program[$id]["cond"] as $key => $condition) {
			if(resolve($condition) == FALSE){
				return;
			}
		}
	}

	// multiple operations
	if($program[$id]["oper"]){ 
		foreach ($program[$id]["oper"] as $key => $operationId) {
			operate($operationId);
		}
	}

}


function resolve($run){

	$run1["cond_variables"] = preg_split('/[^a-z0-9.$]/i', $run, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$run1["cond_operators"] = preg_split('/[a-z0-9.$]/i', $run, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	$first = renderValue($run1["cond_variables"][0]);
	$second = renderValue($run1["cond_variables"][1]);
	$operator = $run1["cond_operators"][0];

	switch($operator){
		case ">": $resolving = $first > $second; break;
		case "<": $resolving = $first < $second; break;
		case "=": $resolving = $first ==$second; break;
	}

	// If only 1 variable as condition
	if( count($run1["cond_variables"]) < 2 && $first > 0 ){
		$resolving = 1;
	}

	if( $resolving || !$run ){
		return true;
	}
	else{
		return false;
	}

}


function renderValue($var){

	global $program;
	global $id;

	$var = str_replace("$",	$id, $var);

	if(!$var){
		return;
	}

	console("Render  : ".htmlentities($var));

	$attrMods = preg_split('/[a-z0-9.$]/i', $var, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$modifier = $attrMods[0];
	$attrContent = preg_split('/[^a-z0-9.$]/i', $var, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$key = dotValue($attrContent[0]);
	$index = dotValue($attrContent[1]);
	$var = dotValue($var);

	if(count($var)>1){
		return $var;
	}

	// Primary

	// Random
	if (strpos($var, '~') !== FALSE){
		console("Rand : $key $index");
		return rand($key,$index);
	}
	// Substring
	if (strpos($var, '|') !== FALSE){
		console("Subs : $key $index");
		return substr($key, $index);
	}
	// Merge
	if (strpos($var, '&') !== FALSE){
		console("Merge: $key $index");
		return $key.$index;
	}

	// Secondary
	
	// Add
	if (strpos($var, '+') !== FALSE){
		console("Add  : $key $index");
		return $key + $index;
	}
	// Subtract
	if (strpos($var, '-') !== FALSE){
		console("Add  : $key $index");
		return $key - $index;
	}
	// Multiply
	if (strpos($var, '*') !== FALSE){
		console("Add  : $key $index");
		return $key*$index;
	}
	// Divide
	if (strpos($var, '/') !== FALSE){
		console("Add  : $key $index");
		if($index == 0){$index = 1;}
		return $key / $index;
	}
	// Modulo
	if (strpos($var, '%') !== FALSE){
		console("Add  : $key $index");
		return $key % $index;
	}
	// Default
	if( $program[$key]["attr"][$index] && count($attrMods) < 1){
		console("Get  : $key $index");
		return $program[$key]["attr"][$index];
	}
	// Index (TODO)
	if (strpos($var, '.') !== FALSE){
		return $key;
	}

	// Return
	return $var;

}


function dotValue($dotvalue){

	global $program;

	if (strpos($dotvalue, '.') !== FALSE){
		$elements = explode(".", $dotvalue);
		$key = $elements[0];
		$index = $elements[1];
		if( $program[$key]["attr"][$index] ){
			return $program[$key]["attr"][$index];
		}
	}
	else if( $program[$dotvalue]["attr"][0] ){
		if( count($program[$dotvalue]["attr"]) > 1 ){
			return $program[$dotvalue]["attr"];
		}
		else{
			return $program[$dotvalue]["attr"][0];
		}
	}
	return $dotvalue;

}


function operate($operation){

	global $program;
	global $id;

	if(substr($operation, 0,1) == "\""){
		echo renderString($operation);
	}
	else{
		$program = update($operation);
		interpreter($id);
	}

}


function renderString($operation){

	preg_match('/"([^"]+)"/', $operation, $string);
	$string = $string[1];

	$replacements = str_replace("\"".$string."\",", "", $operation);
	$replacements = explode(",", $replacements);

	foreach ($replacements as $key => $value) {
		$replacements[$key] = renderValue($value);
	}

	$result = str_replace(
	    array("@","@","@","@","@"),
	    $replacements,
	    $string
	);

	return "$result";

}


function console($message){

	global $logs;
	global $lineNum;
	global $time_start;

	$logNum = count($logs);
	$logs[$logNum]["message"] = $message;
	$logs[$logNum]["time"] = number_format((float)(1000*(microtime(true) - $time_start)), 2, '.', '');
	$logs[$logNum]["line"] = $lineNum;

}

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