<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:45:11
         compiled from "/var/www/newgt/sites/cms/tpl/listUsers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7179473654edd6597c04014-39647924%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8cb2ad3bad9599b5ca7fea843a125f1f2e056ce5' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/listUsers.tpl',
      1 => 1323109037,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7179473654edd6597c04014-39647924',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'record_type' => 0,
    'users' => 0,
    'u' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd6597cde5f',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd6597cde5f')) {function content_4edd6597cde5f($_smarty_tpl) {?><!-- listContent.tpl -->

<!--  
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        <span class="ui-icon ui-icon-info-tan float-l mr-10"></span>This view contains items that were created or changed in the last 180 days.       
        </div>
    </div>
-->
   

  <div class="ui-widget-content ui-corner-all bk_color2 p-10">          
            <h6>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="new_<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
">New</a>
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
                <th>Name</th>
                <th>Email</th>
               
                <th>Active</th>
            </tr>
            
            <?php  $_smarty_tpl->tpl_vars['u'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['users']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['u']->key => $_smarty_tpl->tpl_vars['u']->value){
$_loop = true;
?>
            <tr>
                <td><input name="" type="checkbox" value=""></td>
                <td><a href="/cms/gt/<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['u']->value->users_pk;?>
"><?php echo $_smarty_tpl->tpl_vars['u']->value->users_first_name;?>
 <?php echo $_smarty_tpl->tpl_vars['u']->value->users_last_name;?>
</a></td>
                <td><?php echo $_smarty_tpl->tpl_vars['u']->value->users_email;?>
</td>
                <td><span <?php if ($_smarty_tpl->tpl_vars['u']->value->users_active>0){?>  class="ui-icon ui-icon-circle-check " 
                          <?php }else{ ?> class=" "
                          <?php }?>
                    </span>
                </td>
                
            </tr>  
            <?php } ?>
         </table>
     </div> 
<?php }} ?>