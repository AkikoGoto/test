/**
 * 進行方向の矢印をつける
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */

function addArrow(datas){
	
	datas.forEach(function(marker) {
		
		
		if(marker.properties["direction"] != null){
			
			var markerRotation = marker.properties["direction"];
			var lnglat = marker.geometry.coordinates;
			
			addEachArrow(markerRotation, lnglat);

		}
	
	});
	
}

function addEachArrow(markerRotation, lnglat){

	    var markerSize = 70;
	    
	    var offsetX = -markerSize / 2;
	    var offsetY = -markerSize / 2;
	    
	    //下方向の場合、字を邪魔しないようにオフセットを調整
	    if(markerRotation > 100 && markerRotation < 260){
	    	
	    	if(markerRotation > 100 && markerRotation < 160){
			    
	    		offsetX = -20;		    	
	    		
	    	}else if (markerRotation > 200 && markerRotation < 260){

	    		offsetX = -50;		    	
	    		
	    	}
	    	
	    	offsetY = -20;
	    }
	    
	    var markerData = ""
	    var el = document.createElement('div');
	    el.className = 'marker';
	    el.innerHTML = '<img src="templates/image/triangle.gif" style=transform:rotate('+ markerRotation + 'deg)>';
	    el.style.width = markerSize + 'px';
	    el.style.height = markerSize + 'px';
	    
	    var arrow_marker = new mapboxgl.Marker(el, {offset: [offsetX, offsetY]})
        .setLngLat(lnglat)
        .addTo(map);
	    
	    arrow_marker.options = {"angle": markerRotation};

	    
	    return arrow_marker;
	

}