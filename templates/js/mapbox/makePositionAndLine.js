/**
 * Mapboxのラインを表示する　主にドライバーの移動記録用
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param layer_id
 * @returns
 */

function makePositionAndLine(datas){
	
	var coordinates = [];
	for ( var i = 0; i < datas.length; ++i ) {

		if(datas[i] != null){

			coordinates.push(datas[i].geometry.coordinates);

			//スタートの場合、あるいは
			if(((datas[i].properties.start != "0000-00-00 00:00:00")&&
					(datas[i].properties.start != ""))||
					(datas[i+1] == null)){
								
				//ソースを分けるための識別文字列
				var source_id = "source" + i ;

				addLineOnStyleLoad(coordinates, datas[i].properties.color, source_id);
				coordinates = [];
				
			}
		}
	}
	

}
