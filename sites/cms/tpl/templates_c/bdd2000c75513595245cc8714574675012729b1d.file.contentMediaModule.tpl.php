<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:43:08
         compiled from "/var/www/newgt/sites/cms/tpl/contentMediaModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10117666174edd651cef34f9-44900132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bdd2000c75513595245cc8714574675012729b1d' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/contentMediaModule.tpl',
      1 => 1320776130,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10117666174edd651cef34f9-44900132',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd651cf0fcc',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd651cf0fcc')) {function content_4edd651cf0fcc($_smarty_tpl) {?><!-- contentMediaModule.tpl -->     
    <!-- ============== MEDIA ================= --> 
	<div class="ui-widget-content ui-corner-all p-10 bk_color2">
	    <h3>Media</h3>
        <div class="float-r headlink">       
            <h6><a href="#" class="ui-state-default ui-corner-all float-r mt-5 pr-10 pl-10 pt-5">
                    <span class="ui-icon ui-icon-pencil float-l mb-5 mr-5"></span>
          				Edit
            </a></h6>
        </div> 
           
        <br clear="all">
        <div class="thumblist">
            <ul>
             	<li><img src="images/GT_traffic_jam_flickr1.jpg" width="90">  <br>Main</li>
             	<li><img src="images/GT_traffic_jam_flickr1.jpg" width="90">  <br>Featured</li>
             	<li><img src="images/GT_traffic_jam_flickr1.jpg" width="90">  <br>Extra</li>
            </ul>
            <br clear="all">
        </div>
    </div>
<?php }} ?>