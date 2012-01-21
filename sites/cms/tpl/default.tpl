<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
<title>e.Republic {$page_title}</title>
<link rel="stylesheet" href="/css/titan/reset.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/titan/text.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/titan/grid.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/titan/forms.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/titan/cms.css" media="screen" type="text/css" />
<link type="text/css"  href="/css/titan/jquery-ui.css" rel="stylesheet" />	


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<!-- <link  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/> 
 -->
 
 <script type="text/javascript" src="/public/ckeditor/ckeditor.js"></script>
 

<script type="text/javascript">
    
var modelName= "{$model_name}";
{literal} 
	$(function(){

		// Accordion
		$(".accordion").accordion({ header: "h3" });

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
		$('.datepicker').datepicker({
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


        $("#id_chooseSite").change(function(){
            document.location =  "/cms/"+$(this).val() + "/"+modelName;
        });
		
	});



    function formatTheDate(date,time)
    {
        
        date = jQuery.trim(date);
        if(date == '')
            return '1000-01-01 00:00:00';
        
        var dar = date.split('/');
        var str  = dar[2]+ '-' + dar[0]+ '-' + dar[1] ;


        time = jQuery.trim(time);
        if(time == '')
            str += ' 00:00:00';
        else
        {
            var tar = time.split(' ');
            var ampm = jQuery.trim(tar[1]);
            if (ampm == 'pm' || ampm =='PM')   
            {
                var tar1 =  tar[0].split(':');
                var hour = parseInt(tar1[0]) +12;
                str += ' ' +  hour +':'+tar1[1]+':00';
            }    
            else
                str += ' ' +  tar[0] +':00';
        }
       //alert(str);
       return str; 
    }
    
    function prettyTime(datetime) // returns: 4:45 pm
    {
        if(datetime)
        {
            var ampm = ' am';
            var t = datetime.split(' ');
            t = t[1].split(':');
            var h = parseInt(t[0]);
            if( h > 12)
            {
                ampm = ' pm';
                h -= 12;
            }
             return '' + h + ':' + t[1] + ampm;
        }
        else
        {
             var d = new Date();
             var h = d.getHours();
             var am = ' am';
             if( h > 12){
                 h -=12;
                 am= ' pm';
             }
             return  h+ ':' + d.getMinutes() + am;
        }
    }
    
    function prettyDate(datetime) //returns 10/31/2011
    {
        var dstr;
        if(datetime)
        {
             var t = datetime.split(' ');
             t = t[0].split('-');
             dstr = t[1] + '/' + t[2] + '/' + t[0];
        }
        else
        {
            var d = new Date();
             var dstr = ' ' +( d.getMonth()+1) +'/' ;
             if ( d.getDate() < 10)
                dstr = ' ' +( d.getMonth()+1) +'/0'+d.getDate() +'/'+ d.getFullYear();
             else
                dstr = d.getMonth()+1 +'/'+d.getDate() +'/'+ d.getFullYear();
        }
        return dstr;
    }   
</script>
{/literal}

</head>
<body>

<!-- HEADER -->
<div class="container_24">
	<div class="grid_6">
    	<div class="m-10 pt-10 align-c">
            <h3>e.Republic<br>
            CMS Logo</h3>
        </div>
    </div>
    
    <!-- USER ACCOUNT LINKS -->
    <div class="grid_18 align-r">
    	<div class="m-10">
    		Joe User | Dashboard | Messages (3) | Locks | My Tools | Logout 
        </div>
   	</div>
    <!-- / USER ACCOUNT LINKS -->
    
    <!-- Main Tabs -->
    <div class="grid_18 align-r">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Publish</a></li>
				<li><a href="#tabs-2">Design</a></li>
				<li><a href="#tabs-3">Media</a></li>
				<li><a href="#tabs-3">admin</a></li>                
			</ul>
		</div>       
	</div>
    <!-- / Main Tabs --> 
    
    
    <!-- Page title --->
    <div class="grid_24">
    	<div class="ui-widget-content ui-corner-bottom ui-corner-tl bk_color3 p-10" style="margin-top: -11px; border-color:#c4b6a5">
    		       
            <select name="id_chooseSite" id="id_chooseSite" class="select-list-medium" style="width:170px; float:right;" >
                <option class="" value="ALL">All sites</option>
                <option class="" value="ER" {if $site_code == 'ER'} selected="selected" {/if} >eRepublic</option>
                <option class="" value="EM" {if $site_code == 'EM'} selected="selected" {/if} >emergencymgmnt</option>
                <option class="" value="GT" {if $site_code == 'GT'} selected="selected" {/if} >govtech</option>
                <option class="" value="GOV" {if $site_code == 'GOV'} selected="selected" {/if} >governing</option>
                <option class="" value="DC" {if $site_code == 'DC'} selected="selected" {/if} >digitalcommunities</option>
                <option class="" value="CV" {if $site_code == 'CV'} selected="selected" {/if} >converge</option>
                <option class="" value="CDG" {if $site_code == 'CDG'} selected="selected" {/if} >centerdigitalgov</option>
                <option class="" value="CDE" {if $site_code == 'CDE'} selected="selected" {/if} >centerdigitaled</option>
            </select>   
       
            <h2>{$page_title}</h2>
            
		</div>        
    </div>
    <!-- / Page Title -->
    
       
</div>
<!-- / HEADER -->


<!-- CONTENT -->
<div class="container_24">
   

   
  
    <!-- LEFT COLUMN -->
    <div class="grid_6">
    {if $sideModules } 
        {foreach $sideModules.left as $sideModule}
            {include file=$sideModule->template params=$sideModule }
        {/foreach}
    {/if}    
    </div> 
    <!-- END OF  LEFT COLUMN -->

  
  <div class="grid_18">     
 
     {include file="$main_tpl"}  
	
     {foreach $sideModules.center as $sideModule}
            {include file=$sideModule->template params=$sideModule }
     {/foreach}
  </div> 
  <div class="clear"></div>
<br>
</div>
  <!-- /CONTENT -->
  
  
</body>
</html>