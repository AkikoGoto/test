/**
 * ラインのような一つのGeoJSONに複数の緯度経度が入っている場合にMapをリサイズする
 * @author Akiko Goto
 * @since 2017/04/18
 * @version 2.1 
 */

function resizeMapToLine(positionDatas){

	var bounds = positionDatas.reduce(function(bounds, coord) {
        return bounds.extend(coord);
    }, new mapboxgl.LngLatBounds(positionDatas[0], positionDatas[0]));

    map.fitBounds(bounds, {
        padding: 20
    });
	
	
}
