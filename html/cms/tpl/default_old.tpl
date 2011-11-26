<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>e.Republic CMS </title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
<link  href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/> 
<script type="text/javascript" src="/common/ckeditor/ckeditor.js"></script>



<link rel="stylesheet" href="/css/reset.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/text.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/grid.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/forms.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/cms.css" media="screen" type="text/css" />
<link rel="stylesheet" href="/css/thickbox.css" media="screen" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>





</head>
<body>

<!-- HEADER -->
<div class="container_24">
    <div class="grid_4">
        e.Republic<br>
        CMS Logo
    </div>
    <div class="grid_20 align-r">Joe User | Dashboard | Messages (3) | Locks | My Tools | Logout </div>
    <div class="grid_20 align-r">Publish | Design | Media | Admin</div>
</div>
<!-- / HEADER -->


    {if $main_tpl_content}
        {$main_tpl_content}
    {elseif $main_tpl}
        {include file="$main_tpl"} {* website=$webpage->mWebsite *}
    {/if}
  
 
</body>
</html>
