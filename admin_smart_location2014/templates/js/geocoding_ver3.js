   var geocoder;
   var map;
   var address;
   
   function doGeocode() {

	    geocoder = new google.maps.Geocoder();
	    var latlng = new google.maps.LatLng(-34.397, 150.644);
	    var myOptions = {
	      zoom: 15,
	      center: latlng,
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	    }
	    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	    address = $("#geocode_address").text();

	//   console.log(latlng);
	    
	    geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
            	
            	print_address(results);
          	
            	
            } else {
              alert("Geocode was not successful for the following reason: " + status);
            }
          });
    
			     
	    }

    function print_address(results, status){

        if (status == google.maps.GeocoderStatus.OK) {
            alert("住所が特定できませんでした");
          } else {
            
//	     var lng=results[0].geometry.location.c;
//	     var lat=results[0].geometry.location.b;

        	 //locationオブジェクトはLatlngクラス
        	 var lng=results[0].geometry.location.lng();
     	     var lat=results[0].geometry.location.lat();
	    
		 var lng_box = document.getElementById("lng");
		 var lat_box = document.getElementById("lat");

		 var textNode_lng = document.createTextNode(lng);
		 var textNode_lat = document.createTextNode(lat);
		 
		 var longitude = document.profile.longitude;
		 var latitude = document.profile.latitude;

		 
		 lng_box.appendChild(textNode_lng);
		 lat_box.appendChild(textNode_lat);

		 longitude.value = lng;		 
		 latitude.value = lat;		 
		 
         map.setCenter(results[0].geometry.location);
         var marker = new google.maps.Marker({
             map: map, 
             position: results[0].geometry.location
         });



          }
    } 

    function DisplayPropertyNames(place){
		var names="";
		for (var name in place) names +=name+ "\n";
		alert(names);
        
    }