var over = "bo";
var over2 = "bo_wh";
var focused = "bo co";	
$(document).ready(function(){				   			   
	$(".btn01").live("mouseover", function(){ $(this).addClass(over); });
	$(".btn01").live("mouseout", function(){ $(this).removeClass(over); });
	$(".th_vid_m i").live("mouseover", function(){ $(this).addClass(over2); });
	$(".th_vid_m i").live("mouseout", function(){ $(this).removeClass(over2); });	
	$("input").live("focus", function(){ $(this).addClass(focused); });		
	$("textarea").live("focus", function(){ $(this).addClass(focused); });		
	$("input").live("blur", function(){ $(this).removeClass(focused); });		
	$("textarea").live("blur", function(){ $(this).removeClass(focused); });			
});	