<?php /* Smarty version Smarty 3.1.0, created on 2011-12-05 16:43:09
         compiled from "/var/www/newgt/sites/cms/tpl/saveContentDialog.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9773572104edd651d20cfd3-54716739%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8417f670ce61387b12a2e707cf60497834e325dd' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/saveContentDialog.tpl',
      1 => 1322769925,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9773572104edd651d20cfd3-54716739',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4edd651d261ae',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4edd651d261ae')) {function content_4edd651d261ae($_smarty_tpl) {?>
<!-- ============== saveContentDialog.tpl ================== -->

<script type="text/javascript">
function saveContent()
{
	$('#id_changed_targets').attr('value', gatherChangedTargets()); 
	
    
    var $dialog = $('#save_dialog').dialog({title: 'Save', modal:true
        ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                   {text: "Save", click: function() { saveContentPart2(); }  }
                  ]
    });
    
}

function saveContentPart2()
{
    var comment = $('#id_enter_comment').attr('value') 
	$('#id_comment').attr('value', comment );   //comment

    $('#id_status').attr('value', $('#id_status_dropdown').attr('value') ); // status
    if($('#id_dlg_make_live').attr('checked')){    //makelive
    		 $('#id_make_live').attr('value',1);
    		 $('#id_status').attr('value','READY');
    }
    if($('#id_dlg_make_preview').attr('checked'))  //makePreview
   		 $('#id_make_preview').attr('value',1);

    //alert($('#id_make_preview').attr('value') );
	$('#id_details_form').submit();
}
    
</script>


<div id='save_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="width:350px;display:none; background-color:white">
    <textarea id="id_enter_comment">Please enter a comment</textarea> 
    <br>
    <label><input type="checkbox" id="id_dlg_make_live" >Make Live</label>&nbsp;
    <label><input type="checkbox" id="id_dlg_make_preview" >Make Preview</label>&nbsp;
    <select  id="id_status_dropdown" class="select-list-small" style="width:80px">
                <option <?php if ($_smarty_tpl->tpl_vars['content']->value->contents_status=='DRAFT'){?> selected='selected'<?php }?>> DRAFT </option>
                <option <?php if ($_smarty_tpl->tpl_vars['content']->value->contents_status=='REVIEW'){?> selected='selected'<?php }?>> REVIEW  </option>
                <option <?php if ($_smarty_tpl->tpl_vars['content']->value->contents_status=='READY'){?> selected='selected'<?php }?>> READY </option>
            </select> 
</div>

<!-- ============== end of: saveContentDialog.tpl ================== --><?php }} ?>