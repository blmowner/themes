<?php

    include("../../../lib/common.php");
    checkLogin();
	$public_id = $_REQUEST['id'];
	
	$sql_thesis="SELECT a.id,a.issue, a.issn_no AS issnId, h.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, 
	a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, a.author,
	b.id AS publisherId,b.publisher_name,
	c.description as typePubDesc, c.id AS pub_type_id,
	e.id AS countryId, e.description AS country_name,
	f.id as volumeId, f.volume as volumeDesc,
	g.id AS authorId, g.name as authorName
	FROM pg_publication_detail a
	LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
	LEFT JOIN pg_publication h ON (h.id = a.issn_no)
	LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
	LEFT JOIN ref_country e ON (e.id = a.country_id)
	LEFT JOIN ref_volume f ON (f.id = a.volume_id)
	LEFT JOIN ref_author g ON (g.id = a.author_id)
	WHERE a.publication_status = 'S'
	AND a.publication_uploader = '$user_id'
	AND a.id = '$public_id'";
				
	$result_sql_thesis = $db->query($sql_thesis);
	$resultsqlthesis = $db->next_record();
	 
	//pg_publication_detail//
	$pid = $db->f('id');
	$issue = $db->f('issue');
	$issn_no = $db->f('issn_no');
	$issnId = $db->f('issnId');
	$volume_id = $db->f('volume_id');
	$publishDate = $db->f('publishDate');
	$title = $db->f('title');
	$publisher_id = $db->f('publisher_id');
	$type_pub_id = $db->f('type_pub_id');
	$abstract = $db->f('abstract');
	$publisher_id = $db->f('publisher_id');
	$website = $db->f('website');
	$country_id = $db->f('country_id');
	$keyword = $db->f('keyword');
	//$author = $db-f('author');
	//ref_publisher//
	$publisherIdDetail = $db->f('publisherId');
	$publisherNameDetail = $db->f('publisher_name');
	//ref_publication_type//
	$typePubDesc = $db->f('typePubDesc');
	$pub_type_id = $db->f('pub_type_id');
	//ref_country//
	$countryId = $db->f('countryId');
	$country_name = $db->f('country_name');
	//ref_volume//
	$volumeId = $db->f('volumeId');
	$volumeDesc = $db->f('volumeDesc');
	//ref_author//
	$authorId = $db->f('authorId');
	$authorName = $db->f('authorName');
		
	/*$authorIdArray = array();
	$authorNameArray = array();
	$pubIdAuthor = array();
	$i = 0;
	$inc = 0;
	
	$sqlauthor = "SELECT id,name,publication_id AS pubIdAuthor 
	FROM ref_author
	WHERE publication_id = '$pid'
	AND insert_by = '$user_id'";
					
	$result_sqlauthor = $dbu->query($sqlauthor);
	while ($dbu->next_record()) 
	{
		$authorId=$dbu->f('id');
		$authorName=$dbu->f('name');
		$pubIdAuthor=$dbu->f('pubIdAuthor');
		
		$authorIdArray[$i] = $authorId;
		$authorNameArray[$i] = $authorName;
		$pubIdAuthor[$i] = $pubIdAuthor;
		$i++;
		$inc++;
	}*/
	
	
function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}    

function runnum($column_name, $tblname) 
{ 
	global $db_klas2;
	
	$run_start = "0001";
	
	$sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
	$sql_slct = $db_klas2;
	$sql_slct->query($sql_slct_max);
	$sql_slct->next_record();

	if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
		$run_id = date("Ymd").$run_start;
	} 
	else 
	{
		$todate = date("Ymd");
		
		if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
			$run_id = $todate.$run_start;
		} 
		else 
		{
			$run_id = $sql_slct->f("run_id") + 1; 
		}
	}
	return $run_id;
}

function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

