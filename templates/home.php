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
				<h1 class="logo">Zara<span class="grunge">fix</span></h1> 
			</header>
  				<div id="map_canvas"></div>
				
			<footer>
				<div class="row">
          			<div class="span6">Este es un proyecto iniciado en el <a href="http://www.opendataday.org/wiki/City_Events_2011#Zaragoza">Open Data Hackaton 2011, en Zaragoza</a>. <br/>
	Por Carlos Cabrero, Pablo Jimeno y Dani Latorre.</div>
          			<div class="span6">Enlace a versión iphone.</div>
          			<div class="span6">Enlace a versión android.</div>
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
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
  function initialize() {
    var center = new google.maps.LatLng(41.65,-0.883333);
    var myOptions = {
      zoom: 14,
      center: center,
      disableDefaultUI: true,
      zoomControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	infowindow = new google.maps.InfoWindow({maxWidth:960});
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
	var marker = new google.maps.Marker({
		position: latLng, 
		map: map
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
</script>
		</div><!-- End div container-fluid -->
	</body>
</html>