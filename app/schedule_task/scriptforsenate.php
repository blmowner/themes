<?php
//ini_set("display_errors","1");
session_start();

include("../../lib/common.php");
checkLogin();

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

/**
 *
 * @create a roman numeral from a number
 *
 * @param int $num
 *
 * @return string
 *
 */
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
    
    $run_start = "001";
    
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
    global $db;
    
    $run_start = "0001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,12)) AS run_id FROM $tblname";
    $sql_slct = $db;
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
function runnum4($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,1,11)) AS run_id FROM $tblname";
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

$sqlforsenate = "SELECT a.id, a.viva_status, d.description AS vivaStatDesc, a.pg_calendar_id, a.student_matrix_no, 
a.pg_thesis_id, defense_date, DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS viva_date, g.id as pg_proposal_id,
DATE_FORMAT(f.defense_stime,'%h:%i%p') AS viva_stime, DATE_FORMAT(f.defense_etime,'%h:%i%p') AS viva_etime, 
f.venue, g.thesis_title
FROM pg_viva a
LEFT JOIN pg_evaluation_viva b ON (b.pg_viva_id = a.id)
LEFT JOIN pg_amendment c ON (c.pg_viva_id = a.id)
LEFT JOIN ref_recommendation d ON (d.id = a.viva_status)
LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = a.pg_thesis_id)
WHERE a.status = 'ARC'
AND a.submit_status = 'CON'
AND (a.viva_status = 'FAI' AND a.appeal_result IS NULL AND CURDATE() > b.end_appeal_date )
AND g.archived_status IS NULL
AND a.id NOT IN (SELECT pg_viva_id FROM pg_senate)";

$result_sqlforsenate = $db->query($sqlforsenate); 
$db->next_record();
$row_cnt_result_sqlforsenate = mysql_num_rows($result_sqlforsenate);

if($row_cnt_result_sqlforsenate > 0) {

	do {
	
		$pgVivaId = $db->f('id');
		$pg_thesis_id = $db->f('pg_thesis_id');
		$student_matrix_no = $db->f('student_matrix_no');
		$pg_proposal_id = $db->f('pg_proposal_id');
		$pg_calendar_id = $db->f('pg_calendar_id');
		
		////////////////work completion////////
		$sqlwork = "SELECT pw.id as pgWorkid, pwe.id as pgEvaWorkId
		FROM pg_thesis pt 
		LEFT JOIN pg_proposal pp ON (pp.pg_thesis_id=pt.id) 
		LEFT JOIN ref_proposal_status rps ON (rps.id = pp.verified_status) 
		LEFT JOIN ref_proposal_status rps2 ON (rps2.id = pp.status) 
		LEFT JOIN pg_work_evaluation pwe ON (pwe.pg_thesis_id = pt.id)
		LEFT JOIN pg_work pw ON (pw.id = pwe.pg_work_id)
		WHERE pt.student_matrix_no = '$student_matrix_no'
		AND pp.verified_status in ('APP','AWC')				
		AND pp.archived_status is null
		AND pt.ref_thesis_status_id_proposal in ('APP','AWC','APC')
		AND pwe.ref_work_marks_id IS NOT NULL 
		AND ((pwe.status = 'APP' AND pwe.ref_work_marks_id IN ('SAT','SUB')) 
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SUB' AND pwe.proposed_marks_id = 'SAT') 
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'SAT' AND pwe.proposed_marks_id = 'SUB')
		OR (pwe.status = 'DIS' AND pwe.ref_work_marks_id = 'NSA' AND pwe.proposed_marks_id IN ('SAT','SUB')))
		AND pw.archived_status IS NULL 
		AND pwe.student_matrix_no = '$student_matrix_no'
		AND pt.id = '$pg_thesis_id'
		AND pp.id = '$pg_proposal_id'
		ORDER BY pt.id";
		
		$dbWork = $dbk;
		$resultwork = $dbWork->query($sqlwork);
		$resultsqlwork = $dbWork->next_record(); 
		$pgWorkid = $dbWork->f('pgWorkid');
		$pgEvaWorkId = $dbWork->f('pgEvaWorkId');
		
		
		$senateNewId = runnum4('id','pg_senate');
		$refNewId = "S".runnum2('reference_no','pg_senate');
		$curdatetime = date("Y-m-d H:i:s");
		
		$sqlinsertsenate = "INSERT INTO pg_senate
		(id, pg_viva_id, reference_no, student_matrix_no, pg_thesis_id, pg_proposal_id, pg_calendar_id, status, 
		insert_by, insert_date, modify_by, modify_date, pg_work_id, pg_work_evaluation_id, submit_status, respond_status)
		VALUES
		('$senateNewId','$pgVivaId','$refNewId', '$student_matrix_no', '$pg_thesis_id', '$pg_proposal_id', '$pg_calendar_id', 'A',
		'$user_id', '$curdatetime', '$user_id', '$curdatetime', '$pgWorkid', '$pgEvaWorkId', 'PND', 'N')";
		
		$result_sqlinsertsenate = $dbg->query($sqlinsertsenate); 

		
	} while($db->next_record());

}


?>

	

