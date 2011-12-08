<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:45:31
         compiled from "/var/www/newgt/sites/cms/tpl/listContent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13867218084ee0f3056832a6-60745037%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'acb4bb65436a16f6c8e1c7f5b7c11a79949e62c1' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/listContent.tpl',
      1 => 1323365337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13867218084ee0f3056832a6-60745037',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f3057a22c',
  'variables' => 
  array (
    'record_type' => 0,
    'contents' => 0,
    'c' => 0,
    'DATETIME_FORMAT' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f3057a22c')) {function content_4ee0f3057a22c($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?><!-- listContent.tpl -->

<!--  
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        <span class="ui-icon ui-icon-info-tan float-l mr-10"></span>This view contains items that were created or changed in the last 180 days.       
        </div>
    </div>
-->
   

  <div class="ui-widget-content ui-corner-all bk_color2 p-10">          
            <h6>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
/new">New</a>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Filter</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Copy</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Quick Edit</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Target</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Categorize</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 mr-10 pr-10 pl-10" href="#">Delete</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 ml-20" href="#"><<</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5" href="#"><</a>          
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">View All 125</a>     
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">></a>
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">>></a>                              
            </h6>
    </div>


    <div class="ui-widget-content ui-corner-all bk_color2">
        <table class="datatable">
            <tr>
                <th>&nbsp;</th>
                <th>Title</th>
                <th>Last Modified</th>
                <th>Status</th>
                <th>Assigned To</th>
            </tr>
            
            <?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['contents']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_loop = true;
?>
            <tr>
                <td><input name="" type="checkbox" value=""></td>
                <td><a href="/cms/gt/<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['c']->value->contents_pk;?>
"><?php echo $_smarty_tpl->tpl_vars['c']->value->contents_title;?>
</a></td>
                <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['c']->value->contents_updated_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</td>
                <td><span <?php if ($_smarty_tpl->tpl_vars['c']->value->contents_live_version>0){?>  class="ui-icon ui-icon-circle-check " 
                          <?php }else{ ?> class="ui-icon-tan ui-icon-alert "
                          <?php }?>
                   </span></td>
                <td>eRepublic - Editors</td>
            </tr>  
            <?php } ?>
         </table>
     </div> 
<?php }} ?>