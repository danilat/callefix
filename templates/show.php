<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<link href="../../css/application.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Knewave' rel='stylesheet' type='text/css'>
		<title>ZaraFix</title>
	</head>
<body onload="initialize()">
	<div class="container-fluid">
		<header class="overlay">
			<h1 class="logo">Zara<span class="grunge">fix</span></h1> 
		</header>
<b>Categoría:</b> <?php echo getCategory($issue['category'])?><br/>
<b>Descripción:</b> <?php echo htmlentities($issue['description'])?><br/>
<?php if(isset($issue['imageSrc'])){
	echo '<img src="../../photos/'.htmlentities($issue['imageSrc']).'" width="200"><br/>';
}?>
<b/>Fecha de aviso:</b> <?php echo$issue['createdAt']?>
<br/><br/>

<div id="map_canvas_detail"></div>
</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
function initialize() {
    var center = new google.maps.LatLng(<?php echo $issue['lat']?>,<?php echo $issue['lng']?>);
    var myOptions = {
      zoom: 14,
      center: center,
      disableDefaultUI: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	var map = new google.maps.Map(document.getElementById("map_canvas_detail"), myOptions);
	var marker = new google.maps.Marker({
		position: center, 
		map: map
	});
  }
</script>
</body>
</html>