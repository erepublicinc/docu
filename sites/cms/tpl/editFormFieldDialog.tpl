
<!-- ============== editFormFieldDialog.tpl ================== -->
{literal}
<script type="text/javascript">
function editFormFieldConfig(field_id)
{
	// populate the dialog 
    
   
        
    var $dialog = $('#field_config_dialog').dialog({title: 'Configure Field', modal:true
        ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                   {text: "Save", click: function() { saveFieldConfig(); }  }
                  ]
    });
    
}

function saveFieldConfig()
{
    

    
    var comment = $('#id_enter_comment').attr('value') 
	//$('#id_comment').attr('value', comment );   //comment
    $('input[name=contents_rev_comment]').attr('value', comment );   //comment
    
    //$('#id_status').attr('value', $('#id_status_dropdown').attr('value') ); // status
    $('input[name=contents_rev_status]').attr('value', $('#id_status_dropdown').attr('value') ); // status
    
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

<div id='field_config_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="width:350px;display:none; background-color:white">
    <textarea id="id_xml">XML</textarea> 
    <br>
    <label><input type="checkbox" id="id_https" />https</label>&nbsp;
    <label>validation</label>
    <select  id="id_fields_type" class="select-list-small" style="width:80px">
                <option>select</option>
                <option>text</option>
                <option>text</option>  
    </select>            
    <label>validation</label>
    <select  id="id_fields_validation" class="select-list-small" style="width:80px">
                <option value="">[none]</option>
                <option>number</option>
                <option>alphanumeric</option>
                <option>phone</option>
                <option>email</option>                          
     </select> 
</div>

<!-- ============== end of: saveContentDialog.tpl ================== -->