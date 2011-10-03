<?
	require_once('db.php');

	$version = file_get_contents("timeline.db.version");
	if($version!==false) {
		switch($version) {
			case "0":
				$q = $db->query("ALTER TABLE timelines ADD COLUMN version INTEGER DEFAULT(0)");
				if(!$q) die($q->lastErrorMsg());
				break;
		}
	} else {
		$q = $db->query("CREATE TABLE IF NOT EXISTS timelines (id INTEGER PRIMARY KEY, timeline string, key string, next_id int, version int DEFAULT(0))");
		if(!$q) die($q->lastErrorMsg());
	}
	file_put_contents("timeline.db.version","1");
	/*
	 * Database versions:
	 * 0: (id INTEGER PRIMARY KEY, timeline string, key string, next_id int)
	 * 1: (id INTEGER PRIMARY KEY, timeline string, key string, next_id int, version INT default 0)
	 */
	echo "Db updated";
?>
