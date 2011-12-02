
<!-- ============== saveContentDialog.tpl ================== -->
{literal}
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
{/literal}

<div id='save_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="width:350px;display:none; background-color:white">
    <textarea id="id_enter_comment">Please enter a comment</textarea> 
    <br>
    <label><input type="checkbox" id="id_dlg_make_live" >Make Live</label>&nbsp;
    <label><input type="checkbox" id="id_dlg_make_preview" >Make Preview</label>&nbsp;
    <select  id="id_status_dropdown" class="select-list-small" style="width:80px">
                <option {if $content->contents_status == 'DRAFT'} selected='selected'{/if}> DRAFT </option>
                <option {if $content->contents_status == 'REVIEW'} selected='selected'{/if}> REVIEW  </option>
                <option {if $content->contents_status == 'READY'} selected='selected'{/if}> READY </option>
            </select> 
</div>

<!-- ============== end of: saveContentDialog.tpl ================== -->