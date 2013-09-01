<?

$original = file_get_contents("mutescript.php");
print "<h2>Code</h2>";
print "<pre>";
print_r($original);
print "</pre>";

print "<h2>Console</h2>";


$original = str_replace(" ", "", $original);
$operations = explode("\n", $original);


// =======================
// Parser
// =======================


foreach ($operations as $key => $value) {

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

		$temp2 = explode(":", $value);
		if( count($temp2) > 1 ){
			$temp[$temp2[0]] = $temp2[1];
		}

		$temp[$key] = $value;
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

	$run["cond_variables"] = preg_split('/[^a-z0-9.]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$run["cond_operators"] = preg_split('/[a-z0-9.]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	$first = floatval(associate($run["cond_variables"][0]));
	$second = floatval(associate($run["cond_variables"][1]));
	$operator = $run["cond_operators"][0];

	switch($operator){
		case ">": $resolving = $first > $second; break;
		case "<": $resolving = $first < $second; break;
		case "=": $resolving = $first ==$second; break;
	}

	if( $resolving || !$run["cond"] ){
		return true;
	}
	else{
		return false;
	}

}


function associate($var){

	global $program;
	global $id;

	$breakdown = explode(".", $var);

	// If $ use self
	$varname = $breakdown[0];
	if($varname == "$"){
		$varname = $id;
	}

	// If key is null, use 0
	if( strlen($breakdown[1]) > 0 && intval($breakdown[1]) < 1){
		$varkey = $breakdown[1];
	}
	else{
		$varkey = intval($breakdown[1]); 
	}
	

	if( $program[$varname]["attr"][0] ){
		return $program[$varname]["attr"][$varkey];
	}

	return $var;

}


function operate($operation){

	global $program;

	$id = preg_split('/[^a-z0-9]/i', $operation, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$id = $id[0];

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
		$replacements[$key] = associate($value);
	}

	$result = str_replace(
	    array("@1","@2","@3"),
	    $replacements,
	    $string
	);

	return "$result";

}

print "<h2>Memory</h2>";
print "<pre>";
print_r($program);
print "</pre>";

?>