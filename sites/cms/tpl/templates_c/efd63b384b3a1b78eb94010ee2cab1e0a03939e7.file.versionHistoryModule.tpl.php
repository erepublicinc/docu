<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:25:37
         compiled from "/var/www/newgt/sites/cms/tpl/versionHistoryModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19367217414ee0f3114adb79-38036454%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'efd63b384b3a1b78eb94010ee2cab1e0a03939e7' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/versionHistoryModule.tpl',
      1 => 1321562696,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19367217414ee0f3114adb79-38036454',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'params' => 0,
    'record_type' => 0,
    'site_code' => 0,
    'p' => 0,
    'content' => 0,
    'h' => 0,
    'DATETIME_FORMAT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f3115b35e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f3115b35e')) {function content_4ee0f3115b35e($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?><script>

</script>

<!-- versionHistoryModule.tpl -->
    <!-- Version History -->       
    <div class="ui-widget-content ui-corner-all p-10 bk_color1">
        <h3>Version History </h3>
        <br clear="all">
        <table class="bk_color3 ui-corner-all">
            	<tr>
                	<td colspan="2" class="align-r ui-corner-tl" width="60%"><h6>Live Version:</h6> </td>    
                    <td class="ui-corner-tr"><?php echo $_smarty_tpl->tpl_vars['params']->value->live_version;?>
</td>            
                </tr>  
            	<tr>
                	<td colspan="2" class="align-r" width="60%"><h6>Preview Version:</h6> </td>    
                    <td><?php echo $_smarty_tpl->tpl_vars['params']->value->preview_version;?>
</td>            
                </tr>
            	  
		</table>         
        <div class="m-10">
        <?php if ($_smarty_tpl->tpl_vars['record_type']->value=='page'){?>
            <form method="post" action="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/pages">
             <input type="hidden" name="pk" value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_pk;?>
" />
             <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_id;?>
" />
        <?php }else{ ?>
            <form method="post" action="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/articles">
            <input type="hidden" name="pk" value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_pk;?>
" />
        <?php }?>
                       
			<table class="condenced">
              <?php  $_smarty_tpl->tpl_vars['h'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->history; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['h']->key => $_smarty_tpl->tpl_vars['h']->value){
$_loop = true;
?>
            	<tr>
                	<td><input type="radio" name="version" value="<?php echo $_smarty_tpl->tpl_vars['h']->value->version;?>
"></td>
                	<td><?php echo $_smarty_tpl->tpl_vars['h']->value->version;?>
</td>
                    <td>
                        <?php if ($_smarty_tpl->tpl_vars['record_type']->value=='page'){?>
                           <a  target='_blank' href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/page/<?php echo $_smarty_tpl->tpl_vars['h']->value->pk;?>
" >
                        <?php }else{ ?> 
                            <a  target='_blank' href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_type;?>
/<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_pk;?>
?version=<?php echo $_smarty_tpl->tpl_vars['h']->value->version;?>
" >
                        <?php }?>
                        <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['h']->value->version_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</a></td>
	            </tr>
                <tr><td colspan="3" style="padding:0 0 0px 40px;">Author:	<?php echo $_smarty_tpl->tpl_vars['h']->value->users_first_name;?>
 <?php echo $_smarty_tpl->tpl_vars['h']->value->users_last_name;?>
</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Comment: <br><?php echo $_smarty_tpl->tpl_vars['h']->value->version_comment;?>
</td></tr>
            	<tr style="padding-bottom:15px;" />
              <?php } ?>       
            </table>
         
        
           
           
         <h6 class="mt-15">
              <input type="submit" name="makelive"    value="Make Live"    style="width:70px;" class="ui-state-default ui-corner-all pr-10 pl-10 p-5" />
              <input type="submit" name="makepreview" value="Make Preview" style="width:70px;" class="ui-state-default ui-corner-all pr-10 pl-10 p-5" />
         </h6>
         
        </form>   
        </div>        
    </div> 


    
    <?php }} ?>