<?php 
	require_once("config.php");

	//��������� � �������� ����� �� ������

	function validateAndUploadFile($file)
	{
		if($file['error'] == 4) 
			throw new Exception("�������� ���� ��� ��������!");
		else if($file['error'] != 0) 
			throw new Exception("������ ��� �������� �����! ������: {$file['error']}");

		$arrMime = array("image/jpeg", "image/png", "image/gif", "image/bmp");
		if(!in_array($file['type'], $arrMime)) throw new Exception("������ ����� �� ��������������!");

		$arrExt = array("png", "jpeg", "jpg", "gif", "bmp");
	 	$ext = mb_strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	  	if(!in_array($ext, $arrExt)) throw new Exception("���������� �����  ($ext) �� ��������������!");

	  	if(move_uploaded_file($file['tmp_name'], IMAGE_BIG_PATH . $file['name']) == false)
		{
			throw new Exception("������ �� ������� ����������� ����!");
		}
		return $file['name'];
	}

	//������ ��������� �����������
	
	function createSmallImage($imageName)
	{
		//���������� � �����������

		$imagePath = IMAGE_BIG_PATH . $imageName;
		$imagaSize = getimagesize($imagePath);

		$srcWidth = $imagaSize[0];
		$srcHeight = $imagaSize[1];
		$imageType = $imagaSize[2];

		//����������� ���� ����������� ������ ������

		$imageDst = IMAGE_SMALL_PATH . $imageName;
		if($srcWidth <= IMAGE_SIZE && $srcHeight <= IMAGE_SIZE)
		{
			copy($imagePath, $imageDst);
		}
		else
		{
			//��������������� ����������� � ���������� � ����

			switch ($imageType) 
			{
				case IMAGETYPE_JPEG:
					$imgSrc = imagecreatefromjpeg($imagePath);
					break;
				case IMAGETYPE_PNG:
					$imgSrc = imagecreatefrompng($imagePath);
					break;
				case IMAGETYPE_GIF:
					$imgSrc = imagecreatefromgif($imagePath);
					break;
				case IMAGETYPE_BMP:
					$imgSrc = imagecreatefromwbmp($imagePath);
					break;
				default:
					die("����������� ������!");
			}

			//����������� ������� ���������

			if($srcWidth >= $srcHeight)
			{
				$koef = (double)$srcHeight / $srcWidth;
				$dstWidth = IMAGE_SIZE;
				$dstHeight = (int)($koef * IMAGE_SIZE);
			}
			else
			{
				$koef = (double)$srcWidth / $srcHeight;
				$dstWidth = (int)($koef * IMAGE_SIZE);
				$dstHeight = IMAGE_SIZE;
			}

			//�������� ���������

			$imgDst = imagecreatetruecolor($dstWidth, $dstHeight);
			if(!imagecopyresized($imgDst, $imgSrc, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight))
			{
				throw new Exception("�� ������� �������������� �����������");
			}

			//���������� � ����

			$dstPath = IMAGE_SMALL_PATH;
			switch ($imageType) 
			{
				case IMAGETYPE_JPEG:
					imagejpeg($imgDst, $dstPath . $imageName);
					break;
				case IMAGETYPE_PNG:
					imagepng($imgDst, $dstPath . $imageName);
					break;
				case IMAGETYPE_GIF:
					imagegif($imgDst, $dstPath . $imageName);
					break;
				case IMAGETYPE_BMP:
					imagewbmp($imgDst, $dstPath / $imageName);
					break;
			}
			
			//������������ ��������

			imagedestroy($imgSrc);
			imagedestroy($imgDst);
		}
	}

	//���������� ������ ��� ����������� �� �������� ��������

	function getImages()
	{
		$path = IMAGE_SMALL_PATH;
		$dir = opendir($path);
		if($dir == false) throw new Exception("�� ������� ������� ������� � �������������");
		$images = array();
		while (true) 
		{
			$file = readdir($dir);
			if($file == false) break;	//����� ��������

			if(filetype($path . $file) == "dir") continue;	//�������
			
			//�� �����������
			
			$arrExt = array("png", "jpeg", "jpg", "gif", "bmp");
			$ext = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));	
			if(in_array($ext, $arrExt) == false) continue;
			
			//�������� ������� �������� �����������

			if(file_exists(IMAGE_BIG_PATH . $file))
			{
				$images[] = $file;
			}
		}
		closedir($dir);
		return $images;
	}				