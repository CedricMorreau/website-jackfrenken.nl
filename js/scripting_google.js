var arr_markerData=[];
function func_markerData(lat, lng, /*address, city, country, price, url,*/ ajax_id, /*, thumb,*/ og_type, clickable){
	return {
		lat: lat,
		lng: lng,
		// address: address,
		// city: city,
		// country: country,
		// price: price,
		// url: url,
		ajax_id: ajax_id,
		// thumb: thumb,
		og_type: og_type,
		clickable: clickable
	}
}

var arr_googleMapsData=[];
function func_googleMapsData(map_elementId, map_center_lat, map_center_lng, map_zoomlevel, map_markerType, show_infoWindow){
	return {
		map_elementId:map_elementId,
		map_center_lat:map_center_lat,
		map_center_lng:map_center_lng,
		map_zoomlevel:map_zoomlevel,
		map_markerType:map_markerType,
		show_infoWindow:show_infoWindow
	}
}

var global_var_googleMapOpenState=false;
function func_openGoogleMaps(open_state,arr_googleMapsData,arr_markerData){

	generate_googleMaps(arr_googleMapsData[0].map_elementId,arr_googleMapsData[0].map_center_lat,arr_googleMapsData[0].map_center_lng,arr_googleMapsData[0].map_zoomlevel,arr_googleMapsData[0].map_markerType,arr_markerData,arr_googleMapsData[0].show_infoWindow);

}

var googleMap_object;
var global_var_googleMapLoaded=false;
var global_arr_googleMapsMarkers=[];
var totalBounds = new google.maps.LatLngBounds();
var coordDistance = new Array;

function generate_googleMaps(map_elementId,map_center_lat,map_center_lng,map_zoomlevel,map_markerType,arr_markerData,show_infoWindow){

	googleMap_object=googleMaps_initialize(map_elementId,map_center_lat,map_center_lng,map_zoomlevel);

	switch(map_markerType){

		case 'multiple_clustered':

			var clusterMarkers_options = {
				gridSize: 25, 
				maxZoom: 14,
				styles: [{
					height: 25,
					url: "/resources/map-marker.png.png",
					width: 21
				}]
			};

			var clusterMarkers_object = new MarkerClusterer(googleMap_object,null,clusterMarkers_options);

			// Do not display count
			clusterMarkers_object.setCalculator(function(markers, numStyles) {
			  var index = 0;
			  var count = markers.length;
			  var dv = count;
			  while (dv !== 0) {
			    dv = parseInt(dv / 10, 10);
			    index++;
			  }

			  index = Math.min(index, numStyles);
			  return {
			    text: '',
			    index: index
			  };
			});

			var marker_object;

			for(var i = 0; i < arr_markerData.length; i++){

 				marker_object=googleMaps_addMarker(googleMap_object,arr_markerData,i,show_infoWindow);
			}
			break;

		case 'multiple_clustered_bs':

			var clusterMarkers_options = {
				gridSize: 50, 
				maxZoom: 5,
				styles: [{
					height: 26,
					url: "/resources/map-marker.png",
					width: 26
				}]
			};

			var clusterMarkers_object = new MarkerClusterer(googleMap_object,null,clusterMarkers_options);

			var marker_object;

			for(var i = 0; i < arr_markerData.length; i++){

 				marker_object=googleMaps_addMarker(googleMap_object,arr_markerData,i,show_infoWindow);
				clusterMarkers_object.addMarker(marker_object);

				ppLat = '50.853893799999994';
				ppLon = '5.9444501';

				distance = calcDistance(ppLat, ppLon, arr_markerData[i].lat, arr_markerData[i].lng, 'K');

				if (distance > 0.1 && distance < 300)
					totalBounds.extend(marker_object.getPosition());
			
				global_arr_googleMapsMarkers[arr_markerData[i].address]=marker_object;
			}
			break;

		default:
			break;
	}
}

function googleMaps_initialize(map_elementId,center_lat,center_lng,zoomlevel) {

	var styleArray = googleMapsStyling;

	var mapOptions = {
		center: new google.maps.LatLng(center_lat,center_lng),
    	zoom: zoomlevel,
    	mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: true,
        backgroundColor: 'transparent',
        streetViewControl: false,
        keyboardShortcuts: false,
        disableDefaultUI: false,
        styles: styleArray
    };
    
    var map = new google.maps.Map(document.getElementById(map_elementId), mapOptions);
	return map;
}

function googleMaps_addMarker(googleMap_object,arr_markerData,arr_index,show_infoWindow) {
	var markerImage = 0;

	arrIconImage = '/resources/map-marker.png';
	var markerImage = new google.maps.MarkerImage(
		arrIconImage,
		new google.maps.Size(83, 80),
		new google.maps.Point(0, 0),
		new google.maps.Point(60, 72)
	);

//	var marker = new google.maps.Marker({
//		position: new google.maps.LatLng(arr_markerData[arr_index].lat, arr_markerData[arr_index].lng),
//		map: googleMap_object,
//		icon: markerImage
//		// title: arr_markerData[arr_index].address
//	});
	
	var customCustomOverlay = new CustomOverlay(googleMap_object, new google.maps.LatLng(arr_markerData[arr_index].lat, arr_markerData[arr_index].lng), '/resources/map-marker.png', '', 83, 99, 0, true, arr_markerData[arr_index].ajax_id);
	
	totalBounds = new google.maps.LatLngBounds();

	totalBounds.extend(new google.maps.LatLng(arr_markerData[arr_index].lat, arr_markerData[arr_index].lng));

	// marker.setClusterGroup(arr_markerData[arr_index].clusterGroup);

//	return marker;
}