function runnum3($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

if($_POST['btnadd'] <> "") 
{
	$pdate = $_REQUEST['pdate'];
	$issue = $_REQUEST['issue'];
	$issn = $_REQUEST['issn'];
	$publisher = $_REQUEST['publisher'];
	$volume = $_REQUEST['volume'];
	$title = $_REQUEST['title'];
	$add_publication_type = $_REQUEST['add_publication_type'];
	$website = $_REQUEST['website'];
	$add_country = $_REQUEST['add_country'];
	$author = $_REQUEST['author'];
	$keyword = $_REQUEST['keyword'];
	$abstract = $_REQUEST['abstract'];
	$issnId = $_REQUEST['issnId'];
	
	$pub_id = $_REQUEST['pub_id'];
	$author_Id = $_REQUEST['authorID'];
	
	$msg = Array();

	if(empty($_POST['pdate'])) $msg[] = "<div class=\"error\"><span>Please enter the published date as required below.</span></div>";
	if(empty($_POST['issue'])) $msg[] = "<div class=\"error\"><span>Please enter the issue as required below.</span></div>";
	if(empty($_POST['issn'])) $msg[] = "<div class=\"error\"><span>Please enter the ISSN as required below.</span></div>";
	if(empty($_POST['publisher'])) $msg[] = "<div class=\"error\"><span>Please enter the publisher as required below.</span></div>";
	if(empty($_POST['volume'])) $msg[] = "<div class=\"error\"><span>Please choose the volume as required below.</span></div>";
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['author'])) $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if($author == " " || $author == "," || $author == ", " || $author == " ," || $author == " , ") $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if(empty($_POST['add_country'])) $msg[] = "<div class=\"error\"><span>Please choose the country as required below.</span></div>";
	if(empty($_POST['add_publication_type'])) $msg[] = "<div class=\"error\"><span>Please choose the type of publication as required below.</span></div>";
	if(empty($_POST['website'])) $msg[] = "<div class=\"error\"><span>Please enter the website as required below.</span></div>";
	if(empty($_POST['keyword'])) $msg[] = "<div class=\"error\"><span>Please enter the keyword as required below.</span></div>";
	if(empty($_POST['abstract'])) $msg[] = "<div class=\"error\"><span>Please enter the abstract as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn)) $msg[] = "<div class=\"error\"><span>Please enter issn as required below and ISSN no must be in numeric only.</span></div>";
	
	
	$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
	$result_sql_isn = $dbu->query($sqlissn);
	$resultsqlisn = $dbu->next_record();
	 
	$issnNoId = $dbu->f('id');
		
	$sqldetail = "SELECT id from pg_publication_detail
	WHERE volume_id = '$volume'
	AND issn_no = '$issnNoId'
	AND issue = '$issue'
	AND status = 'A'
	AND publication_status = 'A'";
	$dbu1 = $dbu;
	$result_sqldetail= $dbu1->query($sqldetail);
	$resultsqldetail = $dbu1->next_record();
	 
	$detailIdValid = $dbu->f('id');
	if(!empty($detailIdValid))
	{
		$msg[] = "<div class=\"error\"><span>This publication detail already exist in the system.</span></div>";
	}

	$authorName = explode(",", $_REQUEST['author']);
	$authorIdArray = explode(",", $_REQUEST['authorId']);
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$modify_date= date("Y-m-d H:i:s");
	$publicId = "P".runnum2('id','pg_publication_detail');
	$authorId = "A".runnum3('id','ref_author');
	
	
	//$publicDate = date("Y-m-d");	
		
	$publicDate = strtotime($pdate);
	$newPublicDate = date ( 'Y-m-d' , $publicDate );	
	$i = 0;
	
	//exit();
	
	if(empty($msg))
	{
		if(empty($pub_id))
		{
			if(preg_match('/^[a-zA-Z ,.]+$/', $author))
			{

				$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
				$result_sql_isn = $dbu->query($sqlissn);
				$resultsqlisn = $dbu->next_record();
				 
				$issnNo = $dbu->f('issn_no');
				
				if(empty($issnNo))
				{
					$publicIssnId = "I".runnum2('id','pg_publication');
					$sq3="INSERT INTO pg_publication
					(id ,issn_no, `status`, insert_by, insert_date)
					VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
					$dbe->query($sq3);
					
				}
				else {
					$publicIssnId = $dbu->f('id');
				}
	
				$publicationDetail = "INSERT INTO pg_publication_detail 
				(`id`,`issue`,`volume_id`,`issn_no`,`title`,`publisher_id`,`abstract`,`publish_date`,`type_pub_id`,`website`,
				`country_id`,`publication_uploader`,`insert_by`,`insert_date`,`status`,`keyword`,`publication_status`, author_id, author)
				VALUES
				('$publicId', '$issue', '$volume', '$publicIssnId', '$title', '$publisher', '$abstract', '$newPublicDate', '$add_publication_type', '$website',
				 '$add_country', '$user_id', '$user_id', '$curdatetime', 'A', '$keyword', 'A', '$authorId', '$author')";
						 
				$resultPublicationDetail = $dbe->query($publicationDetail);
			
				$sq2="INSERT INTO ref_author
				(id, `name`, publication_id, insert_by, insert_date)
				VALUES('$authorId', '$author', '$publicId', '$user_id', '$curdatetime1')";
				$dbe->query($sq2);			
		
				/*foreach ($authorName as $authorName) {	
					if (!empty($authorName)) {	
						$authorId = "A".runnum3('id','ref_author');
					
						$sq2="INSERT INTO ref_author
						(id, `name`, publication_id, insert_by, insert_date)
						VALUES('$authorId', '$authorName', '$publicId', '$user_id', '$curdatetime1')";
						$dbe->query($sq2);			
					}
				}*/
				$sqlUploadUpdate="UPDATE file_upload_publication SET publication_id = '$publicId'
				WHERE upload_by = '$user_id'
				AND publication_id = ''";			
				
				$result = $dbe->query($sqlUploadUpdate); 
				
				//$row_cnt = mysql_num_rows($result);
				$sql_thesis="SELECT a.id,a.issue, a.issn_no as issnId, h.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, 
				a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, a.author,
				b.id AS publisherId,b.publisher_name,
				c.description as typePubDesc, c.id AS pub_type_id,
				e.id AS countryId, e.description AS country_name,
				f.id as volumeId, f.volume as volumeDesc,
				g.id AS authorId, g.name as authorName
				FROM pg_publication_detail a
				LEFT JOIN pg_publication h ON (h.id = a.issn_no)
				LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
				LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
				LEFT JOIN ref_country e ON (e.id = a.country_id)
				LEFT JOIN ref_volume f ON (f.id = a.volume_id)
				LEFT JOIN ref_author g ON (g.id = a.author_id)
				WHERE a.publication_status = 'S'
				AND a.publication_uploader = '$user_id'
				AND a.id = '$publicId'";
							
				$result_sql_thesis = $db->query($sql_thesis);
				$resultsqlthesis = $db->next_record();
				 
				//pg_publication_detail//
				$pid = $db->f('id');
				$issue = $db->f('issue');
				$issn_no = $db->f('issn_no');
				$volume_id = $db->f('volume_id');
				$issnId = $db->f('issnId');
				$publishDate = $db->f('publishDate');
				$title = $db->f('title');
				$publisher_id = $db->f('publisher_id');
				$type_pub_id = $db->f('type_pub_id');
				$abstract = $db->f('abstract');
				$publisher_id = $db->f('publisher_id');
				$website = $db->f('website');
				$country_id = $db->f('country_id');
				$keyword = $db->f('keyword');
				//$author = $db-f('author');
				//ref_publisher//
				$publisherIdDetail = $db->f('publisherId');
				$publisherNameDetail = $db->f('publisher_name');
				//ref_publication_type//
				$typePubDesc = $db->f('typePubDesc');
				$pub_type_id = $db->f('pub_type_id');
				//ref_country//
				$countryId = $db->f('countryId');
				$country_name = $db->f('country_name');
				//ref_volume//
				$volumeId = $db->f('volumeId');
				$volumeDesc = $db->f('volumeDesc');
				//ref_author//
				$authorId = $db->f('authorId');
				$authorName = $db->f('authorName');
				
				$msg[] = "<div class=\"success\"><span>Your publication have been add successfully.</span></div>";

			}
			else{
				$msg[] = "<div class=\"error\"><span>Author can only with alphabet, comma and full stop/period</span></div>";
				
				$issue = $_REQUEST['issue'];
				$issn_no = $_REQUEST['issn'];
				$volume_id = $_REQUEST['volume'];
				$publishDate = $_REQUEST['pdate'];
				$title = $_REQUEST['title'];
				$type_pub_id = $_REQUEST['add_publication_type'];
				$abstract = $_REQUEST['abstract'];
				$website = $_REQUEST['website'];
				
				$country_id = $_REQUEST['add_country'];
				
				$sql_country="SELECT description
				FROM ref_country
				WHERE status = 'A'
				AND id = '$country_id'";
						
				$result_sql_country = $dbe->query($sql_country);
				$resultsqlcountry= $dbe->next_record();
				$country_name =  $dbe->f('description');
		
				$keyword = $_REQUEST['keyword'];
				$publisher_id = $_REQUEST['publisher'];
		
				$sql_publish="SELECT publisher_name,id
				FROM ref_publisher
				WHERE status = 'A'
				AND id = '$publisher_id'";
						
				$result_sql_publish = $dbu->query($sql_publish);
				$resultsqlpublish= $dbu->next_record();
				$publisherNameDetail =  $dbu->f('publisher_name');
				$publisherIdDetail =  $dbu->f('id');
				
				$sql_journal="SELECT description
				FROM ref_publication_type
				WHERE status = 'A'
				AND id = '$type_pub_id '";
						
				$sql_journal = $dbb->query($sql_journal);
				$resultsqljournal= $dbb->next_record();
				$typePubDesc =  $dbb->f('description');
		
				
				
				$volumeId = $_REQUEST['volume'];
				$volumeDesc = $_REQUEST['volume'];
				//ref_author//
				$authorName = $_REQUEST['author'];	

			}			
			
		}
		else if(!empty($pub_id))
		{
			if(preg_match('/^[a-zA-Z ,.]+$/', $author))
			{

				$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
				$result_sql_isn = $dbu->query($sqlissn);
				$resultsqlisn = $dbu->next_record();
				 
				$issnNo = $dbu->f('issn_no');
				
				if(empty($issnNo))
				{
					$publicIssnId = "I".runnum2('id','pg_publication');
					
					$sq3="INSERT INTO pg_publication
					(id ,issn_no, `status`, insert_by, insert_date)
					VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
					$dbe->query($sq3);
					
				}
				else {
					$publicIssnId = $dbu->f('id');
				}	
				
				$publicationDetail = "UPDATE pg_publication_detail 
				SET issue = '$issue', volume_id = '$volume', issn_no= '$publicIssnId', title= '$title', publisher_id = '$publisher', abstract='$abstract', publish_date= '$newPublicDate',
				type_pub_id= '$add_publication_type', website = '$website', country_id = '$add_country', keyword = '$keyword', publication_status = 'A', author = '$author'
				WHERE id = '$pub_id'
				AND insert_by is not null
				AND insert_date is not null";	 
				$resultPublicationDetail = $dbe->query($publicationDetail);
				//echo "<br>success";
				
				$sqlupdateauthor = "UPDATE ref_author 
				SET name = '$author', modify_by = '$user_id', modify_date = '$modify_date'
				WHERE publication_id= '$pub_id'
				AND id = '$author_Id'";
				$resultsqlupdateauthor=$dbe->query($sqlupdateauthor);
								
				$sql_thesis="SELECT a.id,a.issue, a.issn_no AS issnId,h.issn_no, a.volume_id, a.title, 
				DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, 
				a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, a.author,
				b.id AS publisherId,b.publisher_name,
				c.description as typePubDesc, c.id AS pub_type_id,
				e.id AS countryId, e.description AS country_name,
				f.id as volumeId, f.volume as volumeDesc,
				g.id AS authorId, g.name as authorName
				FROM pg_publication_detail a
				LEFT JOIN pg_publication h ON (h.id = a.issn_no)
				LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
				LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
				LEFT JOIN ref_country e ON (e.id = a.country_id)
				LEFT JOIN ref_volume f ON (f.id = a.volume_id)
				LEFT JOIN ref_author g ON (g.id = a.author_id)
				WHERE a.publication_status = 'S'
				AND a.publication_uploader = '$user_id'
				AND a.id = '$pub_id'";
							
				$result_sql_thesis = $db->query($sql_thesis);
				$resultsqlthesis = $db->next_record();
				 
				//pg_publication_detail//
				$pid = $db->f('id');
				$issue = $db->f('issue');
				$issn_no = $db->f('issn_no');
				$issnId = $db->f('issnId');
				$volume_id = $db->f('volume_id');
				$publishDate = $db->f('publishDate');
				$title = $db->f('title');
				$publisher_id = $db->f('publisher_id');
				$type_pub_id = $db->f('type_pub_id');
				$abstract = $db->f('abstract');
				$publisher_id = $db->f('publisher_id');
				$website = $db->f('website');
				$country_id = $db->f('country_id');
				$keyword = $db->f('keyword');
				//$author = $db-f('author');
				//ref_publisher//
				$publisherIdDetail = $db->f('publisherId');
				$publisherNameDetail = $db->f('publisher_name');
				//ref_publication_type//
				$typePubDesc = $db->f('typePubDesc');
				$pub_type_id = $db->f('pub_type_id');
				//ref_country//
				$countryId = $db->f('countryId');
				$country_name = $db->f('country_name');
				//ref_volume//
				$volumeId = $db->f('volumeId');
				$volumeDesc = $db->f('volumeDesc');
				//ref_author//
				$authorId = $db->f('authorId');
				$authorName = $db->f('authorName');
				
				$msg[] = "<div class=\"success\"><span>Your publication have been add successfully.</span></div>";
				
			}
			else{
			$msg[] = "<div class=\"error\"><span>Author can only with alphabet, comma and full stop/period</span></div>";
			}			
		
		}
	}
	else
	{
		$issue = $_REQUEST['issue'];
		$issn_no = $_REQUEST['issn'];
		$volume_id = $_REQUEST['volume'];
		$publishDate = $_REQUEST['pdate'];
		$title = $_REQUEST['title'];
		$type_pub_id = $_REQUEST['add_publication_type'];
		$abstract = $_REQUEST['abstract'];
		$website = $_REQUEST['website'];
		
		$country_id = $_REQUEST['add_country'];
		
		$sql_country="SELECT description
		FROM ref_country
		WHERE status = 'A'
		AND id = '$country_id'";
				
		$result_sql_country = $dbe->query($sql_country);
		$resultsqlcountry= $dbe->next_record();
	 	$country_name =  $dbe->f('description');

		$keyword = $_REQUEST['keyword'];
		$publisher_id = $_REQUEST['publisher'];

		$sql_publish="SELECT publisher_name, id
		FROM ref_publisher
		WHERE status = 'A'
		AND id = '$publisher'";
				
		$result_sql_publish = $dbu->query($sql_publish);
		$resultsqlpublish= $dbu->next_record();
	 	$publisherNameDetail =  $dbu->f('publisher_name');
		$publisherIdDetail =  $dbu->f('id');

		$sql_journal="SELECT description
		FROM ref_publication_type
		WHERE status = 'A'
		AND id = '$type_pub_id '";
				
		$sql_journal = $dbb->query($sql_journal);
		$resultsqljournal= $dbb->next_record();
	 	$typePubDesc =  $dbb->f('description');

		
		
		$volumeId = $_REQUEST['volume'];
		$volumeDesc = $_REQUEST['volume'];
		//ref_author//
		$authorName = $_REQUEST['author'];	
	
	}
	

}
/*if($_POST['AddName'] <> "") 
{
	$nameAdd = $_REQUEST['nameAdd'];
	$authorId = "A".runnum3('id','ref_author');
	$curdatetime = date("Y-m-d H:i:s");
	$pub_id = $_REQUEST['pub_id'];
	
	if (empty($_POST['nameAdd'])) $msg[] = "<div class=\"error\"><span>Name need to be feel.</span></div>";
	
	if(!empty($pub_id))
	{
		$sq2="INSERT INTO ref_author
		(id, `name`, publication_id, insert_by, insert_date)
		VALUES('$authorId', '$nameAdd', '$pub_id', '$user_id', '$curdatetime')";
		$dbe->query($sq2);
	}
	else if(empty($pub_id))
	{
		$sq2="INSERT INTO ref_author
		(id, `name`, insert_by, insert_date)
		VALUES('$authorId', '$nameAdd', '$user_id', '$curdatetime')";
		$dbe->query($sq2);
		

	}
	
}*/
if($_POST['btnsave'] <> "") 
{
	$pdate = $_REQUEST['pdate'];
	$issue = $_REQUEST['issue'];
	$issn = $_REQUEST['issn'];
	$publisher = $_REQUEST['publisher'];
	$volume = $_REQUEST['volume'];
	$title = $_REQUEST['title'];
	$add_publication_type = $_REQUEST['add_publication_type'];
	$website = $_REQUEST['website'];
	$add_country = $_REQUEST['add_country'];
	$author = $_REQUEST['author'];
	$keyword = $_REQUEST['keyword'];
	$abstract = $_REQUEST['abstract'];
	$pub_id = $_REQUEST['pub_id'];
	$author_Id = $_REQUEST['authorID'];
	$issnId = $_REQUEST['issnId'];
	
	if(empty($author))
	{
		$author = " ";
	}
		
	$msg = Array();
	/*if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn)) $msg[] = "<div class=\"error\"><span>Please enter ISSN as required below and ISSN no must be in numeric only.</span></div>";*/
	if(empty($_POST['pdate'])) $msg[] = "<div class=\"error\"><span>Please enter the published date as required below.</span></div>";
	if(empty($_POST['issue'])) $msg[] = "<div class=\"error\"><span>Please enter the issue as required below.</span></div>";
	if(empty($_POST['issn'])) $msg[] = "<div class=\"error\"><span>Please enter the ISSN as required below.</span></div>";
	if(empty($_POST['publisher'])) $msg[] = "<div class=\"error\"><span>Please enter the publisher as required below.</span></div>";
	if(empty($_POST['volume'])) $msg[] = "<div class=\"error\"><span>Please choose the volume as required below.</span></div>";
	if(empty($_POST['title'])) $msg[] = "<div class=\"error\"><span>Please enter the title as required below.</span></div>";
	if(empty($_POST['author'])) $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if($author == " " || $author == "," || $author == ", " || $author == " ," || $author == " , ") $msg[] = "<div class=\"error\"><span>Please enter the author as required below.</span></div>";
	if(empty($_POST['add_country'])) $msg[] = "<div class=\"error\"><span>Please choose the country as required below.</span></div>";
	if(empty($_POST['add_publication_type'])) $msg[] = "<div class=\"error\"><span>Please choose the type of publication as required below.</span></div>";
	if(empty($_POST['website'])) $msg[] = "<div class=\"error\"><span>Please enter the website as required below.</span></div>";
	if(empty($_POST['keyword'])) $msg[] = "<div class=\"error\"><span>Please enter the keyword as required below.</span></div>";
	if(empty($_POST['abstract'])) $msg[] = "<div class=\"error\"><span>Please enter the abstract as required below.</span></div>";
	if(!is_numeric($issue)) $msg[] = "<div class=\"error\"><span>Please enter issue as required below and Issue must be in numeric only.</span></div>";
	if(!is_numeric($issn)) $msg[] = "<div class=\"error\"><span>Please enter issn as required below and ISSN no must be in numeric only.</span></div>";
	
	$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
	$result_sql_isn = $dbu->query($sqlissn);
	$resultsqlisn = $dbu->next_record();
	 
	$issnNoId = $dbu->f('id');
		
	$sqldetail = "SELECT id from pg_publication_detail
	WHERE volume_id = '$volume'
	AND issn_no = '$issnNoId'
	AND issue = '$issue'
	AND status = 'A'";
	$dbu1 = $dbu;
	$result_sqldetail= $dbu1->query($sqldetail);
	$resultsqldetail = $dbu1->next_record();
	 
	$detailIdValid = $dbu->f('id');
	if(!empty($detailIdValid))
	{
		$msg[] = "<div class=\"error\"><span>This publication detail already exist in the system.</span></div>";
	}
	
	//$NAME = explode(",", $_REQUEST['author']);
	$authorName = explode(",", $_REQUEST['author']);
	//$authorIdArray = explode(",", $_REQUEST['authorId']);
	
	$curdatetime = date("Y-m-d H:i:s");
	$curdatetime1 = date("Y-m-d H:i:s");
	$modify_date = date("Y-m-d H:i:s");
	$publicId = "P".runnum2('id','pg_publication_detail');
	$authorId = "A".runnum3('id','ref_author');
	
	//$publicDate = date("Y-m-d");	
	
	$publicDate = strtotime($pdate);
	$newPublicDate = date ( 'Y-m-d' , $publicDate );	
	//preg_match('#[^a-z]+$#i', $firstNameSignup)
	//
	//'/^[a-z.\,]+#/i'
		
	if(empty($msg))
	{
		if(empty($pub_id))
		{
			if(preg_match('/^[a-zA-Z ,.]+$/', $author))
			{
				if($pdate == '')
				{
					$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
					$result_sql_isn = $dbu->query($sqlissn);
					$resultsqlisn = $dbu->next_record();
					 
					$issnNo = $dbu->f('issn_no');
					
					if(empty($issnNo))
					{
						$publicIssnId = "I".runnum2('id','pg_publication');
						$sq3="INSERT INTO pg_publication
						(id ,issn_no, `status`, insert_by, insert_date)
						VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
						$dbe->query($sq3);
						
					}
					else {
						$publicIssnId = $dbu->f('id');
					}				
					//echo "lalla";
					$publicationDetail = "INSERT INTO pg_publication_detail 
					(`id`,`issue`,`volume_id`,`issn_no`,`title`,`publisher_id`,`abstract`,`type_pub_id`,`website`,
					`country_id`,`publication_uploader`,`insert_by`,`insert_date`,`status`,`keyword`,`publication_status`, author_id, author)
					VALUES
					('$publicId', '$issue', '$volume', '$publicIssnId', '$title', '$publisher', '$abstract', '$add_publication_type', '$website',
					 '$add_country', '$user_id', '$user_id', '$curdatetime', 'A', '$keyword', 'S', '$authorId', '$author')";
					 $resultPublicationDetail = $dbe->query($publicationDetail);
					 
				}
				else {
					
					$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
					$result_sql_isn = $dbu->query($sqlissn);
					$resultsqlisn = $dbu->next_record();
					 
					$issnNo = $dbu->f('issn_no');
					
					if(empty($issnNo))
					{
						$publicIssnId = "I".runnum2('id','pg_publication');
						$sq3="INSERT INTO pg_publication
						(id ,issn_no, `status`, insert_by, insert_date)
						VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
						$dbe->query($sq3);
						
					}
					else {
						$publicIssnId = $dbu->f('id');
					}
					
					$publicationDetail = "INSERT INTO pg_publication_detail 
					(`id`,`issue`,`volume_id`,`issn_no`,`title`,`publisher_id`,`abstract`,`publish_date`,`type_pub_id`,`website`,
					`country_id`,`publication_uploader`,`insert_by`,`insert_date`,`status`,`keyword`,`publication_status`, author_id, author)
					VALUES
					('$publicId', '$issue', '$volume', '$publicIssnId', '$title', '$publisher', '$abstract', '$newPublicDate', '$add_publication_type', '$website',
					 '$add_country', '$user_id', '$user_id', '$curdatetime', 'A', '$keyword', 'S', '$authorId', '$author')";
					 
					$resultPublicationDetail = $dbe->query($publicationDetail);
				}
				//echo "<br>";
				/*foreach ($authorName as $authorName) {	
					if (!empty($authorName)) {	
						//$authorId = "A".runnum3('id','ref_author');
					
						$sq2="INSERT INTO ref_author
						(id, `name`, publication_id, insert_by, insert_date)
						VALUES('$authorId', '$authorName', '$publicId', '$user_id', '$curdatetime1')";
						$dbe->query($sq2);			
					}
				}*/
				$sq2="INSERT INTO ref_author
				(id, `name`, publication_id, insert_by, insert_date)
				VALUES('$authorId', '$author', '$publicId', '$user_id', '$curdatetime1')";
				$dbe->query($sq2);
				
				$sqlUploadUpdate="UPDATE file_upload_publication SET publication_id = '$publicId'
				WHERE upload_by = '$user_id'
				AND publication_id = ''";			
				
				$result = $dbe->query($sqlUploadUpdate); 
				//$row_cnt = mysql_num_rows($result);
				
				$msg[] = "<div class=\"success\"><span>Your publication have been saved successfully.</span></div>";
				
				$new = 'N';	
				$sql_thesis="SELECT a.id,a.issue, h.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, 
				a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, a.author,
				b.id AS publisherId,b.publisher_name,
				c.description as typePubDesc, c.id AS pub_type_id,
				e.id AS countryId, e.description AS country_name,
				f.id as volumeId, f.volume as volumeDesc,
				g.id AS authorId, g.name as authorName
				FROM pg_publication_detail a
				LEFT JOIN pg_publication h ON (h.id = a.issn_no)
				LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
				LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
				LEFT JOIN ref_country e ON (e.id = a.country_id)
				LEFT JOIN ref_volume f ON (f.id = a.volume_id)
				LEFT JOIN ref_author g ON (g.id = a.author_id)
				WHERE a.publication_status = 'S'
				AND a.publication_uploader = '$user_id'
				AND a.id = '$publicId'";
							
				$result_sql_thesis = $db->query($sql_thesis);
				$resultsqlthesis = $db->next_record();
				 
				//pg_publication_detail//
				$pid = $db->f('id');
				$issue = $db->f('issue');
				$issn_no = $db->f('issn_no');
				$volume_id = $db->f('volume_id');
				$publishDate = $db->f('publishDate');
				$title = $db->f('title');
				$publisher_id = $db->f('publisher_id');
				$type_pub_id = $db->f('type_pub_id');
				$abstract = $db->f('abstract');
				$publisher_id = $db->f('publisher_id');
				$website = $db->f('website');
				$country_id = $db->f('country_id');
				$keyword = $db->f('keyword');
				//$author = $db-f('author');
				//ref_publisher//
				$publisherIdDetail = $db->f('publisherId');
				$publisherNameDetail = $db->f('publisher_name');
				//ref_publication_type//
				$typePubDesc = $db->f('typePubDesc');
				$pub_type_id = $db->f('pub_type_id');
				//ref_country//
				$countryId = $db->f('countryId');
				$country_name = $db->f('country_name');
				//ref_volume//
				$volumeId = $db->f('volumeId');
				$volumeDesc = $db->f('volumeDesc');
				//ref_author//
				$authorId = $db->f('authorId');
				$authorName = $db->f('authorName');
				
				
			
			}
			else{
			//echo "$author";
				$msg[] = "<div class=\"error\"><span>Author can only with alphabet, comma and full stop/period</span></div>";
				
				$issue = $_REQUEST['issue'];
				$issn_no = $_REQUEST['issn'];
				$volume_id = $_REQUEST['volume'];
				$publishDate = $_REQUEST['pdate'];
				$title = $_REQUEST['title'];
				$type_pub_id = $_REQUEST['add_publication_type'];
				$abstract = $_REQUEST['abstract'];
				$website = $_REQUEST['website'];
				
				$country_id = $_REQUEST['add_country'];
				
				$sql_country="SELECT description
				FROM ref_country
				WHERE status = 'A'
				AND id = '$country_id'";
						
				$result_sql_country = $dbe->query($sql_country);
				$resultsqlcountry= $dbe->next_record();
				$country_name =  $dbe->f('description');
		
				$keyword = $_REQUEST['keyword'];
				$publisher_id = $_REQUEST['publisher'];
		
				$sql_publish="SELECT publisher_name, id
				FROM ref_publisher
				WHERE status = 'A'
				AND id = '$publisher'";
						
				$result_sql_publish = $dbu->query($sql_publish);
				$resultsqlpublish= $dbu->next_record();
				$publisherNameDetail =  $dbu->f('publisher_name');
				$publisherIdDetail =  $dbu->f('id');
		
				$sql_journal="SELECT description
				FROM ref_publication_type
				WHERE status = 'A'
				AND id = '$type_pub_id '";
						
				$sql_journal = $dbb->query($sql_journal);
				$resultsqljournal= $dbb->next_record();
				$typePubDesc =  $dbb->f('description');
		
				
				
				$volumeId = $_REQUEST['volume'];
				$volumeDesc = $_REQUEST['volume'];
				//ref_author//
				$authorName = $_REQUEST['author'];	

			}			
	
		}
		else if(!empty($pub_id))
		{
			if(preg_match('/^[a-zA-Z ,.]+$/', $author))
			{
				if($pdate == '')
				{
					$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
					$result_sql_isn = $dbu->query($sqlissn);
					$resultsqlisn = $dbu->next_record();
					 
					$issnNo = $dbu->f('issn_no');
					
					if(empty($issnNo))
					{
						$publicIssnId = "I".runnum2('id','pg_publication');
						$sq3="INSERT INTO pg_publication
						(id ,issn_no, `status`, insert_by, insert_date)
						VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
						$dbe->query($sq3);
						
					}
					else {
						$publicIssnId = $dbu->f('id');
					}
					//$none = '';
					$publicationDetail = "UPDATE pg_publication_detail 
					SET issue = '$issue', volume_id = '$volume', issn_no= '$publicIssnId', title= '$title', publisher_id = '$publisher', abstract='$abstract', 
					type_pub_id= '$add_publication_type', website = '$website', country_id = '$add_country', keyword = '$keyword', publication_status = 'S', modify_by = '$user_id',
					modify_date = '$modify_date' 
					WHERE id = '$pub_id'
					AND insert_by is not null
					AND insert_date is not null";	 
					$dbe->query($publicationDetail);
				}
				else{
				
					$sqlissn = "SELECT id,issn_no FROM pg_publication WHERE issn_no = '$issn'";
					$result_sql_isn = $dbu->query($sqlissn);
					$resultsqlisn = $dbu->next_record();
					 
					$issnNo = $dbu->f('issn_no');
					
					if(empty($issnNo))
					{
						$publicIssnId = "I".runnum2('id','pg_publication');
						$sq3="INSERT INTO pg_publication
						(id ,issn_no, `status`, insert_by, insert_date)
						VALUES('$publicIssnId','$issn', 'A', '$user_id', '$curdatetime1')";
						$dbe->query($sq3);
						
					}
					else {
						$publicIssnId = $dbu->f('id');
					}	
									
					$publicationDetail = "UPDATE pg_publication_detail 
					SET issue = '$issue', volume_id = '$volume', issn_no= '$publicIssnId', title= '$title', publisher_id = '$publisher', abstract='$abstract', 
					publish_date= '$newPublicDate',
					type_pub_id= '$add_publication_type', website = '$website', country_id = '$add_country', keyword = '$keyword', publication_status = 'S', modify_by = '$user_id',
					modify_date = '$modify_date' 
					WHERE id = '$pub_id'
					AND insert_by is not null
					AND insert_date is not null";	 
					$dbe->query($publicationDetail);
				}
					//echo "<br>success";	
		
				/*if(!empty($authorId))
				{
					$row_name = 0;
					$row_id = 0;
					
					$name = array();
					foreach ($authorName as $authorName) 
					{	
						$row_name++;
						
					}
		
					foreach ($authorIdArray as $authorIdArray) 
					{	
						$row_id ++;
						$sqlauthor = "SELECT * FROM ref_author
						WHERE publication_id = '$pub_id'
						AND id = '$authorIdArray'";
						$resultsqlauthor=$dbe->query($sqlauthor);
						$row_cnt = mysql_num_rows($resultsqlauthor);
						//$resultsqlauthor = $dbe->next_record();
						//echo "<br>";
						$author_id = $dbe->f('id');
						if($row_cnt>0)
						{
							
							/*$sqlupdateauthor = "UPDATE ref_author 
							SET name = '$name[$in]'
							WHERE = '$author_id'";
							
						}
					}
					
					if ($row_id == $row_name)			
					{
						foreach ($NAME as $NAME) 
						{	
							echo $sqlupdateauthor = "UPDATE ref_author 
							SET name = '$NAME'
							WHERE publication_id= '$pub_id'";
							if(!empty($NAME))
							{
								$resultsqlupdateauthor=$dbe->query($sqlupdateauthor);
							}
						}	
					}
				}*/
				$sqlupdateauthor = "UPDATE ref_author 
				SET name = '$author', modify_by = '$user_id', modify_date = '$modify_date'
				WHERE publication_id= '$pub_id'
				AND id = '$author_Id'";
				
				$resultsqlupdateauthor=$dbe->query($sqlupdateauthor);
				
				$publicId = $pub_id;
				
				$msg[] = "<div class=\"success\"><span>Your publication have been saved successfully.</span></div>";
					
				$new = 'N';
				
				$sql_thesis="SELECT a.id,a.issue, h.issn_no, a.volume_id, a.title, DATE_FORMAT(a.publish_date,'%d-%b-%Y') AS publishDate, a.type_pub_id, 
				a.abstract, a.publisher_id, a.website, a.country_id, a.keyword, a.author,
				b.id AS publisherId,b.publisher_name,
				c.description as typePubDesc, c.id AS pub_type_id,
				e.id AS countryId, e.description AS country_name,
				f.id as volumeId, f.volume as volumeDesc,
				g.id AS authorId, g.name as authorName
				FROM pg_publication_detail a
				LEFT JOIN pg_publication h ON (h.id = a.issn_no)
				LEFT JOIN ref_publisher b ON (b.id = a.publisher_id)
				LEFT JOIN ref_publication_type c ON (c.id = a.type_pub_id)
				LEFT JOIN ref_country e ON (e.id = a.country_id)
				LEFT JOIN ref_volume f ON (f.id = a.volume_id)
				LEFT JOIN ref_author g ON (g.id = a.author_id)
				WHERE a.publication_status = 'S'
				AND a.publication_uploader = '$user_id'
				AND a.id = '$pub_id'";
							
				$result_sql_thesis = $db->query($sql_thesis);
				$resultsqlthesis = $db->next_record();
				 
				//pg_publication_detail//
				$pid = $db->f('id');
				$issue = $db->f('issue');
				$issn_no = $db->f('issn_no');
				$volume_id = $db->f('volume_id');
				$publishDate = $db->f('publishDate');
				$title = $db->f('title');
				$publisher_id = $db->f('publisher_id');
				$type_pub_id = $db->f('type_pub_id');
				$abstract = $db->f('abstract');
				$publisher_id = $db->f('publisher_id');
				$website = $db->f('website');
				$country_id = $db->f('country_id');
				$keyword = $db->f('keyword');
				//$author = $db-f('author');
				//ref_publisher//
				$publisherIdDetail = $db->f('publisherId');
				$publisherNameDetail = $db->f('publisher_name');
				//ref_publication_type//
				$typePubDesc = $db->f('typePubDesc');
				$pub_type_id = $db->f('pub_type_id');
				//ref_country//
				$countryId = $db->f('countryId');
				$country_name = $db->f('country_name');
				//ref_volume//
				$volumeId = $db->f('volumeId');
				$volumeDesc = $db->f('volumeDesc');
				//ref_author//
				$authorId = $db->f('authorId');
				$authorName = $db->f('authorName');
			
			}
			else{
				
				$msg[] = "<div class=\"error\"><span>Author can only with alphabet, comma and full stop/period</span></div>";
				
				$issue = $_REQUEST['issue'];
				$issn_no = $_REQUEST['issn'];
				$volume_id = $_REQUEST['volume'];
				$publishDate = $_REQUEST['pdate'];
				$title = $_REQUEST['title'];
				$type_pub_id = $_REQUEST['add_publication_type'];
				$abstract = $_REQUEST['abstract'];
				$website = $_REQUEST['website'];
				
				$country_id = $_REQUEST['add_country'];
				
				$sql_country="SELECT description
				FROM ref_country
				WHERE status = 'A'
				AND id = '$country_id'";
						
				$result_sql_country = $dbe->query($sql_country);
				$resultsqlcountry= $dbe->next_record();
				$country_name =  $dbe->f('description');
		
				$keyword = $_REQUEST['keyword'];
				$publisher_id = $_REQUEST['publisher'];
		
				$sql_publish="SELECT publisher_name, id
				FROM ref_publisher
				WHERE status = 'A'
				AND id = '$publisher_id'";
						
				$result_sql_publish = $dbu->query($sql_publish);
				$resultsqlpublish= $dbu->next_record();
				$publisherNameDetail =  $dbu->f('publisher_name');
				$publisherIdDetail =  $dbu->f('id');
		
				$sql_journal="SELECT description
				FROM ref_publication_type
				WHERE status = 'A'
				AND id = '$type_pub_id '";
						
				$sql_journal = $dbb->query($sql_journal);
				$resultsqljournal= $dbb->next_record();
				$typePubDesc =  $dbb->f('description');
		
				
				
				$volumeId = $_REQUEST['volume'];
				$volumeDesc = $_REQUEST['volume'];
				//ref_author//
				$authorName = $_REQUEST['author'];	
				
			}			
	
		}
	}
	else
	{
		$issue = $_REQUEST['issue'];
		$issn_no = $_REQUEST['issn'];
		$volume_id = $_REQUEST['volume'];
		$publishDate = $_REQUEST['pdate'];
		$title = $_REQUEST['title'];
		$type_pub_id = $_REQUEST['add_publication_type'];
		$abstract = $_REQUEST['abstract'];
		$website = $_REQUEST['website'];
		
		$country_id = $_REQUEST['add_country'];
		
		$sql_country="SELECT description
		FROM ref_country
		WHERE status = 'A'
		AND id = '$country_id'";
				
		$result_sql_country = $dbe->query($sql_country);
		$resultsqlcountry= $dbe->next_record();
	 	$country_name =  $dbe->f('description');

		$keyword = $_REQUEST['keyword'];
		$publisher_id = $_REQUEST['publisher'];

		$sql_publish="SELECT publisher_name, id
		FROM ref_publisher
		WHERE status = 'A'
		AND id = '$publisher_id'";
				
		$result_sql_publish = $dbu->query($sql_publish);
		$resultsqlpublish= $dbu->next_record();
	 	$publisherNameDetail =  $dbu->f('publisher_name');
		$publisherIdDetail = $dbu->f('id');

		$sql_journal="SELECT description
		FROM ref_publication_type
		WHERE status = 'A'
		AND id = '$type_pub_id '";
				
		$sql_journal = $dbb->query($sql_journal);
		$resultsqljournal= $dbb->next_record();
	 	$typePubDesc =  $dbb->f('description');

		
		
		$volumeId = $_REQUEST['volume'];
		$volumeDesc = $_REQUEST['volume'];
		//ref_author//
		$authorName = $_REQUEST['author'];	
	
	}
}

