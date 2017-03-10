<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="softboxkid" />

	<title>Untitled 3</title>
    <script type="text/javascript">
        function getHTTPObject() { 
          var xmlhttp; 
        
          if(window.XMLHttpRequest){ 
            xmlhttp = new XMLHttpRequest(); 
          } 
          else if (window.ActiveXObject){ 
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
            if (!xmlhttp){ 
                xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
            } 
            
        } 
          return xmlhttp; 
        } 
        var http = getHTTPObject();
        
        
        window.onbeforeunload = doConfirm;
        
        var resetValue = true;
        
        function doConfirm() {
        	var URL;
        	if(resetValue) {
        		URL = "logout.php";
        		http.open("POST", URL, false);
        		http.send();
        		resetValue = false;
        	}
        	
        }
    </script>
</head>

<body>



</body>
</html>