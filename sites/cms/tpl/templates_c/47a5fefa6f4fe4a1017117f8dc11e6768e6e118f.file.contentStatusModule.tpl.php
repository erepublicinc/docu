<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:25:37
         compiled from "/var/www/newgt/sites/cms/tpl/contentStatusModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16597800644ee0f3113ae299-13362666%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47a5fefa6f4fe4a1017117f8dc11e6768e6e118f' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/contentStatusModule.tpl',
      1 => 1322843038,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16597800644ee0f3113ae299-13362666',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'DATETIME_FORMAT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f31145768',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f31145768')) {function content_4ee0f31145768($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?><!-- contentStatushModule.tpl -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
            <h2>PK: <?php echo $_smarty_tpl->tpl_vars['content']->value->contents_pk;?>
      version: <?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version;?>
</h2> 
			<h6>
            <span class="float-l mr-10">Status:</span> <span class="ui-icon ui-icon-circle-check float-l mr-5"></span>
               <?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version_status;?>
</h6>
               
        </div> 
        <br clear="all">                                                          
    </div> 
	
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        	<h6>
            Created:  <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['content']->value->contents_create_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
<br>
 			Modified: <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['content']->value->contents_update_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</h6>
        </div>                                                           
    </div>
     
<?php }} ?>