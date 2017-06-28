
var getQueryParams = function() {
    var queryString = window.location.search;
    queryString = queryString.substring(1);
    var params = {}, queries, temp, i, l;
 
    // Split into key/value pairs
    queries = queryString.split("&");
 
    // Convert the array of strings into an object
    for ( i = 0, l = queries.length; i < l; i++ ) {
        temp = queries[i].split('=');
        params[temp[0]] = temp[1];
    }
 
    return params;
};

function getPHPSessionID() {

    var phpSessionId = document.cookie.match(/PHPSESSID=[A-Za-z0-9]+/i);
    
    console.log(document.cookie);
    console.log(phpSessionId);
    if(phpSessionId == null) 
        return '';

    if(typeof(phpSessionId) == 'undefined')
        return '';

    if(phpSessionId.length <= 0)
        return '';

    phpSessionId = phpSessionId[0];

    var end = phpSessionId.lastIndexOf(';');
    if(end == -1) end = phpSessionId.length;

    return phpSessionId.substring(10, end);
}

var zoomed = false;
var centerChanged = false;
var cid,key,branch_id,show_destinations;
var map, markers, directionMarkers;

var realtimeMap = function(config) {	

  var phpSessionID = getPHPSessionID();
  var socket = io.connect(socketio_server_host, {path:socketio_server_path, query:'phpSessionID=' + phpSessionID });

  // var socket = io.connect(location.origin);

    // on connection to server, ask for user's name with an anonymous callback
  socket.on('connect', function(){
    	var data = getQueryParams();
    	console.log(data.company_id);
    	data.cid = data.company_id;
        data.bid = data.branch_id;
        
        data.lat = company_lat;
        data.lng = company_long;
        
        console.log('SOCKET CONNECT');
        console.log(data);
        
    	if(typeof data.lat == "undefined") data.lat = 35.681325; // default
																	// Tokyo
																	// Station
        if(typeof data.lng == "undefined") data.lng = 139.766817;
        if(typeof data.cid == "undefined") data.cid = 9841;
        if(typeof data.zoom == "undefined") data.zoom = 13;
        cid = data.cid;
        key = data.key;
        branch_id = data.branch_id || 0;
        show_destinations = data.show_destinations || 0;
        if(!map) {
        	
        		mapboxgl.accessToken = 'pk.eyJ1Ijoib25saW5lY29uc3VsdGFudCIsImEiOiJ0NXNSdE1VIn0.48aKT-tYUwPSibdAXP_NAQ'; 

	        	if (!mapboxgl.supported()) {
	        		
	        	    alert('このブラウザではMapbox GLを表示することができません。違うブラウザをお試しください。');
	        	
	        	} else {
	        		
	        		map = new mapboxgl.Map({
	        		    container: 'map',
	        		  	style: 'mapbox://styles/onlineconsultant/cj07xj7gd000p2spl15ijo2k8',
	        		    center: [data.lng, data.lat ],
	        		    zoom: 17
	        		});				
	        					
	        		map.addControl(new mapboxgl.NavigationControl());
	        		
	        		map.addControl(new mapboxgl.ScaleControl({
	        	   		maxWidth: 80
	        		}));
	        		map.addControl(new mapboxgl.ScaleControl({
	        	   		maxWidth: 80,
	            		unit: 'imperial'
	        		}));
	        		
	        		if(routes !=undefined && routes.length > 0){
	        			addRoutes(routes);
	        		}
	        		
	        		if(showDestination != undefined && showDestination == true){
	        		    addCircleAndSymbolMarker(destinationDatas, "destinations", colorList ) ;
	        		    makeMapboxPopup('destinations');	
	        		}
	        		
	        	}	        	
	        	
   	
	        	markers = {};
	        	directionMarkers = {};
	        	
        }
        socket.emit('startTrackings', data);

             
        // TODO 後で直す
     /*
		 * map.on('move', function() { console.log('CENTER CHANGED');
		 * centerChanged = true; return; });
		 */
  });
    
  socket.on('trackings', function(encoded) {

	  	console.log('SOCKET TRACKINGS');
	    var data = encoded; 
	    console.log(data);
	    if(centerChanged) {
	      centerChanged = false;
	      var request = {};
	      var center = map.getCenter();
	      request.cid = cid;
	      request.lat = center.lat;
	      request.lng = center.lng;
	      request.key = key;
	      request.branch_id = branch_id;
	      console.log(request);
	      socket.emit('startTrackings', request);
	      return;
	    }
		    // console.log(data);
		var currentdate = new Date(); 
	    var lastUpdated = currentdate.getFullYear() + "/"
	      + ('0' + (currentdate.getMonth()+1)).slice(-2)  + "/"
	      + ('0' + currentdate.getDate()).slice(-2) + " "
	      + ('0' + currentdate.getHours()).slice(-2) + ":"
	      + ('0' + currentdate.getMinutes()).slice(-2) + ":"
	      + ('0' + currentdate.getSeconds()).slice(-2);
	    $('#updated').text(lastUpdated);
	
		console.log('last update:' + lastUpdated);

        var latest = Math.round(new Date().getTime() / 1000) - 180; 


        for(var driver_id in markers) {
        	
          var targetMarker = markers[driver_id];
          var update = data[driver_id];

          if(update == undefined) {
            // Delete old marker
            console.log('delete driver[' + driver_id + ']');
            targetMarker.remove();
            delete markers[driver_id];
            
            if(driver_id in directionMarkers) {
            	
           	  directionMarkers[driver_id].remove();
              delete directionMarkers[driver_id];
            }
          } else {
            // Update marker
              var latlng = markers[driver_id].getLngLat();
              if(driver_id in directionMarkers) {
            	  
            	  var directionMarker = directionMarkers[driver_id];
            	  // console.log(directionMarker);
	                if(!update.direction){
	                
	                	console.log('remove dirction driver[' + driver_id + ']');
	                  
	                	// map.removeLayer(directionMarkers[driver_id]);
	                	directionMarkers[driver_id].remove();
	                	
	                	delete directionMarkers[driver_id];
	                
	                } else if(directionMarkers[driver_id].options != undefined && update.direction != directionMarkers[driver_id].options.angle) {
	                	
	                  console.log('update dirction driver[' + driver_id + ']' + directionMarkers[driver_id].options.angle + '->' + update.direction);
	                  
	                  directionMarkers[driver_id].remove();
	                  // TODO
	                  directionMarkers[driver_id] = addEachArrow(update.direction, [update.longitude, update.latitude]);
	                
	                } else if(latlng.lat != update.latitude || latlng.lng != update.longitude) {
	                
	                	console.log('move dirction driver[' + driver_id + '] direction:'+update.direction);
	                  
	                	directionMarkers[driver_id].setLngLat(new mapboxgl.LngLat(update.longitude, update.latitude));
	                }
                
              } else if(update.direction) {
                  console.log('add dirction driver[' + driver_id + ']');

                  
                  directionMarkers[driver_id] = addEachArrow(update.direction, [update.longitude, update.latitude]);
                  
              } 
              
              // ルート外れ
              if(markers[driver_id].info.is_deviated != update.is_deviated) {
                  
            	  console.log('ルートはずれを更新');
            	  markers[driver_id].remove();
            	  markers[driver_id] = addMarker(update);
               
              }
              
              
              if(latlng.lat != update.latitude || latlng.lng != update.longitude) {
              
            	  console.log('move driver[' + driver_id + ']');
            	  markers[driver_id].setLngLat(new mapboxgl.LngLat(update.longitude, update.latitude));
               
              }


              if(markers[driver_id].info.status != update.status) {
            	  console.log('status update driver[' + driver_id + ']');
            	  markers[driver_id].remove();
            	  delete markers[driver_id];
            	  markers[driver_id] = addMarker(update); 

              }
            markers[driver_id].info = update; 
            delete data[driver_id];
          }
        }
        
        for(var driver_id in data) {
          var element = data[driver_id];
          if(typeof element == 'function') continue;

          // Add new marker
          console.log('add markers[' + driver_id + '] ' + element.updated);

       	  markers[driver_id] = addMarker(element);
          
          if(element.direction){
        	  
        	  var arrow = addEachArrow(element.direction, [element.longitude, element.latitude]);
        	  directionMarkers = {};
        	  directionMarkers[driver_id] = arrow;
        	  
          }
          delete data[driver_id];
        }; 
        
        
        if(!zoomed) {
          resizeToFit(markers);
          zoomed = true;
       }
  });

  socket.on('destinations', function(data) {
    console.log('SOCKET DESTINATIONS');
    console.log(data);
    // ver2.1 より配送先の表示は動的にWebsocketで受け取らないことにした
    
  });
};

