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
	<link href="http://www.headjump.de/stylesheets/arrowsandboxes.css" rel="stylesheet" type="text/css" />
	<script src="http://www.headjump.de/javascripts/jquery_wz_jsgraphics.js" type="text/javascript"></script>
	<script src="http://www.headjump.de/javascripts/arrowsandboxes.js" type="text/javascript"></script>

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

var row=0
var id2=0
var show_id=false

function redraw() {
	str = ""
	$.each(items, function(i, row) {
		$.each(row, function(i2, item) {
			if(item[2].length > 0) {
				links = " > ["+item[2].join(",")+"]"
			} else {
				links = ""
			}
			text = "{{<span data-row='"+i+"' data-id2='"+i2+"'>"+item[1]+(show_id?" ("+item[0]+")":"")+"</span>}} "
			if(item[1]=="") {
				text=""
			}
			str+="("+item[0]+":"+text+links+") "
		})
		str+=" || "
	})
	$("#timeline").empty().append("<pre class='arrows-and-boxes'>"+str+"</pre>")
	$("#timeline .arrows-and-boxes").arrows_and_boxes()
	$("#src").val(items.toSource())
	$("#next_id").val(next_id)
}

function set_item(row, col, val) {
	if(items[row].length < col) {
		for(i = items[row].length;i<col;++i) {
			items[row][i] = [next_id++, "", [] ]
		}
	}
	if(items[row][col] == undefined) {
		items[row][col] = [next_id,val,[] ]
		return next_id++
	} else {
		items[row][col][1] = val
		return items[row][col][0]
	}
}

function update_controls() {
	item = items[row][id2]
	$("#content").val(item[1])
	$("#id").html(item[0])
	$("#links").val(item[2].join(","))
}

$(function() {
	redraw()

	$(".arrowsandboxes-node").live('click',function() {
		row = $(this).find("span").data("row")
		id2 = $(this).find("span").data("id2")
		update_controls()
	})



	$("#add_branch").live('click',function(event) {
		event.stopPropagation()
		if(items[row+1] == undefined) {
			items[row+1] = new Array()
			col = id2
		} else if(items[row][id2][2].length == 0) {
			col = id2
		}	else {
			col = id2
			while(items[row][col] && items[row][col][2].length > 0) {
				++col
			}
		}	
		new_id = set_item(row+1, col,"[new]")
		items[row][id2][2].push(new_id)
		row++
		id2 = col
		update_controls()
		redraw()
		return false
	})

	$("#content").change(function() {
		items[row][id2][1] = $("#content").val()
		redraw()
	})

	$("#links").change(function() {
		try {
			items[row][id2][2] = eval("["+$("#links").val()+"]")
			redraw()
		} catch (e) {
			alert("Parse error in links: "+e)
		}
	})

	$("#show_id").change(function() {
		show_id = $("#show_id").attr("checked")
		redraw();
	})

	update_controls()
})
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
