/**
 * アカウントでMapboxStudio上のDatasetsを全部取得してくる
 * @author Akiko Goto
 * @since 2017/04/07
 * @version 2.1
 * @param 
 * @returns
 */

function getDatasets(){
	
	
       var url ="https://api.mapbox.com/datasets/v1/onlineconsultant?access_token=" + mapboxgl.accessToken;

       var xmlhttp = new XMLHttpRequest();	
	
       xmlhttp.onreadystatechange = function() {
		    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		    	
		        var result = JSON.parse(xmlhttp.responseText);
		        console.log(result);
		    
		    }else{
		    	
		    	$("#result").text("指定のデータはMapbox Studio上に存在しません");
		    }
	
		}
	
	xmlhttp.open("GET", url, true);
	xmlhttp.send();
     	
}