function addMarker(element){

	
console.log(element);	
      var image_name = getMarkerIconUrlByStatus(element.status, element.is_deviated);
      var marker = null;

      var el = document.createElement('div');
	  el.className = 'marker';
	  el.style.backgroundImage = 'url('+ image_name +')';
	  el.style.backgroundRepeat = 'no-repeat'; 
	  el.style.width = '80px';
	  el.style.height = '80px';
	  
	  // テキストをアタッチ
	// var textnode =
	// document.createTextNode(decodeURIComponent(element.last_name +
	// element.first_name)); // Create a text node
	  var svgNode = makeSvgText(element);
	  el.appendChild(svgNode);   

	  // add marker to map
	  marker =  new mapboxgl.Marker(el, {offset: [-60 / 2, -50 / 2]})
		        .setLngLat([element.longitude, element.latitude])
		        .addTo(map);
		    
	  marker.info = {"status": element.status};
	  

	  el.addEventListener('click', function() {

	        var popup = new mapboxgl.Popup({closeOnClick: false})
	        .setLngLat([element.longitude, element.latitude])
	        .setHTML(getMarkerPopupContent(element) )
	        .addTo(map);
	        
	   });
  
	  return marker;
}


// マーカーにつけるためのドライバー名をSVGテキストにする
function makeSvgText(element){
	
	var svgNode = document.createElement("svg"); 
	var driverName = decodeURIComponent(element.last_name + ' ' + element.first_name);   
	svgNode.innerHTML = '<text class="svg_text">' + driverName + '</text>';
	
	return svgNode;

}



