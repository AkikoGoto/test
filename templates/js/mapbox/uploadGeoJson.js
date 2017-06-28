/**
 * アップロードされたGeoJSONからMapboxで線を表示させる
 * @author Akiko Goto
 * @since 2017/04/18
 * @version 2.1
 * 参考
 * https://dzone.com/articles/file-upload-and-progress
 */


function uploadFiles(url, files) {

  var formData = new FormData();

  for (var i = 0, file; file = files[i]; ++i) {
    formData.append("file", file);
  }

  var xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.onload = function(e) {


	};
  xhr.upload.addEventListener("progress", progressFunction, false);

  xhr.onreadystatechange = function() {

	  if (xhr.readyState === 4) {

    	$("input#geo_json").val(xhr.response);

    	var jsonData = JSON.parse(xhr.response);
    	console.log(jsonData);

    	if(jsonData.error != null ){
    		$("#file_content").html("エラーがあります。" + jsonData.error);
    	}else{

    		addLineToMap(jsonData);

    	}
	  }

  }

  xhr.send(formData);


}


function addLineToMap(jsonData){

	var positionDatas = jsonData.features[0].geometry.coordinates;

	var existed_map = map.getSource("newRoute");

	if(existed_map != null && existed_map != "undefined"){
	      map.removeSource("newRoute");
	      map.removeLayer("newRoute");
	}

	addLine(positionDatas, "#ff0000", "newRoute");

	resizeMapToLine(positionDatas);

}


document.querySelector('input[type="file"]').addEventListener('change', function(e) {
  uploadFiles('index.php?action=transport_route/geoJsonUpload', this.files);
}, false);


function progressFunction(evt){
         var progressBar = document.getElementById("progressBar");
         var percentageDiv = document.getElementById("percentageCalc");
         if (evt.lengthComputable) {
           progressBar.max = evt.total;
           progressBar.value = evt.loaded;
           percentageDiv.innerHTML = Math.round(evt.loaded / evt.total * 100) + "%";
         }
 }