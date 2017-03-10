<?php
include("../../../lib/common.php");

	///// get student semester From other database()
	echo $matrix_no = $_GET['userId'];
	$sql = "SELECT SUBSTRING(sp.semester_id,1,4) AS sem FROM student st
			INNER JOIN student_program sp ON (sp.matrix_no=st.matrix_no) 
			WHERE st.matrix_no = '" . $matrix_no. "'";
	
	if (substr($matrix_no,0,2) != '07') { 
		$dbstupic=$dbc; 
	} 
	else { 
		$dbstupic=$dbc1; 
	}
	$dbstupic->query($sql);
	$nxstupic=$dbstupic->next_record();
	
	$sem = $dbstupic->f("sem");
	
	////retrieve image from other database
	$sql1 = "SELECT mime_type,pic_contents FROM student_pic_".$sem." WHERE matrix_no = '$matrix_no'";
	$result_dbstupic = $dbstupic->query($sql1);


	$row = mysql_fetch_array($result_dbstupic);
	$num_rows = mysql_num_rows($result_dbstupic);


	if($num_rows != '0')
	{
		$s = $row['mime_type'];
		ob_clean();
		header("Content-type: $s");
		echo $row['pic_contents'];

	}
	else
	{
			# standard height & weight if not given
			if(!isset($maxX)) $maxX = 126;
			if(!isset($maxY)) $maxY = 170;
			
			# colour- & textvalues
			$picBG = "0,0,0"; # RGB-value !
			$picFG = "104,104,104"; # RGB-value !
			$copyright = "(c)ITIC MSU"; 
			$font = 1;
			
			# minimal & maximum zoom
			$minZoom = 1; # per cent related on orginal (!=0)
			$maxZoom = 200; # per cent related on orginal (!=0)
			
			# paths
			//$imgpath = "images/"; # ending with "/" !
			$imgpath = "";
			$nopicurl = "../images/default.png"; # starting in $imagepath!!!
			$nofileurl = "../images/default.png"; # starting in $imagepath!!!
			
			if(!isset($image) || empty($image))
				 $imageurl = $imgpath . $nopicurl;
			elseif(! file_exists($imgpath . trim($image)))
				 $imageurl = $imgpath . $nofileurl;
			else
				 $imageurl = $imgpath . trim($image);
			
			# reading image
			$image = getImageSize($imageurl, $info); # $info, only to handle problems with earlier php versions...
			switch($image[2]) {
					 case 1:
						 # GIF image
						 $timg = imageCreateFromGIF($imageurl);
						 break;
				 case 2:
						 # JPEG image
						 $timg = imageCreateFromJPEG($imageurl);
						 break;
				 case 3:
						 # PNG image
						 $timg = imageCreateFromPNG($imageurl);
						 break;
			}
			
			# reading image sizes
			$imgX = $image[0];
			$imgY = $image[1];
			
			# calculation zoom factor 
			$_X = $imgX/$maxX * 100;
			$_Y = $imgY/$maxY * 100;
			
			# selecting correct zoom factor, so that the image always keeps in the given format
			# no matter if it is more higher than wider or the other way around
			if((100-$_X) < (100-$_Y)) $_K = $_X;
			else $_K = $_Y;
			
			# zoom check to the original
			if($_K > 10000/$minZoom) $_K = 10000/$minZoom;
			if($_K < 10000/$maxZoom) $_K = 10000/$maxZoom;
			
			# calculate new image sizes
			$newX = $imgX/$_K * 100;
			$newY = $imgY/$_K * 100;
			
			# set start positoin of the image
			# always centered 
			$posX = ($maxX-$newX) / 2;
			$posY = ($maxY-$newY) / 2;
			
			# creating new image with given sizes
			$imgh = imageCreateTrueColor($maxX, $maxY);
			
			# setting colours
			$cols = explode(",", $picBG);
			$bgcol = imageColorallocate($imgh, trim($cols[0]), trim($cols[1]), trim($cols[2]));
			$cols = explode(",", $picFG);
			$fgcol = imageColorallocate($imgh, trim($cols[0]), trim($cols[1]), trim($cols[2]));
			
			# fill background
			imageFill($imgh, 0, 0, $bgcol);
			
			# create small copy of the image
			imageCopyResampled($imgh, $timg, $posX, $posY, 0, 0, $newX, $newY, $image[0], $image[1]);
			
			# writing copyright note
			imageStringUp($imgh, $font, $maxX-9, $maxY-3, $copyright, $fgcol);
			
			# output the image
			switch($image[2]) {
				 case 1:
				 # GIF image
						 header("Content-type: image/gif");
						 imageGIF($imgh);
				 case 2:
				 # JPEG image
						 header("Content-type: image/jpeg");
						 imageJPEG($imgh);
				 case 3:
				 # PNG image
						 header("Content-type: image/png");
						 imagePNG($imgh);
				 /*case 4:
				 # JPEG image
						 header("Content-type: image/jpg");
						 imageJPG($imgh);*/
			}
			
			# cleaning cache
			imageDestroy($timg);
			imageDestroy($imgh);		
	 }?>