<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:42:59
         compiled from "/var/www/newgt/sites/cms/tpl/listPages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6075181924edd65139064b0-73930228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd8b7596eb67fccf154330308ba0ef0222b3eff68' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/listPages.tpl',
      1 => 1322763024,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6075181924edd65139064b0-73930228',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pages' => 0,
    'site_code' => 0,
    'p' => 0,
    'DATETIME_FORMAT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd651397e33',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd651397e33')) {function content_4edd651397e33($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?>
<!-- listPages.tpl -->
 <div class="ui-widget-content ui-corner-all bk_color2 p-10">          
            <h6>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="new_page">New</a>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Filter</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Copy</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Quick Edit</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Target</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Categorize</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 mr-10 pr-10 pl-10" href="#">Delete</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 ml-20" href="#"><<</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5" href="#"><</a>          
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">View All 125</a>     
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#"></a>
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#"></a>                              
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
            
            <?php  $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['pages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['p']->key => $_smarty_tpl->tpl_vars['p']->value){
$_loop = true;
?>
            <tr>
                <td><input name="" type="checkbox" value=""></td>
                <td><a href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/page/<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_pk;?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_title;?>
</a></td>
                <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['p']->value->pages_version_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</td>
                <td><span <?php if ($_smarty_tpl->tpl_vars['p']->value->pages_status=='READY'){?>  
                                class="ui-icon ui-icon-circle-check float-l mr-5" >
                          <?php }elseif($_smarty_tpl->tpl_vars['p']->value->pages_status=='REVIEW'){?>  
                                class="ui-icon ui-icon-alert float-l mr-5" >
                          <?php }else{ ?> 
                                class=""> -
                          <?php }?>
                   </span></td>
                <td>eRepublic - Editors</td>
            </tr>  
            <?php } ?>
         </table>
     </div> <?php }} ?>