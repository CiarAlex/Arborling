/** Arborling : Analayse arborée
 * 
 * Copyright 2016
 * Authors : Laurent VANNI, Alexandre CIARAFONI
 * License: GNU-GPL Version 3 or greater
 * 
 *  Arborling is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Arborling is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Arborling.  If not, see <http://www.gnu.org/licenses/>.
 */


var charge = 0; //Charge of the graph
var gravity = 0; //Gravity of the graph
var dataModal = 0; //Test calcul only one time when we open the modalBigTree

var ORIGIN_X;
var ORIGIN_Y = 100;
var RESIZE_FACTOR;

//Count number of spaces of the first line
var textMat = document.getElementById("textareaMatrice").innerHTML;
var textMatOrd = textMat.split("\n");
for(i = 0; i < 1; i++){
	res = textMatOrd[i].match(/ /g);
}

//Tree position in the SVG and resize
if(res.length < 8)
{
	ORIGIN_X = 200;
	RESIZE_FACTOR = 0.05;
}
else if(res.length >= 8 && res.length <= 30)
{
	ORIGIN_X = 400;
	RESIZE_FACTOR = 0.01;
}
else if(res.length > 30)
{
	ORIGIN_X = 1000;
	RESIZE_FACTOR = 0.003;
}

/**
 * Build the tree
 * @param tree_id : identifiant HTML correspondant à l'endroit où l'arbre doit être crée
 * @param width : width of the SVG including the tree
 * @param height : height of the SVG including the tree
 * @param svg_id : identifiant HTML correspondant au SVG
 */
