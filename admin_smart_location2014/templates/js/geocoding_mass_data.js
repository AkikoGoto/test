/*大量データ投入用　ジオコーディング*/

   var geocoder;
   var map;
   var address;
   
   function doGeocode() {

	    geocoder = new google.maps.Geocoder();
	    address = $("#geocode_address").text();

        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
    
            	print_address(results);
          	
            	
            } else {
    //          alert("Geocode was not successful for the following reason: " + status);

            	//緯度経度が取得できなかった場合、最初のアクションに戻るループ
            	location.href="index.php?action=prepare_mass_geocode";

            }
          });
    
			     
	    }

    function print_address(results, status){

        if (status == google.maps.GeocoderStatus.OK) {
            alert("住所が特定できませんでした");
          } else {
            
	     var lng=results[0].geometry.location.c;
	     var lat=results[0].geometry.location.b;
	     
	//     console.log(lng);
	 //    console.log(lat);
	     	    
          }
	     location.href="index.php?action=putindb_mass_geocode&long="+lng+"&lat="+lat;

    } 

