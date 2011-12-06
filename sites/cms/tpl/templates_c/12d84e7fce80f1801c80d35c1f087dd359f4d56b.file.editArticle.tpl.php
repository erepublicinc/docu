<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:43:09
         compiled from "/var/www/newgt/sites/cms/tpl/editArticle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16069037394edd651d0d9eb3-00017176%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12d84e7fce80f1801c80d35c1f087dd359f4d56b' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/editArticle.tpl',
      1 => 1322788400,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16069037394edd651d0d9eb3-00017176',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'site_code' => 0,
    'DATETIME_FORMAT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd651d20993',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd651d20993')) {function content_4edd651d20993($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?><!-- ===================== editArticle.tpl ===============     -->

<?php echo $_smarty_tpl->getSubTemplate ("saveContentDialog.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>




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
    
</script>



    

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
        <div class="grid_6 m-10"> <h3>Article Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/articles">
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
            <input type="hidden"id="id_changed_targets" name='changed_targets' value="" />                                              
            <input type="hidden" name='contents_pk' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_pk;?>
" />
            <input type="hidden" name='contents_latest_version' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_latest_version;?>
" />
            <input type="hidden" id="id_comment" name='contents_version_comment' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version_comment;?>
" />
            <input type="hidden" id="id_status" name='contents_version_status' value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_version_status;?>
" />     
            <input type="hidden" id="id_make_preview" name='make_preview'  />  
            <input type="hidden" id="id_make_live" name='make_live'  />   
               
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
                    <input type="text" name="contents_display_title" class="required" 
                    value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_display_title;?>
">
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
                    <label class="grid_12">URL Resource Name: <a href="#">Edit</a><br>
                    <span>Do not include the '.html' suffix, it will be appended to the URL automatically.
                    </span></label>
                    <input type="text" name="contents_url_name" class="restricted" 
                    value="<?php echo $_smarty_tpl->tpl_vars['content']->value->contents_url_name;?>
">
                </div>                                                                                             
                <div>
                    <label class="grid_12">Published Date:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="contents_create_date" class="required" 
                    value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['content']->value->contents_create_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
">                                       
                </div>
                <div>
                    <label class="grid_12">Abstract:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                        <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <textarea type="text" name="contents_summary" ><?php echo $_smarty_tpl->tpl_vars['content']->value->contents_summary;?>
</textarea>
                </div> 
                <div>
                    <label class="grid_12">Body:</label>                   
                    <div class="float-r" style="width: 100px;">
                      <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                      <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>     
                     <br clear="all">                        
                    <textarea id="id_body" type="text" name="contents_article_body" rows="25" class="required"><?php echo $_smarty_tpl->tpl_vars['content']->value->contents_article_body;?>
</textarea>
                </div>   
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     
    
   



<!-- end of CONTENT -->
 
  
  
  
  
  
  
  

<?php }} ?>