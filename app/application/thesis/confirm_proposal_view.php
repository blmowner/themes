<?php
include("../../../lib/common.php");
checkLogin();

	//include("../../include/function.php");	
	// Secure the user data by escaping characters 
	// and shortening the input string
	
	//$app_id = "0";
	if (isset($_GET['pid'])) {
		$app_id = (get_magic_quotes_gpc()) ? $_GET['userId'] : addslashes($_GET['userId']);
	}	
	echo $pid;
	//exit();
	
	function clean($input, $maxlength)
	{
		$input = substr($input, 0, $maxlength);
		$input = EscapeShellCmd($input);
		return ($input);
	}
  //$app_id='20150102001';
 // $app_id = clean($app_id, 50);

  //if (empty($app_id))
  //   exit;
	
	$sql="select fu_document_filetype, fu_document_filedata 
			FROM file_upload_proposal
			WHERE pg_proposal_id = '$pid'
			AND attachment_level = 'F'";
			
	echo "sql ".$sql;

	
	$result=$db->query($sql); 
	//$db->next_record();
	
	while ($row = mysql_fetch_array($result))
{
 $pic_contents = $row["fu_document_filedata"];
 $mime_type = $row["fu_document_filetype"];
}
   mysql_free_result($result);
	
	//$mime_type=$db->f('fu_document_filetype');
	//$pic_contents=$db->f('fu_document_filedata');
	
	
	if (strlen($pic_contents)>0) 
	{   
	   
	 
        ob_end_clean();
        ob_start();
		header("Content-type: $mime_type");
		echo $pic_contents;
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
	 }
	 
?>