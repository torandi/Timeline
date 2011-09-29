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
			header("Location: ?id=".$db->lastInsertRowid()."&key=".$key);
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
		font-size: 10pt;
		background: white;
		border: 1px solid black;
		position: fixed;
		top: 10px;
		right: 10px;
		z-index: 10;
		padding: 5px;
	}

	#instructions {
		font-size: 10pt;
		width: 600px;
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
<div id="instructions">
<a href="#" onclick="$('#instructions_toggle').toggle('fast');return false">Hide/show instructions</a>
<div id="instructions_toggle" style="display: none;">
<h3>Instructions</h3>
<p>
	Click on a node to edit the node.<br/>
	Text and "Links" updates on change (leave the field to trigger)<br/>
	Links is where to draw the arrows from this node (specify ids)<br/>
</p>
<p>
<strong>Buttons:</strong>
</p>
<p>
<strong>Add branch:</strong> Adds a child on the level below, at the rightmost available position (this creates a branch if the current node already has a child). Next focus: Same</p><p>
<strong>Insert child:</strong> Inserts a child below this node. If the position below is empty the child is placed there, otherwise an empty level is created to make room for the child. Next focus: Create child</p><p>
<strong>Save:</strong> Saves the timeline, use url to load</p>
<strong>&lt;-/-&gt;:</strong> Move node left or right
</p>
<p style="font-size: 8pt">
Written by <a href="https://github.com/torandi">Andreas Tarandi</a><br/>
Powered by <a href="http://www.headjump.de/article/arrows-and-boxes">Arrows and Boxes</a>
</p>
</div>
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
		<input type="submit" id="move_left" value="<-"/>
		<input type="submit" id="move_right" value="->"/>
	</p>
	<p>
		<input type="submit" id="add_branch" value="Add branch"/>
		<input type="submit" id="add_child" value="Insert child"/>
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
