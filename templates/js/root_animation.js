/*
*	アニメーション
*/
var lineSymbol = null;
var line = null;
var isAnimating = false;
var animationTimer;

function startRouteAnimation () {
	
//	if ( lineSymbol == null ) {
//
//		lineSymbol = {
//			path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
//			scale: 3,
//			strokeColor: '#19283c'
//		};
//		
//	}

	// animationLocations -> 他Javascript外部ファイルで生成する（  shortest_root.js / at 328 line ）
	
	/*
	if( line == null ) {
		line = new google.maps.Polyline({
			path: animationLocations,
			strokeColor: "#b21d2d",
			strokeWeight: 3,
			geodesic : true,
			icons: [{
				icon: lineSymbol,
				offset: '100%'
			}],
			map: map
		});
	}
	*/
	
	animate();
}

function animate() {
	
	//animationRoutes ルート線毎の緯度経度たち
	//rootPaths ルート線たち
	
	var count = 0;
	
	var routeIndicater = 0;	//ルートの回数
	var pointIndicaterByRoute = 0;	//ルートごとのポイント間の移動回数
	
	var points = animationRoutes[ routeIndicater ];	//ルート線の緯度経度たち
	var path = rootPaths[ routeIndicater ];	//ルート線
	
	animationTimer = setInterval( function() {
		
		console.log ( pointIndicaterByRoute + " / " + points.length );
		
		//一つのルート（各地点間ごとにルートが生成されている）をアニメーションし終えたら
		if ( isAnimating && pointIndicaterByRoute == points.length ) {
			
			//利用しているルートの順番が、ルート数と一致する場合、ストップする
			if ( routeIndicater == animationRoutes.length ) {
				stop();
				return;
			}
			
			//次のルートを利用する
			routeIndicater++;
			pointIndicaterByRoute = 0;
			points = animationRoutes[ routeIndicater ];//次の対象となるルート線の緯度経度たち
			path = rootPaths[ routeIndicater ];//次の対象となるルート線
			
		}
		
		isAnimating = true;
		count = (count + 1) % 300;
		pointIndicaterByRoute++;
		var icons = path.get('icons');
		icons[0].offset = (count / 3) + '%';
		path.set('icons', icons);
		
	}, 20);
}

function stop(id) {
    clearInterval( animationTimer );
//    polylines[id].polyline.setOptions({
//        icons: null});
    isAnimating = false;
};