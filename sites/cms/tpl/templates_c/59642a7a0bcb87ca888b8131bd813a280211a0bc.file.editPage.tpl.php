<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:27:28
         compiled from "/var/www/newgt/sites/cms/tpl/editPage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6290056024ee0f3807130a0-53364484%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59642a7a0bcb87ca888b8131bd813a280211a0bc' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/editPage.tpl',
      1 => 1322762955,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6290056024ee0f3807130a0-53364484',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
    'p' => 0,
    'site_code' => 0,
    'site_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f38089925',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f38089925')) {function content_4ee0f38089925($_smarty_tpl) {?>

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

function savePage()
{
   
 //   $('#id_status').attr('value', $('#id_status_dropdown').attr('value') );
    
   
    
    var $dialog = $('#save_dialog').dialog({title: 'Save', modal:true
        ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                   {text: "Save", click: function() { savePagePart2(); }  }
                  ]
    });
    
    

}

function savePagePart2()
{
	var moduleData = collectModuleInfo();         
	$('#id_module_data').attr('value', moduleData );
    
    var comment = $('#id_enter_comment').attr('value') 
    $('#id_comment').attr('value', comment );

    //alert('saving: '+comment);
    $('#id_details_form').submit();
}
    
</script>



<div id='save_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="display:none; background-color:white">
    <textarea id="id_enter_comment">Please enter a comment</textarea>
</div>


<div class="ui-widget-content ui-corner-all">
    <div class="m-10">  
    <h2><?php echo $_smarty_tpl->tpl_vars['content']->value->pages_title;?>
</h2>
    <h6><?php echo $_smarty_tpl->tpl_vars['content']->value->pages_type;?>
</h6>          
    </div>
</div>


<div class="ui-widget-content ui-corner-all bk_color3">
    <div class="grid_6 m-10"> <h3>Page Details</h3>  </div>
    <div class="grid_6 m-10">   <h4>    pk: <?php echo $_smarty_tpl->tpl_vars['p']->value->pages_pk;?>
 &nbsp; id: <?php echo $_smarty_tpl->tpl_vars['p']->value->pages_id;?>
  &nbsp; version: <?php echo $_smarty_tpl->tpl_vars['p']->value->pages_version;?>
</h4></div>
             
    <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
/pages">
    <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
    CANCEL</a></h6>

    <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="savePage();">
    <span class="ui-icon ui-icon-disk float-l mr-5"></span>
    SAVE</span></h6>
    
         
    <br clear="all">
</div>





<div class="ui-widget-content ui-corner-all bk_color2" >

    <form id= "id_details_form" method="POST" >
    <fieldset>
        <input type="hidden" name="pages_pk" value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_pk;?>
" />
        <input type="hidden" name="pages_id" value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_id;?>
" />
        <input type="hidden" name="pages_version" value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_version;?>
" />
        <input type="hidden" name="pages_site_code" value="<?php echo $_smarty_tpl->tpl_vars['site_code']->value;?>
" />
        <input type="hidden" name="pages_version_comment" value=""  id="id_comment"/>
        <input type="hidden" name="json_module_data" value=""  id="id_module_data" />
        <h2><?php echo $_smarty_tpl->tpl_vars['site_name']->value;?>
</h2>
       
        <div>
            <label class="grid_12">title:</label> 
            <div class="float-r" style="width: 100px;">
               <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
               <span href="#"class="ui-icon ui-icon-stop float-r"></span>
            </div>     
            <input type="text" name="pages_title" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_title;?>
"> <br>
        </div>
        display Title:<input type="text" name="pages_display_title" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_display_title;?>
"> <br>
        url         <input type="text" name="pages_url" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_url;?>
"> <br>
        type:       <input type="text" name="pages_type" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_type;?>
"> <br>
        password    <input type="text" name="pages_password" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_password;?>
"> <br>
        status:     <input type="text" name="pages_status" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_status;?>
"> <br>
        php class:  <input type="text" name="pages_php_class" class=""  value="<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_php_class;?>
"> <br>
        
        no robots:  <input type="checkbox" name="pages_no_robots" <?php if ($_smarty_tpl->tpl_vars['p']->value->pages_no_robots==1){?> checked="checked"<?php }?>class="" /> <br>
        
        body:      <textarea id="id_body" type="text" name="pages_body" rows="25" class=""><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_body;?>
</textarea>
        <br><hr />
        make live:    <input type="checkbox" name="pages_is_live" class="" />  
        make preview: <input type="checkbox" name="pages_is_preview" class=""/>  
        create new version: <input type="checkbox" name="new_version" checked="checked" class="" /> <br>
         <br>
    </fieldset>
    </form>
  
   
  
</div>
<br>
<br><?php }} ?>