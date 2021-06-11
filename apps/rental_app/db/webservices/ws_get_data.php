<?php
require_once "functions.php";

header("Content-Type: application/json; charset=UTF-8");

if (isset($_GET['search'])) {
	$search = htmlspecialchars($_GET['search']);
} else {
	$search = "";
}
if (isset($_GET['id'])) {
	$id = htmlspecialchars($_GET['id']);
} else {
	$id = null;
}
if (isset($_GET['limit'])) {
	$limit = htmlspecialchars($_GET['limit']);
} else {
	$limit = "0";
}
if (isset($_GET['offset'])) {
	$offset = htmlspecialchars($_GET['offset']);
} else {
	$offset = "0";
}
if (isset($_GET['type'])) {
	$type = htmlspecialchars($_GET['type']);
} else {
	$type = null;
}
if (isset($_GET['ord'])) {
	$ord = htmlspecialchars($_GET['ord']);
} else {
	$ord = 0;
}
if (isset($_GET['parentID'])) {
	$parentID = htmlspecialchars($_GET['parentID']);
} else {
	$parentID = null;
}
if (isset($_GET['mode'])) {
	$mode = htmlspecialchars($_GET['mode']);
} else {
	$mode = 0;
}
if ($type == "null") {
	$type = null;
}
if ($parentID == "null") {
	$parentID = null;
}

function safe_json_encode($value, $options = 0, $depth = 512, $utfErrorFlag = false)
{
	$encoded = json_encode($value, $options, $depth);
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			return $encoded;
		case JSON_ERROR_DEPTH:
			return 'Maximum stack depth exceeded';
		case JSON_ERROR_STATE_MISMATCH:
			return 'Underflow or the modes mismatch';
		case JSON_ERROR_CTRL_CHAR:
			return 'Unexpected control character found';
		case JSON_ERROR_SYNTAX:
			return 'Syntax error, malformed JSON';
		case JSON_ERROR_UTF8:
			$clean = utf8ize($value);
			if ($utfErrorFlag) {
				return 'UTF8 encoding error';
			}
			return safe_json_encode($clean, $options, $depth, true);
		default:
			return 'Unknown error';
	}
}

function utf8ize($d)
{
	if (is_array($d)) {
		foreach ($d as $k => $v) {
			unset($d[$k]);
			$d[utf8ize($k)] = utf8ize($v);
		}
	} else if (is_object($d)) {
		$objVars = get_object_vars($d);
		foreach ($objVars as $key => $value) {
			$d->$key = utf8ize($value);
		}
	} else if (is_string($d)) {
		return iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($d));
	}
	return $d;
}

try {
	$sql = "";
	if (!(is_null($id))) {
		$sql = "select * from objects where id=:id ";
	} else {
		$sql = "select * from objects where id>0 ";

		if (!(is_null($type))) {
			$sql = $sql . " and object_type=:type ";
		}
		if (strlen($search) > 0) {
			$sql = $sql . "  and id in (select id from objects_ft where objects_ft match '" . $search . "') ";
		}
		if ($mode == 1) {
			$sql = $sql . " and objects_id is null ";
		} elseif ($mode == 2) {
			if (!(is_null($parentID))) {
				$sql = $sql . " and objects_id=:parentID ";
			}
		}
		if ($ord == 0) {
			$sql = $sql . " order by id desc ";
		} else if ($ord == 1) {
			$sql = $sql . " order by id ";
		} else if ($ord == 2) {
			$sql = $sql . " order by name ";
		} else if ($ord == 3) {
			$sql = $sql . " order by event_year,event_month, event_day ";
		}
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
			if ($offset > 0) {
				$sql = $sql . " OFFSET " . $offset;
			}
		}
	}

	$stmt = $dbCo->prepare($sql);
	if (!(is_null($id))) {
		$stmt->bindParam(':id', $id);
	} else {
		if (!(is_null($type))) {
			$stmt->bindParam(':type', $type);
		}
	}
	if (($mode == 2) && (!(is_null($parentID)))) {
		$stmt->bindParam(':parentID', $parentID);
	}
	$stmt->execute();
	$return = "";
	$rows = [];
	while ($row = $stmt->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT)) {
		$rows[] = $row;
	}
	if ($offset > 0) {
		sleep(1);
	}
	echo safe_json_encode($rows);
	return;
} catch (PDOException $e) {
	echo $e->getMessage();
	return;
}
