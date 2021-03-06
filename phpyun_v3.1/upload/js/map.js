 function getmaplnglat(id,x,y,xid,yid){
	var data=get_map_config();
	var config=eval('('+data+')');
	var rating,map_control_type,map_control_anchor;
	if(!x && !y){x=config.map_x;y=config.map_y;}
	var map = new BMap.Map(id, {defaultCursor: 'default'});
	var pront=map.centerAndZoom(new BMap.Point(x,y),15);
	var TILE_SIZE = 256;
	map.enableScrollWheelZoom(true); 
	var opts = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	map.addControl(new BMap.NavigationControl(opts));
	
	if(config.map_control_scale==1){//������
		 var opts = {offset:new BMap.Size(150,5)} 
		 map.addControl(new BMap.ScaleControl(opts));  
	 }
	
	map.addEventListener('click', function(e){
		var info = new BMap.InfoWindow('', {width: 260});
		var projection = this.getMapType().getProjection();
		var lngLat = e.point;
		document.getElementById(xid).value=lngLat.lng;
		document.getElementById(yid).value=lngLat.lat;
		map.clearOverlays();
		var point = new BMap.Point(lngLat.lng,lngLat.lat);
		var marker = new BMap.Marker(point);  
		map.addOverlay(marker);           
	});
}

function getmapshowcont(id,x,y,title,cont){
	var map = new BMap.Map(id);
	var point = new BMap.Point(x,y);
	var marker = new BMap.Marker(point);
	map.enableScrollWheelZoom(true); 
	var optsa = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	map.addControl(new BMap.NavigationControl(optsa)); 
	var opts = {
	  width : 50,     
	  height: 20,     
	  title : title  
	}
	map.centerAndZoom(point, 15);
	map.addOverlay(marker);
	var infoWindow = new BMap.InfoWindow(cont, opts);
	marker.openInfoWindow(infoWindow);  
	marker.addEventListener("click", function(){      
	marker.openInfoWindow(infoWindow);    
	});
}

function getmapnoshowcont(id,x,y,xid,yid){
	var data=get_map_config();
	var config=eval('('+data+')');
	var rating,map_control_type,map_control_anchor;
	if(!x && !y){x=config.map_x;y=config.map_y;}
	
	
	
	var map = new BMap.Map(id);
	var point = new BMap.Point(x,y);
	var marker = new BMap.Marker(point);
	var opts = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	
	map.enableScrollWheelZoom(true); 
	map.addControl(new BMap.NavigationControl(opts));  
	map.centerAndZoom(point, 15);
	map.addOverlay(marker);	
	map.addEventListener("click",function(){
	   getmaplnglat(id,x,y,xid,yid);
	});
}
function get_map_config(){
	var config="";
	$.ajax( {
			async : false,
			type : "post",
			url :'../index.php?m=ajax&c=mapconfig',
			data : {id:""},
			success : function(set) {
			config=set;
		}
		});
	return config;
}