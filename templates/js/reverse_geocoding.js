//営業所をGooglemapにアイコン表示するJS
	var lat;
	var long;		
	var geocoder;
	var map;
	var infowindow = new google.maps.InfoWindow();
	var image = 'templates/image/branch_icon.gif';			    
	var taxi_icons=new Array();
	var taxi_windows=new Array();
	//スマートフォンか、PCか判断　ガラケーはここ以前で判別しているので必要なし
	if ((navigator.userAgent.indexOf('iPhone') > 0 && navigator.userAgent.indexOf('iPad') == -1) || 
				navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0) {
		var smart_phone=1;
	}else{
		var smart_phone=0;			
	}
				
	function initialize(lat,long) {
			
		geocoder = new google.maps.Geocoder();
			
	    var myLatlng = new google.maps.LatLng(lat, long);
	    
	    var myOptions = {
				      zoom: 14,
				      center: myLatlng,
				      mapTypeId: google.maps.MapTypeId.ROADMAP
				    }
	    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				    
	    geocoder.geocode({'latLng': myLatlng}, function(results, status) {
	    	console.log(results[1]);
	    	
	           if (status == google.maps.GeocoderStatus.OK) {
		              if (results[2]) {
			                marker = new google.maps.Marker({
			                    map: map, 
			                    position: myLatlng
			                });
			                
			                var info_address=results[1].formatted_address.split(",",2);

				            $("#present_location1").text(info_address[1]);
				            $("#present_location_form").val(info_address[1]);
			                $("#present_location").text(info_address[1]);
				             infowindow.setContent(info_address[1]);
				            
							if(smart_phone){
								
								google.maps.event.addListener(marker, 
																'mousedown', 
													function() {
						             					infowindow.open(map, marker);
													});
							}else{
								google.maps.event.addListener(marker, 
															'mouseover', 
													function() {
	             										infowindow.open(map, marker);
													});
											
								google.maps.event.addListener(marker, 
															'mouseout', 
													function() {
														infowindow.close(map, marker);
													});
							}

				            
				  
				            
			              }
			            } else {
			              alert("Geocoder failed due to: " + status);
			            }
			          });



		for (var i = 0; i < locationArray.length; i++) {
						
				taxi_icons=new google.maps.Marker({
			    	  position: locationArray[i].LatLng,
			          map: map,
			          icon:image,
			          title:locationArray[i].company_name

				   	});
				   	
		var contentString1 = '<div id="content">'+locationArray[i].number+':'+locationArray[i].company_name ;
		var contentString2 = '<br><a href="tel:'+ locationArray[i].tel + '">'+locationArray[i].tel +'</a></div>';
		var contentString=contentString1+contentString2;										
		taxi_windows= new google.maps.InfoWindow({
										maxWidth:500,
										content: contentString
										
								});


		attachInfoWindow(taxi_icons,taxi_windows,i,smart_phone);
	
		};
		

		function attachInfoWindow(taxi_icons,taxi_windows,i,smart_phone){
				if(smart_phone){
				
					google.maps.event.addListener(taxi_icons, 
													'mousedown', 
										function() {
											taxi_windows.open(map,taxi_icons);
										});
				}else{
					google.maps.event.addListener(taxi_icons, 
												'mouseover', 
										function() {
											taxi_windows.open(map,taxi_icons);
										});
								
					google.maps.event.addListener(taxi_icons, 
												'mouseout', 
										function() {
											taxi_windows.close(map,taxi_icons);
										});
				}
		}


	}