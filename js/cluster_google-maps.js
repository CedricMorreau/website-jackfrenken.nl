
var icon_directory = "../images/googleMap_icons/";

var step_count = 50;

var total_markers = 0;

var marker_count = 0;

var builder_indicator = false;

var timeout_timer = false;
var timeout_time = 30; // 30 sec

var request_url;

function googleMaps_init(url)
	{

	request_url=url;

	jQuery(function($)
		{
		$.ajax(
			{
			url : request_url+"&op=get_total_markers",
			type : "GET",
			success : function(data)
				{
				total_markers = data;
				$('#marker_count_total').html(total_markers);
				retrieve_locations(0);
				}
			});
		});
	timeout_timer = setTimeout(error_timeout,1000*timeout_time)
	}

function error_timeout(e)
	{
	if ($('#loading').length)
		{
		$('#loading .info_field').html('Het opbouwen van de kaart duurt erg lang, klik <a href="#">hier</a> indien u niet langer wilt wachten.<br/>(waarschuwing: sommige gegevens zijn wellicht niet geladen)');
		$('#loading .info_field').addClass('error');
		$('#loading .info_field a').click(function()
			{
			$('#loading').remove();
			return false;
			});
		}
	}

function retrieve_locations(loc_start)
	{
	$.ajax(
		{
		url : request_url+"&op=get_address&step="+step_count+"&start="+(loc_start),
		type : "GET",
		success : function(data)
			{
				
//		alert(request_url+"&op=get_address&step="+step_count+"&start="+(loc_start));
				
			// get the data string and convert it to a JSON object.
			var jsonData = JSON.parse(data);
			var latitude = new Array();
			var longitude = new Array();
			var address = new Array();
			var extra = new Array();
			$.each(jsonData, function(Idx, Value)
				{
				$.each(Value, function(x, y)
					{
					//Creating an array of latitude, longitude
					if(x == 'lat')
						latitude[latitude.length] = y;
					if(x == 'lng')
						longitude[longitude.length] = y;
					if(x == 'address')
						address[address.length] = y;
					if(x == 'extra')
						extra[extra.length] = y;
				 	});
				});
		
			process_marker_data(latitude,longitude,address, extra);
			
			if ( loc_start <= total_markers )
				retrieve_locations(loc_start+step_count);
			else
				{
				builder_indicator = jQuery('#loading');
				if (builder_indicator)
					{
					jQuery(builder_indicator).animate({'opacity':0},400,function()
						{
						jQuery(this).remove();
						});
					}
				}
			}
		});
	}


var map = false, mc = false;
var ib = false;
var geocoder = false;


var images = [];


function process_marker_data(latitude,longitude, address, extra)
	{
	if (map === false) // if the map has not been created yet, do so now
		{
		geocoder = new google.maps.Geocoder();
		var initCenter = new google.maps.LatLng(51.192685, 5.992699);
		map = new google.maps.Map(document.getElementById('googlemaps'), {
		    zoom: 11,
		    center: initCenter,
		    mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		
		var mcOptions = {gridSize: 50, maxZoom: 14}; // maxZoom: 15 // default
		mc = new MarkerClusterer(map,null,mcOptions);
		
		var image;		
			
		//initialization of infowindow
		var infoWindow = new google.maps.InfoWindow;
		var boxText = document.createElement("div");
		var j = 1;
		
		var infobox_options = {
				 content: boxText
				 // styling is done via CSS now.
				 /*
				,disableAutoPan: false
				,maxWidth: 181
				,zIndex: null
				,boxStyle: { 
					  background: "#000000"
					  ,color: "#fff"
					  ,width: "250px"
					  ,padding: "10px"
					  ,borderRadius: "5px"
					  ,fontFamily: "Tahoma"
					  ,fontSize: '12px'
					  ,opacity: "0.8"
					 }
				*/
				,infoBoxClearance: new google.maps.Size(1, 1)
				,isHidden: false
				,pane: "floatPane"
				,closeBoxURL: ""
				,enableEventPropagation: false
			};
		ib = new InfoBox(infobox_options);
		}

	for(var a = 0; a < latitude.length; ++a)
		{
		create_marker(geocoder, map, address[a], latitude[a], longitude[a], ib, extra[a]);
		}
	}

function update_marker_count()
	{
	marker_count++;
	$('#marker_count').html(marker_count);
	$('#total_markers').html("Totaal aantal gevonden klanten: "+marker_count);
	}

function create_marker(geocoder, map, address, latitude, longitude, ib, extra)
	{
	update_marker_count();
	
	image = null;
	if (extra)
		{
		if (extra["image"].length)
			{
			image_name = extra["image"];
			if (!images[image_name])
				images[image_name] = new google.maps.MarkerImage(icon_directory+image_name); //('icon-home.gif');
			image = images[image_name];
			}
		}
	
	//onclick marker function
    var onMarkerClick = function()
    	{
        var marker = this;
        var latLng = marker.getPosition();
        var ib_content = "";
        if (extra)
        	{
        	if (extra["company_name"].length)
        		ib_content += '<h2>'+extra["company_name"]+'</h2>';
        	if (extra["company_branch"].length)
        		ib_content += '<h3>Bedrijfstak: '+extra["company_branch"]+'</h3>';
        	
        	ib_content += '<p>'+address+'</p>';
        	
        	if (extra["url_detail_page"].length)
        		ib_content += '<a href="'+extra["url_detail_page"]+'">Commerci&euml;le groep &raquo;</a>';
        	
        	
        	}
        ib.setContent('<div class="googlemaps_info_box">'+ib_content+'</div>');
        ib.open(map, marker);
        };
      
      google.maps.event.addListener(map, 'click', function() {
		ib.close();
        });
      
	  //In array lat long is saved as an string, so need to convert it into int.
      var lat = parseFloat(latitude);
      var lng = parseFloat(longitude);
    
	var marker = new google.maps.Marker({
        map: map,
        icon: image,
        position: new google.maps.LatLng(lat, lng),
        title: address
    	});
	mc.addMarker(marker);
	google.maps.event.addListener(marker, 'click', onMarkerClick);
	}