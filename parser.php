<?

// Todo
$filename = "mutescript.mu.php";
$original = file_get_contents($filename);
if (preg_match_all('/"([^"]+)"/', $original, $m)) {
    $string_uncompressed = $m[1]; 
    $string_compressed = str_replace(" ", "", $string_uncompressed);
}
$logs = array();

console("Start: $filename");

$original = str_replace(" ", "", $original);
// Decompress strings between quotes
$original = str_replace($string_compressed, $string_uncompressed, $original);
$operations = explode("\n", $original);

// =======================
// Parser
// =======================

foreach ($operations as $key => $value) {

	global $id;
	console("Line $key");

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

	$id = preg_split('/[^a-z0-9]/i', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$id = $id[0];

	preg_match('#\[(.*?)\]#', $value, $attr);
	$attr = $attr[1];
	preg_match('#\((.*?)\)#', $value, $cond);
	$cond = $cond[1];
	preg_match('#\{(.*?)\}#', $value, $oper);
	$oper = $oper[1];

	$program[$id]["name"] = $id;
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
		console("Attr : $key");
		$temp[$key] = renderValue($value);
	}

	return $temp;

}



// =======================
// Interpreter
// =======================


function interpreter($id){

	global $program;

	if( resolve($program[$id]) && $program[$id]["oper"] ){
		operate($program[$id]['oper']);
	}

}


function resolve($run){

	$run["cond_variables"] = preg_split('/[^a-z0-9.$]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$run["cond_operators"] = preg_split('/[a-z0-9.$]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	$first = renderValue($run["cond_variables"][0]);
	$second = renderValue($run["cond_variables"][1]);
	$operator = $run["cond_operators"][0];

	switch($operator){
		case ">": $resolving = $first > $second; break;
		case "<": $resolving = $first < $second; break;
		case "=": $resolving = $first ==$second; break;
	}

	// If only 1 variable as condition
	if( count($run["cond_variables"]) < 2 && $first > 0 ){
		$resolving = 1;
	}

	if( $resolving || !$run["cond"] ){
		return true;
	}
	else{
		return false;
	}

}


function renderValue($var){

	global $program;
	global $id;

	if(!$var){
		return;
	}

	$attrMods = preg_split('/[a-z0-9.$]/i', $var, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$modifier = $attrMods[0];
	$attrContent = preg_split('/[^a-z0-9.$]/i', $var, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$key = dotValue($attrContent[0]);
	$index = dotValue($attrContent[1]);

	// Random
	if (strpos($var, '~') !== FALSE){
		console("Rand : $key $index");
		return rand($key,$index);
	}
	// Length
	if (strpos($var, ';') !== FALSE){
		console("Length : ".strlen($program[$key]["attr"][0]));
		return strlen($key);
	}
	// Merge
	if (strpos($var, '&') !== FALSE){
		console("Merge: $key $index");
		return $key.$index;
	}
	// Combine
	if (strpos($var, '+') !== FALSE){
		console("Add  : $key $index");
		return $key + $index;
	}
	// Index (TODO)
	if (strpos($var, ':') !== FALSE){
		console("Index(error)");
		$program[$id]["attr"][$key] = $index;
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
	console("Default");

	// Return
	return $var;

}


function dotValue($dotvalue){

	global $program;
	// Get
	if (strpos($dotvalue, '.') !== FALSE){

		$elements = explode(".", $dotvalue);
		$key = $elements[0];
		$index = $elements[1];

		console("Get  : ".$key." ".$index);
		if( $program[$key]["attr"][$index] ){
			return $program[$key]["attr"][$index];
		}
	}

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
	    array("@1","@2","@3"),
	    $replacements,
	    $string
	);

	return "$result";

}


function console($message){

	global $logs;
	$logs[count($logs)] = $message;

}

print "<pre style='padding:10px; border:1px dashed #000; font-size:11px; line-height:10px; margin-bottom:20px'>";
print_r($logs);
print "</pre>";

print "<pre style='padding:10px; border:1px dashed #000; font-size:11px; line-height:10px'>";
print_r($program);
print "</pre>";

?>