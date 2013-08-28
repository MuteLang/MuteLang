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

	preg_match('#\[(.*?)\]#', $value, $attr);
	$attr = $attr[1];
	preg_match('#\((.*?)\)#', $value, $cond);
	$cond = $cond[1];
	preg_match_all('#\{(.*?)\}#', $value, $oper);
	$oper = $oper[1];

	$program[$id]["name"] = $id;
	$program[$id]["attr"] = $attr;
	$program[$id]["cond"] = $cond;
	$program[$id]["oper"] = $oper;

	interpreter($program[$id]);
	$line++;

}

// =======================
// Interpreter
// =======================

function interpreter($run){

	// =======================
	// Parameters
	// =======================

	$run["attr"] = explode(",", $run["attr"]);

	// =======================
	// Conditions
	// =======================

	$run["variables"] = preg_split('/[^a-z0-9.]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	$run["operators"] = preg_split('/[a-z0-9.]/i', $run["cond"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	// =======================
	// Operations
	// =======================

	echo "<h2>Program ".$run['name']."</h2>";
	echo "<pre>";
	print_r($run);
	echo "</pre>";

}




?>