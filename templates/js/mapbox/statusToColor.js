/**
 * ステータスのIDで色を返す
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param layer_id
 * @returns
 */

function statusColor(status){

	var color;
	
	if(	status == 1){
		color = "#cf714a";
	}else if(status == 2){
		color = "#ff00ff";
	}else if(status == 3){
		color = "#4169e1";
	}else if(status == 4){
		color = "#009944";
	}else{
		color = "#FF0000";
	}

	return color;
		 
}