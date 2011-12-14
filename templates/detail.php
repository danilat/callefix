<b>Categoría:</b> <?php echo $issue['category']?><br/>
<b>Descripción:</b> <?php echo $issue['description']?><br/>
<?php if(isset($issue['photo'])){
	echo '<img src="photos/'.$issue['photo'].'" width="200">';
}?>
<br/>