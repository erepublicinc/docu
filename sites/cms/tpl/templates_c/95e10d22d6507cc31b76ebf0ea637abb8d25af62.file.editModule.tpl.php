<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 10:06:14
         compiled from "/var/www/newgt/sites/cms/tpl/editModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3002133404ee0f7b6110071-14931316%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '95e10d22d6507cc31b76ebf0ea637abb8d25af62' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/editModule.tpl',
      1 => 1323367534,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3002133404ee0f7b6110071-14931316',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f7b627529',
  'variables' => 
  array (
    'content' => 0,
    'site_code' => 0,
    'record_type' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f7b627529')) {function content_4ee0f7b627529($_smarty_tpl) {?>

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
//	$('#id_changed_targets').attr('value', gatherChangedTargets()); 
	$('#id_status').attr('value', $('#id_status_dropdown').attr('value') );
    
    var $dialog = $('#save_dialog').dialog({title: 'Save', modal:true
        ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                   {text: "Save", click: function() { saveContentPart2(); }  }
                  ]
    });
    
	

}

function saveContentPart2()
{
    var comment = $('#id_enter_comment').attr('value') 
	$('#id_comment').attr('value', comment );

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
        <h2><?php echo $_smarty_tpl->tpl_vars['content']->value->contents_title;?>
</h2>
        <h6><?php echo $_smarty_tpl->tpl_vars['content']->value->contents_type;?>
</h6>          
        </div>
    </div>
    <!-- / PAGE TITLE -->  
    



    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>Module Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['record_type']->value;?>
">
        <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
        CANCEL</a></h6>

        <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="saveContent();">
        <span class="ui-icon ui-icon-disk float-l mr-5"></span>
        SAVE</span></h6>        
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
            <input type="hidden" id="id_changed_targets" name='changed_targets' value="" />                                              
            <input type="hidden" name='contents_pk' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_pk;?>
" />
            <input type="hidden" name='contents_latest_version' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_latest_version;?>
" />
            <input type="hidden" id="id_comment" name='contents_version_comment' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version_comment;?>
" />
            <input type="hidden" id="id_status" name='contents_version_status' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version_status;?>
" />        
            <fieldset>                      

                <div>
                    <label class="grid_12">Title:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>
                    <input type="text" name="contents_title" class="required" 
                     value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_title;?>
">
                </div>

                <div>
                    <label class="grid_12">Display Title:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="contents_display_title" class="required"  value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_display_title;?>
"/>
                </div>
                                
                <div>
                    <label class="grid_12">Author:</label>
                    <select  name="contents_main_authors_fk" class="required">
                                 <option value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_main_authors_fk;?>
" ><?php echo $_smarty_tpl->tpl_vars['content']->value->users_first_name;?>
 <?php echo $_smarty_tpl->tpl_vars['content']->value->users_last_name;?>
</option>
                    </select> 
                </div>
                <div>
                    <label class="grid_12">Meta Keywords:</label>
                  
                    <input type="text" name="">
                </div>   
                                                                                               

                <div>
                    <label class="grid_12">Notes:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                        <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <textarea type="text" name="contents_summary" ><?php echo $_smarty_tpl->tpl_vars['content']->value->contents_summary;?>
</textarea>
                </div> 
                
                <div>
                    <label class="grid_12">php class:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="modules_php_class" class="required"   value="<?php echo $_smarty_tpl->tpl_vars['content']->value->modules_php_class;?>
"/>
                </div>              
                
                 <div>
                    <label class="grid_12">json params:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="modules_json_params" class=""  value="<?php echo $_smarty_tpl->tpl_vars['content']->value->modules_json_params;?>
"/>
                </div>  
                
                <div>
                    <label class="grid_12">site code: (the site that this module is for)</label>
                    <select  name="modules_site_code" class="required">
                                 <option value="COMMON" >COMMON</option>
                                 <option value="GOV" >GOV</option>
                                 <option value="GT" >GT</option>
                                 <option value="EM" >EM</option>
                                 <option value="CV" >CV</option>
                                 <option value="CDG" >CDG/CDE</option>
                                 <option value="ER" >ER</option>                               
                    </select> 
                </div>             
                
                
                <div>
                    <label class="grid_12">Body:</label>                   
                    <div class="float-r" style="width: 100px;">
                      <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                      <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>     
                     <br clear="all">                        
                    <textarea id="id_body" type="text" name="modules_body" rows="25" class=""><?php echo $_smarty_tpl->tpl_vars['content']->value->modules_body;?>
</textarea>
                </div>   
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     
    
   



<!-- end of CONTENT -->
 
  
  
  
  
  
  
  


<?php }} ?>