<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:18:56
         compiled from "/var/www/newgt/sites/cms/tpl/listModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6579280064ee0ed51b8f890-80859625%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ff78d54e301f5b6daf9b392bc5aee0f59ca6234' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/listModule.tpl',
      1 => 1323364734,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6579280064ee0ed51b8f890-80859625',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0ed51d608a',
  'variables' => 
  array (
    'params' => 0,
    'item' => 0,
    'button' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0ed51d608a')) {function content_4ee0ed51d608a($_smarty_tpl) {?><!-- listModule.tpl -->
	<div class="ui-widget-content ui-corner-all p-10 bk_color2">
	    <h3><?php echo $_smarty_tpl->tpl_vars['params']->value->title;?>
</h3>
       
        <br clear="all">
        <div class="m-10">
            <ul class="condenced">
               <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->items; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_loop = true;
?>
                   <li><a href="<?php echo $_smarty_tpl->tpl_vars['params']->value->path;?>
<?php echo $_smarty_tpl->tpl_vars['item']->value->pk;?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value->title;?>
</a></li>
                <?php } ?>
            </ul>
            <br clear="all">
            <div>
              <?php  $_smarty_tpl->tpl_vars['button'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->buttons; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['button']->key => $_smarty_tpl->tpl_vars['button']->value){
$_loop = true;
?>
                 <button href="<?php echo $_smarty_tpl->tpl_vars['button']->value['url'];?>
" style="float:left;" ><?php echo $_smarty_tpl->tpl_vars['button']->value['text'];?>
</button>
              <?php } ?>
            </div>
            <br clear="all">
        </div>
        
    </div>  
<?php }} ?>