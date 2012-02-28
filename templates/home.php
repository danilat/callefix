<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<link href="css/application.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Knewave' rel='stylesheet' type='text/css'>
		<title>ZaraFix</title>
	</head>
	<body onload="initialize()">
		

    	<div class="container-fluid">
			<header class="overlay">
				<a class="queja pull-right btn primary large" data-controls-modal="modal-from-dom" data-backdrop="static" data-keyboard="true">Tengo una queja o incidencia de la que informar</a>
				<div class="logo">				
					<img src="images/zarafix.png" />
				</div>
			</header>
  				<div id="map_canvas" class="shadow"></div>
				
			<footer>
				<div class="row">
          			<div class="span5">
		              <img class="small-bs-icon" src="images/icon-github.png">
									<h3>Open Data Hackaton 2011</h3>
									<p>Este es un proyecto iniciado en el <a href="http://www.opendataday.org/wiki/City_Events_2011#Zaragoza">Open Data Hackaton 2011, en Zaragoza</a>. <br/>
	Por Carlos Cabrero, Pablo Jimeno y Dani Latorre.</p>
									</div>
          			<!--div class="span5">
		              <img class="small-bs-icon" src="images/icon-github.png">
									<h3>Enlace a versión iphone</h3>
								</div>
          			<div class="span5">
		              <img class="small-bs-icon" src="images/icon-github.png">
									<h3>Enlace a versión android</h3>
								</div-->
          			<div class="span5 icon github">
		              <img class="small-bs-icon" src="images/icon-github.png">
									<h3>Feel free to <a href="https://github.com/danilat/callefix">fork the code</a></h3>
								</div>
          		</div>

          		
			</footer>
			<div id="modal-from-dom" class="modal hide fade in">
            <div class="modal-header">
              <a href="#" class="close">×</a>
              <h3>C&oacute;mo empezar</h3>
              <p>Si quieres dar aviso de un problema o desperfecto, simplemente deber&aacute;s:</p>
            </div>
            <div class="modal-body">
              <ol>
              	<li>Localiza en el mapa el sitio de la incidencia</li>
              	<li>Haz click sobre el mapa.</li>
              	<li>Completa el formulario con los detalles de la incidencia.</li>
              </ol>	
            </div>
            <div class="modal-footer">
              
            </div>
          </div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"></script>
<script type="text/javascript" src="js/zarafix-modal.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
  function initialize() {
    var center = new google.maps.LatLng(41.65,-0.883333);
    var myOptions = {
      zoom: 14,
      center: center,
      disableDefaultUI: true,
      zoomControl: true,
      //mapTypeId: google.maps.MapTypeId.ROADMAP
	mapTypeId: "OSM",
	                mapTypeControlOptions: {
	                    mapTypeIds: ["OSM"]
	                }

    };
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	infowindow = new google.maps.InfoWindow({maxWidth:960});
	
	map.mapTypes.set("OSM", new google.maps.ImageMapType({
	                getTileUrl: function(coord, zoom) {
	                    return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
	                },
	                tileSize: new google.maps.Size(256, 256),
	                name: "OpenStreetMap",
	                maxZoom: 18
	            }));


	<?php 
	if($issues){
	foreach ($issues as $index => $issue) {
		echo "createMarker(new google.maps.LatLng(".$issue['lat'].",".$issue['lng']."), map, infowindow,".$issue['id'].");\n";
	}
	}?>
	var newMark = new google.maps.Marker();
	google.maps.event.addListener(map, 'click', function(ev) {
		var latLng = ev.latLng

		newMark.setMap(map);
		newMark.setPosition(latLng)
		loadForm(infowindow, map, newMark, latLng);
	});

  }

function createMarker(latLng, map, infowindow, id){
	var image = 'images/fixMarker.png';
	var marker = new google.maps.Marker({
		position: latLng, 
		map: map,
		icon: image
	});
	google.maps.event.addListener(marker, 'click', function() {
		loadData(infowindow, map, marker, id);
	});
}

function loadData(infowindow, map, marker, id){
$.ajax({
  url: "index.php/detail/"+id,
  context: document.body,
  success: function(data){
	data = '<div style="">'+data+'</div>';
	infowindow.setContent(data);
	infowindow.open(map,marker);
  }
});
}

function loadForm(infowindow, map, marker, latLng){
	var form ='<form action="" method="post" style="height: 120px;width:350px;" enctype="multipart/form-data">'+
		'Categoría: <select name="category">'+
		<?php foreach ($categories as $key => $category) {
			echo '\'<option value="'.$key.'">'.$category.'</option>\'+';
		}?>
		'</select><br/>'+
		'Descripción: <textarea name="description"></textarea><br/>'+
		'Añade una foto: <input type="file" name="photo"/><br/>'+
		'<input type="submit" id="submit" value="Enviar queja"/><br/>'+
		'<input type="hidden" name="lat" value="'+latLng.lat()+'" id="lat"/><input type="hidden" name="lng" value="'+latLng.lng()+'" id="lng"/>'+
	'</form>';
	google.maps.event.addListener(infowindow, 'closeclick', function() {
		marker.setMap(null);
	});
	infowindow.setContent(form);
	infowindow.open(map,marker);
}
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/wPUdVxE4oJ4pdppsU1dcg.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>
		</div><!-- End div container-fluid -->
	</body>
</html>