function buildTree(tree_id, width, height, svg_id)
{
	var resize = 1,//Coefficient used to resize the tree
		maxvaluex = 0,
		maxvaluey = 0,
		minvaluex = 0,
		minvaluey = 0;
	
	$.each(coordonnee.nodes, function(index, value)
	{
		if(value.x-ORIGIN_X < minvaluex)
		{
			minvaluex = value.x-ORIGIN_X;
		}
		if(value.y-ORIGIN_Y < minvaluey)
		{
			minvaluey = value.y-ORIGIN_Y;
		}
	});
	
	$.each(coordonnee.nodes, function(index, value)
	{
		value.x = value.x - minvaluex;
		value.y = value.y - minvaluey;
		if(value.x >= maxvaluex)
		{
			maxvaluex = value.x;
		}
		if(value.y >= maxvaluey)
		{
			maxvaluey = value.y;
		}
	})
	
	//Tree resizing according to SVG height and width
	if(maxvaluex > width)
	{
		resize = width/maxvaluex - RESIZE_FACTOR;
	}
	if(maxvaluey * resize > height)
	{
		resize = height/maxvaluey - RESIZE_FACTOR;
	}
	
	var force = d3.layout.force()
		.size([width, height])
		.charge(charge)
		.gravity(gravity)
		//Calculates the length of each links
		.linkDistance(function(d)
			{
				var x = Math.abs(d.source.x - d.target.x);
				var y = Math.abs(d.source.y - d.target.y);
				var dist = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
				return dist;
			})
		.on("tick", tick);

	var drag = force.drag();

	//Creating of the SVG at the wanted location : tree_id
	var svgContainer = d3.select(tree_id).append("svg")
		.attr("width", width)
		.attr("height", height)
		.attr("id", svg_id);

	var rect = svgContainer.append("rect")
					    .attr("width", "100%")
					    .attr("height", "100%");
	
	var link = svgContainer.selectAll(".link"),
		node = svgContainer.selectAll(".node"),
		circle = svgContainer.selectAll(".circle"),
		text = svgContainer.selectAll(".text");

	(function()
	{
	  force
	      .nodes(coordonnee.nodes)
	      .links(coordonnee.links)
	      .start();

		link = link.data(coordonnee.links)
		    	   .enter().append("line");
		
		node = node.data(coordonnee.nodes)
			       .enter().append("g")
			       .attr("class", "node")
			       .call(drag);
		  
		circle = node.append("svg:circle");

		text = node.append("svg:text");
	})();

$('#newColorBranch, #newColorLeaf, #newColorText, #newColorBackground, #newColorCircle,' +
		'#textSize, #newCircleSize, #newLineSize, #checkboxTopo').on('change', function(){
	tick();
})

$('#buttonExitBigTree, #buttonBigTree').click(function(){
	tick();
})

var tabtarget = [];
//Table containing labels of all the targets
$.each(coordonnee.links, function(key, value)
{
	tabtarget.push(value.target.label);
})

var tabsource = [];
//Table containing labels of all the sources
$.each(coordonnee.links , function(index, value)
{
	tabsource.push(value.source.label);
})

/**
 * Function called for each movement in the tree, perform the dynamic processing
 */
	function tick()
	{
		var textSize = document.getElementById('textSize').value; //Size of the text
		var colorBranch = document.getElementById('newColorBranch').value; // Color of internal branches
		var colorLeaf = document.getElementById('newColorLeaf').value; // Color of external leafs
		var colorText = document.getElementById('newColorText').value; //Color of the text
		var colorCircle = document.getElementById('newColorCircle').value; //Color of the circles
		var circleSize = document.getElementById('newCircleSize').value; // Size of the circles
		var lineSize = document.getElementById('newLineSize').value; //Size of the lines
		var colorBackground = document.getElementById('newColorBackground').value;//Color of the SVG background
		var checkTopo = $('#checkboxTopo')[0].checked;//Get the status of the checkbox
		
		var color,
			label;
		var tab_color = [];
		var i = -1,
			a = 0;
		
		var valuefatherofall;
		//Search the father of all
		$.each(coordonnee.links, function(key, value)
		{
			//If the source is not a target
			if(jQuery.inArray(value.source.label, tabtarget) == -1)
			{
				valuefatherofall = value.source.x;
			}
		})
		
		rect.attr("fill", colorBackground);
		
		//Table containing colors of links
		$.each(coordonnee.links , function(key, value)
		{
			//If the target is not a source
			if(jQuery.inArray(value.target.label, tabsource) == -1)
			{
				a++;
				color = colorLeaf;
			}
			else
			{
				color = colorBranch;
			}
			tab_color.push(color);
		})
		
		//Building of links
		link.attr("x1", function(d) { return d.source.x * resize; })
	      	.attr("y1", function(d) { return d.source.y * resize; })
	      	.attr("x2", function(d) { return d.target.x * resize; })
	      	.attr("y2", function(d) { return d.target.y * resize; })
	      	.attr("stroke", function(d){
				i++;
				return tab_color[i];
	    	})
	    	.attr("stroke-width", lineSize);
		
		circle.attr("cursor", "pointer")
			  .attr("r", circleSize)
			  .attr("fill", colorCircle)
			  .attr("stroke", colorCircle);
		
		var tabpos = [],
			tabpositext = [];
		var m = -1,
			n = -1;
		$.each(coordonnee.nodes, function(key, value)
		{
			var posx = "start";
			var positext = 2;
			positext = positext - -circleSize;
			if(value.x < valuefatherofall)
			{
				posx = "end";
				positext = -2;
				positext = positext - circleSize;
			}
			tabpositext.push(positext);
			tabpos.push(posx);
		})
		
		//Location of circles and direction of text
		node.attr("transform", function(d) { return "translate(" + d.x * resize + "," + d.y * resize + ")"; })
		  .attr("text-anchor", function(d)
		      {
	    		 m++;
	    		 return tabpos[m];
		      });
		
		var b = 0,
			k = 0;
		//Location and size of labels
		text.attr("cursor", "pointer")
		   .text(function(d)
			{
			   //Display topology option
			   if(checkTopo == false)
			   {
				   if(b < a)
				   {
					   if(b < labels.length)
					   {
						   	label = labels[b];
						   	b++;
						   	return label;
					   }
					   else
					   {
						   b++;
						   return d.label;
					   }
				   }
				   else
				   {
					   label = "";
					   return label;
				   }
			   }
			   else
			   {
				   if(k < labels.length)
				   {
					   	label = labels[k];
					   	k++;
					   	return label;
				   }
				   else
				   {
					   k++;
					   if(d.label == "")
					   {
						   label = labels[k];
						   return label;
					   }
					   else
					   {
						   labels.push(d.label + 1);
						   return d.label + 1;
					   }
				   }
			   }
			})
		   .attr("x", function(d)
			{
				n++;
				return tabpositext[n];
			})
			.attr("font-size", textSize)
			.attr("fill", colorText)
			.on("dblclick", rename);
	}//END TICK()
	
	//Rename function when double click on a word
	function rename(d)
	{
		$("#modalEditText").foundation('open');
		document.getElementById('inputRename').value = "";
		document.getElementById('inputRename').focus();
		$("#buttonRename").click(function(){
			var newText = document.getElementById('inputRename').value;
			if(newText !== null && newText !== "")
			{
				labels[d.label] = newText;
				d = "";
			}
			if(dataModal == 0)
			{
				var wi = innerWidth * 0.95;
				var he = innerHeight * 0.95;
				buildTree('#modalBigTree', wi, he, "bigTree");
				dataModal = 1;
			}
			$("#modalBigTree").foundation('open');
			tick();
		})
	}
}

