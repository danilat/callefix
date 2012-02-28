<div style="width:400px">
<b>Categoría:</b> <?php echo getCategory($issue['category'])?><br/>
<b>Descripción:</b> <?php echo htmlentities($issue['description'])?><br/>
<?php if(isset($issue['imageSrc'])){
	echo '<img src="photos/'.htmlentities($issue['imageSrc']).'" width="200">';
}?>
<br/><br/>
<?php if($issue['fixed']){
	echo '<b>Arreglado</b><br/>';
}else{
	echo '<form action="index.php/detail/'. $issue['id'].'" method="post">';
	echo '<input type="submit" value="Marcar como arreglado"/>';
	echo '</form>';
	
}?>
Fecha de aviso: <?php echo$issue['createdAt']?>
</div>