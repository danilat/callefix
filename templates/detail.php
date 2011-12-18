<b>Categoría:</b> <?php echo $issue['category']?><br/>
<b>Descripción:</b> <?php echo $issue['description']?><br/>
<?php if(isset($issue['imageSrc'])){
	echo '<img src="photos/'.$issue['imageSrc'].'" width="200">';
}?>
<br/>