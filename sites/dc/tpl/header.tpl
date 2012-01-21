<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>dc layout</title>
<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/text.css" />
<link rel="stylesheet" href="css/grid_layout.css" />
<link rel="stylesheet" href="css/dc.css" />

<link type="text/css" href="css/humanity/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript">
    $(function(){

        // Accordion
        $("#accordion").accordion({ header: "h3" });

        // Tabs
        $('#tabs').tabs();


        // Dialog
        $('#dialog').dialog({
            autoOpen: false,
            width: 600,
            buttons: {
                "Ok": function() { 
                    $(this).dialog("close"); 
                }, 
                "Cancel": function() { 
                    $(this).dialog("close"); 
                } 
            }
        });
        
        // Dialog Link
        $('#dialog_link').click(function(){
            $('#dialog').dialog('open');
            return false;
        });

        // Datepicker
        $('#datepicker').datepicker({
            inline: true
        });
        
        // Slider
        $('#slider').slider({
            range: true,
            values: [17, 67]
        });
        
        // Progressbar
        $("#progressbar").progressbar({
            value: 20 
        });
        
        //hover states on the static widgets
        $('#dialog_link, ul#icons li').hover(
            function() { $(this).addClass('ui-state-hover'); }, 
            function() { $(this).removeClass('ui-state-hover'); }
        );
        
    });
</script>
</head>

<body>

<div id="#TopBanners" class="container_24 mt_20 align_c">
	<div class="grid_20">
    	<img src="img/TEMP_banner1.gif">
    </div>
    <div class="grid_4">
    	<img src="img/TEMP_banner2.gif">
    </div>
</div>


<div id="ContentArea" class="container_24">
  <div class="grid_24">
      <img src="img/dc_logo.jpg" class="m_20">
  </div>
  <div class="grid_24">
    <ul id="MainNav">
      <li><a href="#">Home</a></li>
      <li><a href="#">About</a></li>
      <li><a href="#">Issues</a></li>
      <li><a href="#">Library</a></li>
      <li><a href="#">Events</a></li>
      <li><a href="#">Blogs</a></li>
      <li><a href="#">County Surveys</a></li>
      <li><a href="#">City Surveys</a></li>
      <li><a href="#">Advertise</a></li>      
    </ul>
  </div> 
  <div class="clear"></div>

<!-- =================== END OF HEAD.tpl ================== -->