function fitBounds(zoomType) {

	googleMap_object.fitBounds(totalBounds);

	if (zoomType == 'zoom') {

		var listener = google.maps.event.addListener(googleMap_object, "idle", function() { 

			zoomMap('out', 1);
			google.maps.event.removeListener(listener); 
		});
	}
}

function zoomMap(type, level) {

	currentZoom = googleMap_object.getZoom();
	zoomLevel = (type == 'in') ? currentZoom + level : currentZoom - level;

	google.maps.event.trigger(googleMap_object, 'resize');
	googleMap_object.setZoom(zoomLevel);
}

function getPosition() {

	var location = navigator.geolocation.getCurrentPosition(handlePosition);

	if (location == undefined) {

		if (typeof updatePosition == 'function') {

			updatePosition('error-noPos');
		}
	}
}

function handlePosition(position) {

	ppLat = '50.853893799999994';
	ppLon = '5.9444501';

	var objCoords = [];
	objCoords.lat = position.coords.latitude;
	objCoords.lon = position.coords.longitude;

	objCoords.distance = calcDistance(ppLat, ppLon, objCoords.lat, objCoords.lon, 'K');

	if (typeof updatePosition == 'function') {

		updatePosition(objCoords);
	}
}

function calcDistance(lat1, lon1, lat2, lon2, unit) {

	return false;
	
	var radlat1 = Math.PI * lat1/180
	var radlat2 = Math.PI * lat2/180
	var radlon1 = Math.PI * lon1/180
	var radlon2 = Math.PI * lon2/180
	var theta = lon1-lon2
	var radtheta = Math.PI * theta/180
	var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
	dist = Math.acos(dist)
	dist = dist * 180/Math.PI
	dist = dist * 60 * 1.1515
	if (unit=="K") { dist = dist * 1.609344 }
	if (unit=="N") { dist = dist * 0.8684 }

	coordDistance[lat2+'_'+lon2] = dist;

	return dist
}

function CustomOverlay(map, latlon, icon, title, width, height, zindex, onclick, mapType) {
    this.latlon_ = latlon;
    this.icon_ = icon;
    this.title_ = title;
    this.markerLayer = $('<div />').addClass('overlay');
    this.width_ = width;
    this.height_ = height;
    this.zindex_ = zindex;
    this.onclick_ = onclick;
    this.mapType_ = mapType;
    this.setMap(map);
};

CustomOverlay.prototype = new google.maps.OverlayView;
CustomOverlay.prototype.onAdd = function() {
    var $pane = jQuery(this.getPanes().floatPane); // Pane 6, one higher than the marker clusterer
    $pane.append(this.markerLayer);
};
CustomOverlay.prototype.onRemove = function(){
    this.markerLayer.remove();
};
CustomOverlay.prototype.draw = function() {
    var projection = this.getProjection();
    var fragment = document.createDocumentFragment();

    this.markerLayer.empty(); // Empty any previous rendered markers

    var clickData = (this.onclick_) ? ' href="javascript: void(0);"' : ' href="javascript:showOverlay(\'' + this.mapType_ + '\');"';

    var location = projection.fromLatLngToDivPixel(this.latlon_);
    
    if (this.onclick_) {
	    var $point = jQuery('<div class="map-point" title="'+this.title_+'" style="'
				+'width:' + this.width_ + 'px; height:' + this.height_ + 'px; '
				+'left:'+location.x+'px; top:'+(location.y)+'px; '
				+'position:absolute; z-index: '+this.zindex_+';'
				+'">'
				+'<a'+clickData+'><img src="'+this.icon_+'" style="position: absolute; top: -72px; left: -33px; height: 72px" /></a>'
			+'</div>');
    	
    } else {

		var $point = jQuery('<div class="map-point" title="'+this.title_+'" style="'
				+'width:' + this.width_ + 'px; height:' + this.height_ + 'px; '
				+'left:'+location.x+'px; cursor: default; top:'+location.y+'px; '
				+'position:absolute; z-index: '+this.zindex_+';'
				+'">'
				+'<img src="'+this.icon_+'" style="position: absolute; top: -72px; left: -30px; height: 72px" />'
			+'</div>');
    }

    fragment.appendChild($point.get(0));
    this.markerLayer.append(fragment);
};

var googleMapsStyling = 

	[
	    {
	        "featureType": "landscape",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#FFBB00"
	            },
	            {
	                "saturation": 43.400000000000006
	            },
	            {
	                "lightness": 37.599999999999994
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "poi",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#00FF6A"
	            },
	            {
	                "saturation": -1.0989010989011234
	            },
	            {
	                "lightness": 11.200000000000017
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.highway",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#FFC200"
	            },
	            {
	                "saturation": -61.8
	            },
	            {
	                "lightness": 45.599999999999994
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.arterial",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#FF0300"
	            },
	            {
	                "saturation": -100
	            },
	            {
	                "lightness": 51.19999999999999
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "road.local",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#FF0300"
	            },
	            {
	                "saturation": -100
	            },
	            {
	                "lightness": 52
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    },
	    {
	        "featureType": "water",
	        "elementType": "all",
	        "stylers": [
	            {
	                "hue": "#0078FF"
	            },
	            {
	                "saturation": -13.200000000000003
	            },
	            {
	                "lightness": 2.4000000000000057
	            },
	            {
	                "gamma": 1
	            }
	        ]
	    }
	];