?>
<style>
.idd {
font-family:Verdana;
font-size:11px;
}
.style1 {color: #FF0000}
</style>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Publication</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	<script src="../../../lib/js/jquery.mask_input-1.3.js"></script>
	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.core.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.widget.js"></script>
	<script src="../../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
	<script type="text/javascript" src="../../../../lib/js/rightClick.js"></script>
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
</head>
<script>
function newAttachment(pid) {
    var ask = window.confirm("Ensure your publication has been saved before proceed or otherwise the last change will be discarded.\nClick OK to proceed or CANCEL to stay on the same page.");
    if (ask) {
		document.location.href = "../publication/publication_attachment.php?pid=" + pid;

    }
}

</script>
<script type="text/javascript">
$(document).ready(function() {
    $("#btnReset").click(function(){
       $("#pdate").val("");
       $("#issue").val("");
       $("#volume").val("");
       $("#title").val("");
    }); 
});

</script>
<body>
<!--<div class="margin-5 padding-5 outer">-->
<?php
if(!empty($msg)) 
{
    foreach($msg as $key) {
       echo $key;
    }
}

?>
<script>
// intilize datepicker at document ready or load..
$(document).ready(function(){

        setdatepicker();

});
</script>
    <form method="post" id="form-set">
		<?
			$sql = "SELECT id, description
			FROM ref_publication_type
			WHERE status = 'A'
			ORDER BY description";

			$result_sql = $dba->query($sql);
			$dba->next_record();
			$row_cnt_sql = mysql_num_rows($result_sql);			
			?>
	<fieldset><legend>Publication</legend>
	<table class="idd">
		<tr>
			<td height="32" style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Publication Date</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><input type="text" readonly="readonly" id="pdate" name="pdate" 
			value="<?php if(isset($publishDate)){ echo $publishDate;} elseif(isset($publishDate)){ echo $_REQUEST['pdate']; }?>"/>
			<input type="hidden" id="pub_id" name="pub_id" value="<?=$pid?>" /></td>
			<?	$jscript .= "\n" . '$( "#pdate" ).datepicker({
												changeMonth: true,
												changeYear: true,
												yearRange: \'-100:+0\',
												dateFormat: \'dd-M-yy\',
												maxDate: new Date
											});';				 
				?>
		</tr>
		
		<? $sql3 = "SELECT id, volume
			FROM ref_volume
			WHERE status = 'A'
			ORDER BY order_by";
					
			$result_sql3 = $dbg->query($sql3);
		?>			<!--<input type = "text" id="volume" name="volume" value="<?=$volume_id?>"/>-->
		<tr>
			<td rowspan="2" style="background-color: rgba(105, 162, 255, 0.7);"> <font color="#FF0000">*</font><b></b>ISSN </td>
			<td style="background-color: rgba(0, 0, 0, 0.1); ">
			<input type = "text" id="issn" name="issn" value="<?=$issn_no?>"/>
			<input type = "hidden" id="issnId" name="issnId" value="<?=$issnId?>"/>
			</td>
			<td style="background-color: rgba(105, 162, 255, 0.7);"> <font color="#FF0000">*</font><b></b>Volume </td>
			<td style="background-color: rgba(0, 0, 0, 0.1); ">
			<select name="volume" id="volume">
				<option value="" selected="selected"></option>
				<? 
					while ($dbg->next_record()) {
						$volId=$dbg->f('id');
						$volume=$dbg->f('volume');
						$chapterDesc=$dbg->f('description');
						if($volume_id == $volId)
						{?>
						<option value="<?=$volumeId?>" selected="selected"><?=romanNumerals($volumeDesc)?> </option>

						<? 
						}
						else{
						?><option value="<?=$volId?>"><?=romanNumerals($volume)?> </option><?
						}
					};
				?></select> </td>
			
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Issue</td>
			<td style="background-color: rgba(0, 0, 0, 0.1); "><input type = "text" id="issue" name="issue" value="<?=$issue?>"/></td>
		</tr>
		<tr>
			<td colspan="6" style="background-color: rgba(0, 0, 0, 0.1); ">
			<span class="style1">Note</span>: Only numeric can be used
			</p></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Title </td>
		  <td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><p>
		  <textarea name="title" cols="50" rows="2" id="title" ><?=$title?>
		  </textarea>
			</td>
		</tr>
		<tr>
		<?
			$sql1 = "SELECT id, publisher_name
			FROM ref_publisher
			WHERE status= 'A'
			ORDER BY id";

			$result_sql1 = $dbj->query($sql1);
			$dbj->next_record();
			$row_cnt_sql = mysql_num_rows($result_sql1);			
			?>

			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Publisher</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
				<select name="publisher" id="publisher">
					<option value="" selected="selected"></option>
					<?
					do {
						$publisherId = $dbj->f('id');
						$publisherName = $dbj->f('publisher_name');
						
						/*$publisherIdDetail = $db->f('publisherId');
						$publisherNameDetail = $db->f('publisher_name');*/
	
						if ($publisherId==$publisher_id) {
							?><option value="<?=$publisherIdDetail?>" selected="selected"><?=$publisherNameDetail?></option><?
						}
						else {						
							?><option value="<?=$publisherId?>"><?=$publisherName?></option><? 
							}					
					} while ($dbj->next_record());?>				
			  </select>		  </td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Author</td>
		  <td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
			<textarea cols="50" type="text" id="author" name="author"><?=$authorName?></textarea>
			<input type = "hidden" name = "authorID" id = "authorID" value= "<?=$authorId?>"/>
			<br>
			<span class="style1">Note</span>: Only alphabetical, commas and fullstop/period can be used
			<?
				/*for ($i=0; $i<$inc; $i++)
				{
					if (!empty($authorIdArray[$i]))
					{
						echo $authorNameArray[$i].",";
					}
				}
				for ($i=0; $i<$inc; $i++)
				{
					if (!empty($authorIdArray[$i]))
					{
						//echo $authorIdArray[$i].",";
						echo '<input type = "text" name = "idauthor[]" id = "idauthor" value= "'.$authorIdArray[$i].'"/><br>';
					}
				}*/

			?>			</td>
		</tr>
		<!--<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);">Author 2</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
			<input type = "text" name = "nameAdd" id = "nameAdd" value= ""/>
			<input type="submit" name="AddName" id="AddName"  value="Add Name"/><br />
			<?
			for ($i=0; $i<$inc; $i++)
				{
					if (!empty($authorIdArray[$i]))
					{	
						//<input type = "text" name = "namee[]" id = "namee" value= "'.$authorNameArray[$i].'"/>
						echo "$authorNameArray[$i],";
						echo '<input type = "hidden" name = "idauthor[]" id = "idauthor" value= "'.$authorIdArray[$i].'"/>';
						
					}
				}	
			?>
			</td>
		</tr>-->
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Type of Publication</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><select name="add_publication_type" id="add_publication_type">
				<?
				do {
					$publicationTypeId = $dba->f('id');
					$publicationTypeDesc = $dba->f('description');
					$defaultId = $dba->f('default_id');
					
					//$typePubDesc = $db->f('typePubDesc');
					//$pub_type_id = $db->f('pub_type_id');
					if ($publicationTypeId==$type_pub_id) {
						?><option value="<?=$type_pub_id?>" selected="selected"><?=$typePubDesc?></option><?
					}
					else if($defaultId=="Y" && empty($type_pub_id))
					{
						?><option value="<?=$publicationTypeId?>" selected="selected"><?=$publicationTypeDesc?></option><?
					}					
					else {						
						?><option value="<?=$publicationTypeId?>"><?=$publicationTypeDesc?></option><? 
						}					
				} while ($dba->next_record());?>				
																															
				</select></td>			
		</tr>
		<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Website</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
			<input type="text" id="website" name="website" value="<?=$website?>"/></td>
		<tr>
			<?
			$sql = "SELECT id, description, default_id
			FROM ref_country
			WHERE status = 'A'
			ORDER BY description";

			$result_sql = $dbb->query($sql);
			$dbb->next_record();
			$row_cnt_sql = mysql_num_rows($result_sql);			
			?>

		<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Country</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><select name="add_country" id="add_country">
              <?
				do {
					$countryId = $dbb->f('id');
					$countryDesc = $dbb->f('description');
					$defaultId = $dbb->f('default_id');
					//$country_name
					//$country_id
					if ($defaultId=="Y" && empty($country_id)) 
					{ 
						?><option value="<?=$countryId?>" selected="selected"><?=$countryDesc?></option><? 
					}
					else if($country_id == $countryId)
					{
						?><option value="<?=$country_id?>" selected="selected"><?=$country_name?></option><?
					}
					else 
					{
						?><option value="<?=$countryId?>"><?=$countryDesc?></option><?														
					}
				} while ($dbb->next_record());?>
            </select></td>
		</tr>
		<? 
		$sqlUpload="SELECT COUNT(*) as total  FROM file_upload_publication
		WHERE publication_id = '$pid' 
		AND upload_by = '$user_id'";			
		
		$result = $dbf->query($sqlUpload); 
		$dbf->next_record();
		$attachment = $dbf->f('total');
		
		if($attachment == '0')
		{
			$a = '';
		}
		else
		{
			$a = "(".$attachment.")";
		}
		?>

		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Attachment</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); "><button type="button" name="btnAttachment" value="Attachment" onClick="return newAttachment('<?=$pid?>')" >
			Attachment <FONT COLOR="#FF0000"><sup><?=$a?></sup></FONT></button></td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b><b></b>Keyword</td>
			<td style="background-color: rgba(0, 0, 0, 0.1); " colspan="5"><input type = "text" id="keyword" size="50" name="keyword" value="<?=$keyword?>"/>			</td>
		</tr>
		<tr>
			<td style="background-color: rgba(105, 162, 255, 0.7);"><font color="#FF0000">*</font><b></b>Abstract</td>
			<td colspan="5" style="background-color: rgba(0, 0, 0, 0.1); ">
			<textarea cols="50" type="text" id="abstract" name="abstract"><?=$abstract?></textarea>			</td>
		</tr>
	</table>
	<table>
		<tr>
		  <td align="right"><!--<input type="submit" id="btnsave" name="btnsave" value = "Save as Draft" />-->
			<input type="submit" id="btnsave" name="btnsave" value = "Save" />
			<input type="submit" id="btnadd" name="btnadd" value = "Add" />
			<!--<INPUT TYPE="submit" id = "reset" name = "reset" VALUE="New" />-->
			<input type="button" name="btnBack" value="Back" onClick="javascript:document.location.href='../publication/list_of_publication.php';" /></td>
		  <td align="left"><span style="color:#FF0000; font-family:Verdana;font-size:11px;">Note:</span> <span style="font-family:Verdana;font-size:11px;">Field marks with (</span><span style="color:#FF0000">*</span><span style="font-family:Verdana;font-size:11px;">) is compulsory.</span><br />
            <span style="font-family:Verdana;font-size:11px;"></span></td>
		</tr>
	</table>
	</fieldset>
    </form>
<script>
	<?=$jscript;?>
</script>
<!--</div>-->
</body>
</html>