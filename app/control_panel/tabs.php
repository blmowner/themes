<?php
    include("../../lib/common.php");
    checkLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<title>Tabs Template</title>
	<link rel="stylesheet" type="text/css" href="http://his.msu.edu.my/theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

	<script src="http://his.msu.edu.my/lib/js/jquery.min2.js"></script>
    <!--script type="text/javascript" src="http://his.msu.edu.my/lib/js/ckeditor/ckeditor.js">
    	CKEDITOR.config.removeButtons = 'Underline,JustifyCenter';
    </script-->
    <script type="text/javascript" src="/app/js/tinymce/js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    });
    </script>
    <script type="text/javascript" src="http://his.msu.edu.my/lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
    <script type="text/javascript">
		$(function() {
			$("#tab1").tabs();
			$("#subtab1").tabs();
		});
	</script>
</head>
<body align="center">
    <div id="tab1" style="width:50%">
        <ul>
            <li><a href="#tabs-1">Personal Record</a></li>
            <li><a href="#tabs-2">Position Applied</a></li>
            <li><a href="#tabs-3">Family Details</a></li>
        </ul>
        <div id="tabs-1">
			<div id="subtab1">
                <ul>
                    <li><a href="#subtab-1">Personal Record</a></li>
                    <li><a href="#subtab-2">Position Applied</a></li>
                    <li><a href="#subtab-3">Family Details</a></li>
                </ul>
				<div id="subtab-1">
					subtab 1
				</div>
                <div id="subtab-2">
                    subtab 2
                </div>
                <div id="subtab-3">
                    subtab 3
                </div>
			</div>
        </div>
        <div id="tabs-2">
            the tab 2
        </div>
        <div id="tabs-3">
            the tab 3
        </div>
    </div>
    
	<textarea name="remark11" cols="30" class="ckeditor" rows="3"></textarea>
    
</body>
</html>