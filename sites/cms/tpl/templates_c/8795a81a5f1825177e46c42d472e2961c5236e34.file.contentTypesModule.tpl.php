<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:42:59
         compiled from "/var/www/newgt/sites/cms/tpl/contentTypesModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13293028364edd65138a1194-76113545%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8795a81a5f1825177e46c42d472e2961c5236e34' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/contentTypesModule.tpl',
      1 => 1322682581,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13293028364edd65138a1194-76113545',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'site_code' => 0,
    'params' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd65138f522',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd65138f522')) {function content_4edd65138f522($_smarty_tpl) {?><!-- contentTypesModule.tpl -->
		<!-- Accordion -->
		<div id="accordion_content_types" class="accordion">
			<div>
				<h3><a href="#">Content</a></h3>
				<div>
                    <a href="#">Ad Module</a>
    				<a href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/articles">Articles</a>
    				<a href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/modules">Modules</a>              
    				<a href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/pages">Pages</a>          
			    </div>	
			</div>
			<div>
				<h3><a href="#">Media</a></h3>
				<div><a href="#">Images</a>
    				<a href="#">Audio</a>
    				<a href="#">Video</a>
    				<a href="#">Documents</a>
    				<a href="#">Binary</a>
    				<a href="#">Import Media</a>
    			</div>       
			</div>
			<div>
				<h3><a href="#">Placement</a></h3>
				<div>
                 <?php  $_smarty_tpl->tpl_vars['page'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->pages; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['page']->key => $_smarty_tpl->tpl_vars['page']->value){
$_loop = true;
?>
                    <a href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/placement/<?php echo $_smarty_tpl->tpl_vars['page']->value->pages_id;?>
"><?php echo $_smarty_tpl->tpl_vars['page']->value->pages_title;?>
</a>
    			 <?php } ?>	
				</div>
			</div>
		</div><?php }} ?>