function getMarkerPopupContent(element) 
{
	console.log('getMarkerPopupContent');
	console.log(element);
	if(typeof element.car_type == 'undefined') {
	  element.car_type = '';
	}
	console.log(decodeURIComponent(element.car_type));
	var html = decodeURIComponent(element.car_type) + '<br>' + decodeURIComponent(element.action);
        if(element.status == 2 && element.car_type && element.zensu_url) {
        	//ステータスが２かつ車輌タイプが存在し、全数管理システムのURLがある
        	html += '<br><a href="' + element.zensu_url + encodeURIComponent(element.car_type) + '" target="_blank">' + element.zensu_url + element.car_type + '</a>';
        }
	return html;
}
function getMarkerColorByStatus(status)
{
  switch(status) {
    case 1: return '#ff0000';
    case 2: return '#0e97f8';
    case 3: return '#0ef850';
    case 4: return '#760ef8';
    default: return '#000000';
  }
}



function resizeToFit()
{

	  var bounds = null;
	  for(var driver_id in markers) {
	    var lnglat = markers[driver_id].getLngLat();
	    
	    console.log(lnglat);
	    if(!bounds) {
	    
	    	bounds = new mapboxgl.LngLatBounds();
	    	if(lnglat != null){
	    		bounds.extend([lnglat.lng, lnglat.lat]);
	    	}
	    
	    } else {

	    	bounds.extend([lnglat.lng, lnglat.lat]);
	    }
	
	  }	

	  map.fitBounds(bounds, {padding: 30});
	

}


$(document).ready(function() {
  if( !window.WebSocket ) {
    document.getElementById('error').style.display = 'block';
    return;
  }
  realtimeMap();
});
