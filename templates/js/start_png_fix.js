function start_png_fix() {
	$(window).load(function(){
		//class="pngfix"内の画像全てを半透明にする
		$(".pngfix").pngFix();
		});
 }
 
window.onload = start_png_fix;