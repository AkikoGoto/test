function getMarkerIconUrlByStatus(status, is_deviated)
{
  var base_url = template_url + '/templates/image/';
  switch(status) {
    case 5:
    case 1: 
    	
    	if(is_deviated){
    		
    		return base_url + 'track_front_working_middle_red_circle.png';
    		
    	}else{
    		
    		return base_url + 'track_front_working_middle.png';

    	}

    case 2: 
    	   	
    	if(is_deviated){

    		return base_url + 'track_rightDirection_transit_red_circle.png';
    		
    	}else{
    	
    		return base_url + 'track_rightDirection_transit.png';
    	
    	}
    
    case 3: 
    	
    	if(is_deviated){

    		return base_url + 'track_leftDirection_returnRunning_red_circle.png';
    		
    	}else{

    		return base_url + 'track_leftDirection_returnRunning.png';
 
    	}
    	
    case 4: 
    	
    	if(is_deviated){

    		return base_url + 'track_front_break_wait_middle_red_circle.png';

    	}else{

    		return base_url + 'track_front_break_wait_middle.png';
    		
    	}
    	
    
    //TODO デフォルトこれでよいか考える
    default: return 'track_front_working_middle.png';
  }
}