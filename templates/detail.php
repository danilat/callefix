<b>Categoría:</b> <?php echo getCategory($issue['category'])?><br/>
<b>Descripción:</b> <?php echo htmlentities($issue['description'])?><br/>
<?php if(isset($issue['imageSrc'])){
	echo '<img src="photos/'.htmlentities($issue['imageSrc']).'" width="200">';
}?>
<br/>