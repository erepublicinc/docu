<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 10:05:52
         compiled from "/var/www/newgt/sites/cms/tpl/editUser.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1334824224edfe4faaa3e67-49881121%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'faf5d08ad7a435e98c77a84691724b81b06ecef7' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/editUser.tpl',
      1 => 1323367505,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1334824224edfe4faaa3e67-49881121',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edfe4fabc529',
  'variables' => 
  array (
    'user' => 0,
    'site_code' => 0,
    'record_type' => 0,
    'roles' => 0,
    'role' => 0,
    'checked' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edfe4fabc529')) {function content_4edfe4fabc529($_smarty_tpl) {?>

<script type="text/javascript">

var ckConfig = {toolbar :
    [
     ['Source', '-','Undo','Redo','PasteFromWord'],
     ['Find','Replace','-','SelectAll','RemoveFormat'],
     ['Link', 'Unlink', 'Image'],           
     ['Bold', 'Italic','Underline','TextColor','Blockquote', 'SpecialChar','NumberedList','BulletedList']
 ]};


    $(document).ready(function(){
         $(".slidingDiv").hide();
         $(".show_hide").show();
         $('.show_hide').click(function(){
         $(".slidingDiv").slideToggle();
         });
         CKEDITOR.replace( 'id_body',ckConfig);        
         
    });



function saveContent()
{
 /*
    var comment = $('#id_enter_comment').attr('value') 
	$('#id_comment').attr('value', comment );

    var roles_str = '';
    $('.role_checkbox').each(function(){
        if( $(this).checked())
            roles_str .= $(this).attr('name')
    });
*/
    
    //alert('saving: '+comment);
	$('#id_details_form').submit();
}
    
</script>



<div id='save_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="display:none; background-color:white">
    <textarea id="id_enter_comment">Please enter a comment</textarea>
</div>




<!-- CONTENT -->
  <!-- PAGE TITLE -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">  
            <h2><?php echo $_smarty_tpl->tpl_vars['user']->value->users_first_name;?>
 <?php echo $_smarty_tpl->tpl_vars['user']->value->users_last_name;?>
</h2>              
        </div>
    </div>
    <!-- / PAGE TITLE -->  
    



    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>User Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
">
        <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
        CANCEL</a></h6>

        <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="saveContent();">
        <span class="ui-icon ui-icon-disk float-l mr-5"></span>
        SAVE</span></h6>
 
 
  <br clear="all">
    </div>
          
     
    <div class="ui-widget-content ui-corner-all bk_color2">
        <!-- MAIN FORM -->
         <form id="id_details_form"  method="post">
            <!--
            <div class="error"> 
                <p>
                    Form messages display here!
                </p> 
                <p>
                    Form messages display here!
                </p>                 
            </div>     
            -->      
            <input type="hidden" id="id_changed_roles" name='changed_roles' value="" />                                              
            <input type="hidden" name='users_pk' value="<?php echo $_smarty_tpl->tpl_vars['user']->value->users_pk;?>
" />
            <fieldset>                      
                <div>
                    <label class="grid_2">Active:</label>
                      <input type="checkbox" name="users_active"  <?php if ($_smarty_tpl->tpl_vars['user']->value->users_active==1){?>checked="checked" <?php }?> class=""/> 
                </div>
                
                <div>
                    <label class="grid_12">First Name:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                   
                    </div>
                    <input type="text" name="users_first_name" 
                     value="<?php echo $_smarty_tpl->tpl_vars['user']->value->users_first_name;?>
">
                </div>

                <div>
                    <label class="grid_12">Last Name:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_last_name" class="required"  value="<?php echo $_smarty_tpl->tpl_vars['user']->value->users_last_name;?>
"/>
                </div>
                                
                <div>
                    <label class="grid_12">Email:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_email" class="required"  value="<?php echo $_smarty_tpl->tpl_vars['user']->value->users_email;?>
"/>
                </div>
               
                <div>
                    <i>setting this field will reset the password</i>
                    <label class="grid_12">Password:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_password" value=""/>
                </div>
                                                                                               

                <div>
                    <label class="grid_12">Notes:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                       
                    </div>                            
                    <textarea type="text" name="users_notes" ><?php echo $_smarty_tpl->tpl_vars['user']->value->users_notes;?>
</textarea>
                </div> 
  
               
  
                <div >
                    <table class="grid_6">
                        <?php  $_smarty_tpl->tpl_vars['checked'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['role'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['roles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['checked']->key => $_smarty_tpl->tpl_vars['checked']->value){
$_loop = true;
 $_smarty_tpl->tpl_vars['role']->value = $_smarty_tpl->tpl_vars['checked']->key;
?>
                           <tr><td>  <?php echo $_smarty_tpl->tpl_vars['role']->value;?>
: </td><td><input type="checkbox" name="user_role_<?php echo $_smarty_tpl->tpl_vars['role']->value;?>
" 
                                <?php if ($_smarty_tpl->tpl_vars['checked']->value==1){?> checked="checked"<?php }?> />  
                           </td></tr>
                        <?php } ?>
                    </table>
                </div>
                 
                 
               <br clear="all">  
               
                 
                                         
            </fieldset>              
     
     
     
     
     
     
 
     
     
     
     
     
     
     
     
     
     
     
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     

<!-- end of CONTENT -->
 
  
  
  
  
  
  
  


<?php }} ?>