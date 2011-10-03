var row=0
var id2=0
var show_id=false

function redraw() {
	str = ""
	$.each(items, function(i, r) {
		$.each(r, function(i2, item) {
			if(item[2].length > 0) {
				links = " > ["+item[2].join(",")+"]"
			} else {
				links = ""
			}
			text = "{{<span data-row='"+i+"' data-id2='"+i2+"'>"+item[1]+(show_id?" ("+item[0]+")":"")+"</span>}} "
			if(item[1]=="") {
				text=""
			}
			item_content = "("+item[0]+":"+text+links+") "
			if(i == row && i2 == id2) {
				item_content = "("+item_content+")" //Add hilight
			}
			str+=item_content
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
		$(".arrowsandboxes-node.arrowsandboxes-node-highlighted").removeClass("arrowsandboxes-node-highlighted")
		row = $(this).find("span").data("row")
		id2 = $(this).find("span").data("id2")
		$(this).addClass("arrowsandboxes-node-highlighted")
		update_controls()
	})



	$("#add_child").live('click',function(event) {
		event.stopPropagation()
		if(items[row+1] == undefined) {
			items[row+1] = new Array()
		}
		if(items[row+1][id2] && items[row+1][id2][1].length > 0) {
			before = items
			//Insert empty row
			last = new Array()
			len = items.length+1
			for(i=row+1; i<len;++i) {
				tmp = items[i]
				items[i] = last
				last = tmp
			}
		}
		new_id = set_item(row+1,id2,"[new]")
		items[row][id2][2].push(new_id)

		row++
		update_controls()
		redraw()
		return false
	})


	$("#add_branch").live('click',function(event) {
		event.stopPropagation()
		if(items[row+1] == undefined) {
			items[row+1] = new Array()
		}
		col = 0
		while(items[row+1][col] && items[row+1][col][1].length > 0) {
			col++
		}
		new_id = set_item(row+1, col,"[new]")
		items[row][id2][2].push(new_id)
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
		redraw()
	})

	$("#delete").click(function() {
		items[row][id2][1] = ""
		items[row][id2][2] = []
		redraw()
		return false
	})

	$("#move_right").click(function() {
		col = id2+1
		while(items[row][col] && items[row][col][1].length > 0) {
			col++
		}
		if(items[row][col]) {
			cur_id = items[row][col][0]
		} else {
			cur_id = next_id++
		}
		items[row][col] = items[row][id2]
		items[row][id2]= [cur_id, "", []]
		id2=col
		redraw()
		return false
	})

	$("#move_left").click(function() {
		col = id2-1
		while(col >= 0 && items[row][col] && items[row][col][1].length > 0 ) {
			col--
		}
		if(col < 0)
			return false
		if(items[row][col]) {
			cur_id = items[row][col][0]
		} else {
			cur_id = next_id++
		}
		items[row][col] = items[row][id2]
		items[row][id2]= [cur_id, "", []]
		id2=col
		redraw()
		return false
	})

	update_controls()
})
