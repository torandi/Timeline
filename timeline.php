<?
	$db = new SQLite3('timeline.sqlite');
	$q = $db->query("CREATE TABLE IF NOT EXISTS timelines (id INTEGER PRIMARY KEY, timeline string, key string, next_id int)");
	if(isset($_POST['src'])) {
		if(isset($_GET['key'])) {
			$db->query("UPDATE timelines SET timeline='".$_POST['src']."', next_id='".$_POST['next_id']."' WHERE key='".$_GET['key']."' AND id='".$_GET['id']."';");
		} else {
			$key = random_string();
			$stmt = "INSERT INTO timelines (id,timeline, key, next_id) VALUES (NULL,'".$_POST['src']."','".$key."', '".$_POST['next_id']."')";
			$q = $db->query($stmt);
			if(!$q) die($error);
			header("Location: timeline.php?id=".$db->lastInsertRowid()."&key=".$key);
		}
	}

	function random_string ($length = 8)
	{
	  $randstr = "";

	  $possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGIJKLMNOPQRSTUVWXYZ"; 

	  for ($i=0;$i<$length;$i++) { 
		 $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		 $randstr .= $char;
	  }
	  return $randstr;
	}

?>
<html>
<head>
<title>Timeline tool</title>
<script type="text/javascript" src="jquery.min.js"></script>
<link href="arrowsandboxes.css" rel="stylesheet" type="text/css" />
<script src="jquery_wz_jsgraphics.js" type="text/javascript"></script>
<script src="arrowsandboxes.js" type="text/javascript"></script>

<style>
	#controls {
		background: white;
		border: 1px solid black;
		position: fixed;
		top: 10px;
		right: 10px;
		z-index: 10;
		padding: 5px;
	}
</style>
<script type="text/javascript">
var items = <?	
	if(isset($_GET['id'])) {
		$result = $db->querySingle("SELECT * FROM timelines WHERE id='".$_GET['id']."' AND key='".$_GET['key']."'", true);
		echo $result['timeline'];
		$next_id = $result['next_id'];
	} else {
		echo "[ [ [0, '[new]', [] ] ] ]";
		$next_id = 1;
	}
?>

var next_id = <?=$next_id?>

</script>
<script type="text/javascript" src="timeline.js">
</script>
</head>
<body>
<h1>Timeline tool</h1>
<div>
Instruktioner
</div>
<hr/>
<div id="controls">
<form method="post" action="">
	<p>
		<input type="checkbox" id="show_id"/>
		<label for="show_id">Show ID</label>
	</p>
	<p>
		<strong>ID: </strong>
		<span id="id"></span>
	</p>
	<p>
		<label for="content">Text: </label>
		<input type="text" id="content"/>
	</p>
	<p>
		<label for="content">Links: </label>
		<input type="text" id="links"/>
	</p>
	<p>
		<input type="submit" id="add_branch" value="Add branch"/>
		<input type="submit" id="add_child" value="Add child"/>
		<input type="submit" id="delete" value="Delete"/>
	</p>
	<p>
		<input type="hidden" name="src" id="src"/>
		<input type="hidden" name="next_id" id="next_id"/>
		<input type="submit" value="Save"/>
	</p>
</form>
</div>
<div id="timeline">
<pre class="arrows-and-boxes">  </pre> 
</div>
</body>
</html>