/**
 * Function called in HTML to create the tree
 * draggable allow to move an element in a container
 */
function arboree()
{
	var wi = innerWidth * 0.50;
	var he = innerHeight * 0.60;
	buildTree('#littleTree',wi,he, "svglittleTree");
	document.getElementById('buttonBigTree').style.display = 'block';
	$("#log").draggable({"cursor": "move", "handle" : "#topLog", "cancel": "#botLog", "containment": "#container"});
}

/**
 * jQuery events
 */
$("#buttonLog").click(function()
{
	if(document.getElementById("svglittleTree"))
	{
		document.getElementById('log').style.display = 'block';
	}
});

$("#buttonExitLog").click(function()
{
	 document.getElementById('log').style.display = 'none';
});

//A joyride is several modal pointing the elements you want and allow you to explain what the element pointed did
$("#buttonHelp").click(function()
{
	$("#joyrideHelp").joyride
	({
		autoStart : true,
		modal : true
	});
});

//Open a bigger SVG in a full screen modal
$("#buttonBigTree").click(function()
{
	if(dataModal == 0)
	{
		var wi = innerWidth * 0.95;
		var he = innerHeight * 0.95;
		buildTree('#modalBigTree', wi, he, "bigTree");
		dataModal = 1;
	}
});

//Set the options movable
$("#options").draggable({"containment": "#modalBigTree"});

//Save the tree as PNG
$("#buttonSaveTree").click(function(){
	var svg = document.querySelector( "#bigTree" );
	var svgData = new XMLSerializer().serializeToString( svg );

	var canvas = document.createElement( "canvas" );
	var svgSize = svg.getBoundingClientRect();
    canvas.width = svgSize.width;
    canvas.height = svgSize.height;
	var ctx = canvas.getContext( "2d" );
	var img = document.createElement( "img" );
	img.setAttribute( "src", "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent( svgData ))) );
	img.onload = function() {
	    ctx.drawImage( img, 0, 0 );
	    var ladate = new Date();
	    var datejour = ladate.getHours() + "h" + ladate.getMinutes() + "m" + ladate.getSeconds() + "s--" + ladate.getDate() + "-" + (ladate.getMonth()+1) + "-" + ladate.getFullYear();
	    var a = document.createElement("a");
		  a.download = "export_tree"+ datejour +".png";
		  a.href = canvas.toDataURL( "image/png" );
		  document.body.appendChild(a);
		  a.click();
	};
})

//Modal error
if(errorMatrix == 'error')
{
	$('#modalError').foundation('open');
}

//ButtonSend is hidden during calculations
$("#buttonSend").click(function()
{
	$(this).fadeOut(500); // Fades out send button when it's clicked
});