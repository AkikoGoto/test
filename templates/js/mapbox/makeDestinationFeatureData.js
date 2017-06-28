/**
 * 配送先用のGeoJSONを作成する
 * @author Akiko Goto
 * @since 6.0
 * @param destination_datas, color = null
 * @returns
 */

function makeDestinationFeatureData(destination_datas, color){
	
	var datas = [];
	var colorList = []

	for(var i = 0; i < destination_datas.length; i++ ){

		if(destination_datas[i].id =="undefined"){
			destination_datas[i].id = "1";
		}

		if(color != null){
			destination_datas[i].color = color;
		}
		
		var data = {
		"type" : "Feature",
		"geometry" : {
			"type" : "Point",
			"coordinates" : [ destination_datas[i].longitude, destination_datas[i].latitude ]
			},
		"properties" : {			
			"id" : destination_datas[i].id,
			"name" : destination_datas[i].destination_name,
			"color" : destination_datas[i].color,
			"information" : destination_datas[i].information,
			"url" : destination_datas[i].url
			}
		}; 

		datas.push(data);	
		colorList.push([destination_datas[i].id, destination_datas[i].color]);
	}

	return Array(datas, colorList);
	
}