var bar_status;
var baseURL;
var errorDestinations = new Array();
var geocoder = new google.maps.Geocoder();
var geocodingErrorCounter = 0;

//Methodの流れ
//sendFileToServer -> geocoding -> record_csv_destination -> record_csv_destination_ajax
//sendFileToServerでアップロードされたCSVファイルをデコードし、取得した配送先の配列をJSON形式でデータを返す
//JSONデータが正常に帰ってきたら、geocodingを呼ぶ
//

function sendFileToServer(formData,status)
{
	bar_status = status;
	var loc = window.location.pathname;
	baseURL = loc.substring(0, window.location.pathname.lastIndexOf('/'));
	
    var uploadURL = baseURL+"/?action=/destination/decode_csv_destination"; //Upload URL
    var extraData ={}; //Extra Data.
    var jqXHR=$.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 95);
                        }
                        //Set progress
                        bar_status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
        	
			//結果によって処理を分ける
			if (data.status === 'SUCCESS') {
				
				//geocoding start
				var destinations = data.destinations;
				var indicator = 0;
				geocoding ( destinations, indicator );
				
			} else if (data.status === 'FALSE') {
				change_status_bar( 95, "失敗" );
			} else if (data.status === 'INVALID_TYPE') {
				change_status_bar( 95, "CSVファイルを選択して下さい" );
			} else if (data.status === 'INVALID_CSV_FORMAT') {
				change_status_bar( 95, "CSVの形式が正しくありません" );
			}
			
        }
    }); 
 
    bar_status.setAbort(jqXHR);
}

//geocoding
function geocoding ( datas, indicater ) {

	var max = datas.length;
	var min = 100 / max;
//	var indicater = 0;
	var datasWithLatLng = new Array();
	var address = datas[indicater].address;

	console.log ( indicater + " / " + max + "-----------------" );
	console.log ( address );
	
//	setTimeout(null, 1000);
	
	geocoder.geocode( { 'address' : address }, function(results, status) 
	{
		var lat;
		var lng;
		var isNextAddressAllowedToGeocode = false;
		if ( status == google.maps.GeocoderStatus.OK ) {

			//緯度経度取得に成功
			lat = results[0].geometry.location.lat();
			lng = results[0].geometry.location.lng();
			isNextAddressAllowedToGeocode = true;//次の住所のジオコーディングを許可する
			
			console.log("ok!!");
			
		} else {
			
			//緯度経度取得に失敗
			lat = "0";
			lng = "0";
			
			//ジオコーディングのエラーが発生した回数を記録
			geocodingErrorCounter++;
			
			//エラー回数が3以上になったとき、今の住所のジオコーディングを中止し、次の住所のジオコーディングを行う
			if ( geocodingErrorCounter >= 3 ) {
				isNextAddressAllowedToGeocode = true;//次の住所のジオコーディングを許可する
				errorDestinations.push( datas[indicater].destination_name );
			}
			
			console.log("no!!");
			
		}
		
		//次の住所のジオコーディングが許可されてる場合のみ、次のジオコーディングでは次の住所を使う
		//許可されてない場合は、今の住所のジオコーディングが失敗しているため、再度今の住所を使う
		if (isNextAddressAllowedToGeocode) {
			geocodingErrorCounter = 0;//エラー回数をリセットする
			datas[indicater].latitude = lat;
			datas[indicater].longitude = lng;
			datasWithLatLng.push( datas );
			indicater++;//次の住所を使うことを意味する
		}
		
		//最後の配送先の場合、再度record_csv_destinationを呼ぶ
		if ( indicater == max )
			record_csv_destination( datas );
		//短時間にgeocodingをしすぎて緯度経度取得に失敗する場合があるため、少しスリープしてからgeocodingを呼ぶ
		else if ( geocodingErrorCounter == 0 ) {
			setTimeout( function(){ geocoding ( datas, indicater );}, 800);
		//何度も失敗している場合は、さらに長めにスリープしてからgeocodingを行う
		} else {
			setTimeout( function(){ geocoding ( datas, indicater );}, 1200);
		}
		
		var per = min * indicater;
		console.log( "Percentage is " + per + " / " + max );
		change_status_bar( per, "" );
		
	});
}

