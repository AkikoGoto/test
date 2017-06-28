$(document).ready(function(){
	
/*
	$(".nav")
	.superfish({
	//	animation : { opacity:"show",height:"show", delay:0, speed:"fast"}
	})
	.find(">li:has(ul)")
		.mouseover(function(){
	
		})
		.find("a")
			.focus(function(){
			});
	
*/
	$('#navigation').slimmenu(
			{
			    resizeWidth: '800',
			    collapserTitle: 'メニュー',
			    animSpeed: 'medium',
			    easingEffect: null,
			    indentChildren: false,
			    childrenIndenter: '&nbsp;'
			});
	
});


