<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:45:26
         compiled from "/var/www/newgt/sites/cms/tpl/moduleUsageModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14487722834ee0f7b603f0b8-50088382%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f3887c11febd1743ddc83cc1a0ddace7f72b0f5' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/moduleUsageModule.tpl',
      1 => 1322607005,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14487722834ee0f7b603f0b8-50088382',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'params' => 0,
    'p' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f7b610a98',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f7b610a98')) {function content_4ee0f7b610a98($_smarty_tpl) {?><!-- moduleUsageModule.tpl -->
	<div class="ui-widget-content ui-corner-all p-10 bk_color2">
	    <h3>Pages that use this module</h3>
        
        <br clear="all">
        <div class="m-10">
            <ul class="condenced">
               <?php  $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->pages; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['p']->key => $_smarty_tpl->tpl_vars['p']->value){
$_loop = true;
?>
                   <li><a href="/cms/<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_site_code;?>
/page/<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_pk;?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_site_code;?>
 <?php echo $_smarty_tpl->tpl_vars['p']->value->pages_title;?>
 </a></li>
                <?php } ?>
            </ul>
        </div>
    </div>  
<?php }} ?>