function record_csv_destination( destinations ) {
	
	record_csv_destination_ajax( destinations )
	.done(function(data){

		console.log( data );
		
		//結果によって処理を分ける
		if (data.status === 'SUCCESS') {

			bar_status.setProgress(100);
			$(".filename:first").append("<div>配送のアップロードが完了しました</div>");
			
			console.log(errorDestinations);
			if ( errorDestinations.length > 0 ) {
				var error_destination_names_str = "";
				for ( var i = 0; i < errorDestinations.length; i++ ) {
					if (i > 0) {
						error_destination_names_str += ", ";
					}
					error_destination_names_str += errorDestinations[i];
				}
				$(".filename:first").append("<span class=\"red\">" + error_destination_names_str + "の住所が正しく入力されていなかったため、緯度経度が登録できませんでした。</span>");

				errorDestinations.length = 0;
			}
			
		} else if (data.status === 'FALSE') {
			change_status_bar( 95, "失敗" );
		} else if (data.status === 'INVALID_TYPE') {
			change_status_bar( 95, "CSVファイルを選択して下さい" );
		} else if (data.status === 'INVALID_CSV_FORMAT') {
			change_status_bar( 95, "CSVの形式が正しくありません" );
		} else if (data.status === 'NO_DATA') {
			change_status_bar( 95, "登録する配送先がありませんでした。データを入れてください" );
		} else {
			change_status_bar( 95, "エラーが発生しました" );
		}
		
		//エラー結果を画面上に表示するテストコード
		/*
		if (data.error) {
			$("#error").text( data.error.xdebug_message );
		}
		console.log(data);
		*/
		
	})
	.fail(function(data){
		console.log ( data );
		change_status_bar( 95, "失敗" );
	});
	
}

function record_csv_destination_ajax( datas ) {

	var defer = $.Deferred();
	console.log (datas);
    $.ajax({
    	type:"POST",
        url: baseURL+'/?action=/destination/destination_auto_upload',
        data: { destinations : datas },
        success: defer.resolve,
        error: defer.reject
    });
    return defer.promise();
    
}

function change_status_bar( progress, text ) {
	bar_status.setProgress( progress );
	$(".filename:first").append("<span class=\"red\">" + text + "</span>");
}
 
var rowCount=0;
function createStatusbar(obj)
{
     rowCount++;
     var row="odd";
     if(rowCount %2 ==0) row ="even";
     this.statusbar = $("<div class='statusbar "+row+"'></div>");
     this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
     this.size = $("").appendTo(this.statusbar);//<div class='filesize'></div>
     this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
     this.abort = $("<div class='abort'>×</div>").appendTo(this.statusbar);
     obj.after(this.statusbar);
 
    this.setFileNameSize = function(name,size)
    {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }
 
        this.filename.html(name);
        this.size.html(sizeStr);
    }
    this.setProgress = function(progress)
    {       
        var progressBarWidth =progress*this.progressBar.width()/ 100;  
        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
            this.abort.hide();
        }
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.statusbar;
        this.abort.click(function()
        {
            jqxhr.abort();
            sb.hide();
        });
    }
}
function handleFileUpload(files,obj)
{
   for (var i = 0; i < files.length; i++) 
   {
        var fd = new FormData();
        fd.append('file', files[i]);
        
		//You can add extra data here:
        fd.append('name2','VALUE2');
 
        var status = new createStatusbar(obj); //Using this we can set progress.
        status.setFileNameSize(files[i].name,files[i].size);
        sendFileToServer(fd,status);
 
   }
}
$(document).ready(function()
{
var obj = $("#dragandrophandler");
obj.on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '2px solid #0B85A1');
});
obj.on('dragover', function (e) 
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e) 
{
 
     $(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
 
     //We need to send dropped files to Server
     handleFileUpload(files,obj);
});
$(document).on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});
$(document).on('dragover', function (e) 
{
  e.stopPropagation();
  e.preventDefault();
  obj.css('border', '2px dotted #0B85A1');
});
$(document).on('drop', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});
 
});