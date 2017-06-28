	function doGeocode(){
//        var map = null;
 //       var geocoder=new GClientGeocoder();
        
        var geocoder;
        var map;
        
        var address = document.getElementById("geocode_address").textContent;

	    geocoder = new google.maps.Geocoder();

        geocoder.getLocations(address, print_address);
	    

	    
	    //
	//	var map = new GMap2(document.getElementById("map_canvas"));
    	var map = new google.maps.Map(document.getElementById("map_canvas"));
		
			     if (geocoder) {
			         geocoder.getLatLng(
			           address,
			           function(point) {
			             if (!point) {
			               alert(address + " not found");
			             } else {
			               map.setCenter(point, 13);
			               var marker = new GMarker(point);
			               map.addOverlay(marker);
			               marker.openInfoWindowHtml(address);
			             }
			           }
			         );
			     }
	    }

    function print_address(response){

        if (!response || response.Status.code != 200) {
            alert("èZèäÇì¡íËÇ≈Ç´Ç‹ÇπÇÒÇ≈ÇµÇΩÅB");
          } else {
            
	    place = response.Placemark[0];

	     var lng=place.Point.coordinates[0];
	     var lat=place.Point.coordinates[1];
	    
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

          }
    } 

    function DisplayPropertyNames(place){
		var names="";
		for (var name in place) names +=name+ "\n";
		alert(names);
        
    }