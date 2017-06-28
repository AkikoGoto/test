function routeAlarm(information){
	
	$('.alarm').show("fast");
	$('.alarm_message').text("ルートの外に出ている車両があります。車両 ： " + information.car_type + ", ドライバー名 : " + information.driver_name);
	
    var audio = new Audio('files/alarm_sound.wav');
    audio.addEventListener('ended', function() {
    	    this.currentTime = 0;
    	    this.play();
    	}, false);
    audio.play();
     
    $('#pause_music').click(function(e) {
    	 audio.pause();
    	});
     
    $('#close_alarm').click(function(e) {
    	 audio.pause();
    	 $('.alarm').hide();
    	});
}