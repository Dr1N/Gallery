<?php 
	
	require_once("config.php");
	require_once("libs.php");

	//Загрузка файла на сервер

	$file = isset($_FILES['userfile']) ? $_FILES['userfile'] : "";
	if ($file != "") 
	{
		try
		{
			$fileName = validateAndUploadFile($file);
			createSmallImage($fileName);
			
		}
		catch(Exception $e)
		{
			echo "<p style='color:red'>". $e->getMessage() . "</p>";
		}
	}

	//Получение имён изображений

	try
	{
		$images = getImages();
	}
	catch(Exception $e)
	{
		echo "<p style='color:blue'>". $e->getMessage() . "</p>";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Галерея</title>
</head>
<body>
	<div align="center">
		<form action="index.php" method="POST" enctype="multipart/form-data">
		<b>Выберете изображение:</b><br/>
		<input type="file" name="userfile"/><br/>
		<input type="submit" value="Загрузить"/>
	</form>
	</div><br/>
	<?php if(count($images) == 0): ?>
		<h3 align="center">В галлерее нет изображений</h3>
	<?php else: ?>

	<table width="70%" align="center" border="1">
	<thead>
		<th colspan="5">Загруженные изображения</th>
	</thead>
	<tbody align="center">
		<tr>
			<?php for($i = 0; $i < count($images); $i++): ?>
				<?php if($i != 0 && $i % IMAGES_PER_ROW == 0) echo "</tr><tr>"; ?>
				<?php 
					$smallPath = IMAGE_SMALL_PATH . $images[$i];
					$bigPath = IMAGE_BIG_PATH . $images[$i];
				?>
				<td><?= "<a href='$bigPath'><img src='$smallPath'></a>" ?></td>
			<?php endfor ?>
		</tr>
	</tbody>
	</table>
	<?php endif ?>
</body>
</html>