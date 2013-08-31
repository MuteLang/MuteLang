<?

$original = file_get_contents("mutescript.php");
$original = str_replace(" ", "", $original);
$operations = explode("\n", $original);

$line = 0;

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
		$program[$id]["attr"] = explode(",", $attr);
	}
	if( $cond ){
		$program[$id]["cond"] = $cond;
	}
	if( $oper ){
		$program[$id]["oper"] = $oper;
	}

	return $program;

}

// =======================
// Interpreter
// =======================

function interpreter($id){

	global $program;

	if( resolve($program[$id]) && $program[$id]["oper"] ){
		echo "$id: Operating<br />==================<br />";
		operate($program[$id]['oper']);
	}
	else{
		echo "$id: Skipped<br />==================<br />";
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

	echo "Resolve: $first $operator $second<br />";

	if( $resolving ){
		return true;
	}
	else{
		return false;
	}

}

function associate($var){

	global $program;

	if (strpos($var, '.') !== false){
		$breakdown = explode(".", $var);
		$varname = $breakdown[0];
		$varkey = intval($breakdown[1]);

		if( $program[$varname]["attr"][0] ){
			return $program[$varname]["attr"][$varkey];
		}
	}

	return $var;

}


function operate($operation){

	global $program;

	$id = preg_split('/[^a-z0-9]/i', $operation, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$id = $id[0];

	

	if(substr($operation, 0,1) == "\""){
		echo "RENDER<br/>";
	}
	else{
		$program = update($operation);
		interpreter($id);
	}

}


// print "<pre>";
// print_r($program);
// print "</pre>";




?>