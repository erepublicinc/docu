<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:42:59
         compiled from "/var/www/newgt/sites/cms/tpl/searchModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12378260254edd6513891d49-81041490%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e191185c1a367612692543facb797d067d46f10c' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/searchModule.tpl',
      1 => 1320690936,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12378260254edd6513891d49-81041490',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd651389eaf',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd651389eaf')) {function content_4edd651389eaf($_smarty_tpl) {?><!-- searchModule.tpl -->
<div class="ui-widget-content ui-corner-all bk_color3">
            <div class="m-10">
                <input type="text" name="field-name-here" onclick="this.value='';" onfocus="this.select()" 
                onblur="this.value=!this.value?'SEARCH':this.value;" value="SEARCH" style="width:150px; color:#666666;" />                
                <span href="#"class="float-r mr-10 mt-5">Go</span>
                <h6 class="mt-5 ml-5"><a href="#">Advanced Search</a></h6>
            </div>                                                           
        </div>  <?php }} ?>