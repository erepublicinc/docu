<!-- ===================== editArticle.tpl ===============     -->


{literal}
<script>

function saveForm()
{
	$('#id_changed_fields').attr('value', collectFieldInfo());
	$('#id_details_form').submit();    
}

</script>
{/literal}


    

<!-- CONTENT -->
  <!-- PAGE TITLE -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">  
        <h2>{$form->contents_title}</h2>
                
        </div>
    </div>
    <!-- / PAGE TITLE -->  

    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>Article Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/{$site_code}/{$model_name}">
        <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
        CANCEL</a></h6>

        <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="saveForm();">
        <span class="ui-icon ui-icon-disk float-l mr-5"></span>
        SAVE</span></h6>

        <br clear="all">
    </div>
          
     
    <div class="ui-widget-content ui-corner-all bk_color2">
        <!-- MAIN FORM -->
         <form id="id_details_form"  method="post">

            
            <input type="hidden" id="id_changed_fields" name='changed_fields' value="" />              
            <input type="hidden" id="id_forms_id"       name='forms_id'  value="{$form->forms_id}"/> 
            <input type="hidden" id="id_site_code"      name='forms_site_code' value="{$form->forms_site_code}" />   
               
            <fieldset>                      
                    
                    <div>
                        <label class="grid_12">Title</label>                   
                        <input type="text" name="forms_title" class="required" value="{$form->forms_title}">    
                        
                        <label class="grid_12">Template</label>                   
                        <input type="text" name="forms_tpl" class="required" value="{$form->forms_tpl}">    

                        <label class="grid_12">URL Name</label>                   
                        <input type="text" name="forms_url_name" class="required" value="{$form->forms_url_name}">    

                        <label class="grid_12">HTTPS</label> 
                        <input id ="id_https" type="checkbox" name="forms_https"{if $form->forms_https == 1} selected="selected"{/if} />
                       
                        <label class="grid_12">Start Date</label> 
                        <input type="text" name="forms_start_date" class="required" value="{$form->forms_start_date|date_format:$DATETIME_FORMAT}">                                                             
                        
                        <label class="grid_12">End Date</label>                                              
                        <input type="text" name="forms_end_date" class="required" value="{$form->forms_end_date|date_format:$DATETIME_FORMAT}">                                                             
                         
                        <label class="grid_12">CSS File</label>                   
                        <input type="text" name="forms_css" class="required" value="{$form->forms_css}">    

                        <label class="grid_12">Eloqua Form ID</label>                   
                        <input type="text" name="forms_eloqua_formid" class="required" value="{$form->forms_eloqua_formid}">    

                        <label class="grid_12">PHP Class</label>                   
                        <input type="text" name="forms_php_class" class="required" value="{$form->forms_php_class}">    

                        <label class="grid_12">XML Data</label>
                        <textarea type="text" name="forms_xml_data" rows="10" class="required">{$form->forms_xml_data}</textarea>                                           
                    </div>                        
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->
        

        {literal}
        
        <style>
            h1 { padding: .2em; margin: 0; }
            #field_masters { float:left; width: 150px; margin-right: 2em; }
            #field_masters ul { font-size:12px;  margin: 0; padding: 1em 0 1em 0;}
            .col_list    { float:left; width: 135px; margin-right: 2em; }
            /* style the list to maximize the droppable hitarea */
            .col_list ol { font-size:14px;  margin: 0; padding: 1em 0 1em 0; }
            
            .border {border: solid black 2px}
        </style>
        
        
        <script>
        var dragged_fid = null;   // field_masters_id
        var dragged_id = null;    // id of the LI  used to see if this is new or just a reorder
        var dragged_fn = null;    // form_name
        var fieldData = [];       // the array that holds the field data
        
        {/literal}
            {foreach $form_fields as $field}
                {assign "tmpid" value = 12345 + $field@index }
                fieldData[{$tmpid}] = {ldelim} 
                     {foreach $field as $val}
                           {if ! $val@first},{/if} "{$val@key}":"{$val}"    
                     {/foreach} {rdelim}; 
            {/foreach} 
                 
        {literal}

         
        $(function() {           
           
            $( "#field_masters li" ).draggable({
                appendTo: "body",
                helper: "clone",
                drag: function(event, ui) { dragged_fid =  $(this).attr('fid'); 
                                            dragged_fn =  $(this).attr('fn'); 
                                            dragged_id =  $(this).attr('id'); 
                                          }
                
            });
            
            $( ".col_list ol" ).droppable({
                activeClass: "ui-state-default",
                hoverClass: "ui-state-hover",
                accept: ":not(.ui-sortable-helper)",
                drop: onDrop
            }).sortable({
                items: "li:not(.placeholder)",
                connectWith: "#trash_can",
                sort: function() {
                    // gets added unintentionally by droppable interacting with sortable
                    // using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
                    $( this ).removeClass( "ui-state-default" );
                }
            });
           
            $( "#trash_can" ).sortable();                       
            
        });

        function onDrop( event, ui )
        {
            $(this).find( ".placeholder" ).remove();

            if (typeof dragged_id === "undefined" )
            {
            	// alert('drop');
                // create a random id
                var tmp_id = 'id_'+new Date().getTime();
    
                fieldData[tmp_id] = {'field_masters_id': dragged_fid, 'fields_title': ui.draggable.text(), 'fields_locked':1};  
                    
                if(dragged_fn.indexOf('xtra_field') == 1)
                {
                	fieldData[tmp_id].fields_locked = 0;
                    $( "<li  id= '"+ tmp_id +"'></li>" ).html("<span class='contenteditable' contenteditable='true' >" + ui.draggable.text() + "</span>&nbsp; [" + dragged_fn +"] &nbsp;<button onclick='configExtraField(\""+tmp_id+"\")'>config</button>" ).appendTo( this );
                }
                else if(dragged_fn == 'info')
                {
                	$( "<li  id= '"+ tmp_id +"'></li>" ).html("<span class='contenteditable' contenteditable='true' >edit me</span>" ).appendTo( this );
                }
                else
                {
                	$( "<li  id= '"+ tmp_id +"'></li>" ).html("<span class='contenteditable' contenteditable='true' >" + ui.draggable.text() + "</span>&nbsp; [" + dragged_fn +"] " ).appendTo( this );
                }

                // store the changed field_title when we leave the field
                $(".contenteditable").blur( storeFieldtitle);
            }
            
        }

        function storeFieldtitle(ev)
        { 
            var tmpId =  $(this).parent().attr('id');
            fieldData[tmpId].fields_title = $(this).text();   
        }
        
        function configExtraField(tmp_id)
        {
            alert('config: '+tmp_id)
        }

        function collectFieldInfo()
        {
            var dta = [];
            // we now have to make sure that we read the fields in the proper order
        	$("#id_form_field_list").each(function(index){
                var linkOrder = 0;
                $(this).find('li').each(function(index){
                    var tmp_id = $(this).attr('id');
                    fieldData[tmp_id].fields_order = ++linkOrder;
                    //alert(fieldData[tmp_id].fields_title);                    
                    dta.push(fieldData[tmp_id]);         
                });
        		
            });
           return JSON.stringify(dta); 
        } 
            
        
        
        </script>
        
        {/literal}


         <div  style="margin:30px">
            <p><i> Drag fields from the Field Master list to the Fields list.<br>
                   Right-click into the field description to edit it</i></p>
            <p>&nbsp;</p>   
    
            <div id="field_masters" style="width:200px; float:left;">              
                    <h5 class="ui-widget-header">Available Fields</h5>
                    <div class="ui-widget-content">
                        <ul>
                        {foreach $masters as $m}                 
                           <li fid="{$m->field_masters_id}" fn="{$m->fields_form_name}" >{$m->fields_title}</li>                
                        {/foreach}           
                        </ul>
                    </div>                
            </div>
                       
           
           <div id="id_form_field_list" class="col_list" style="width:300px; float:left;">
                <h5 class="ui-widget-header">Fields</h5>
                <div class="ui-widget-content">
                    <ol>
                        {foreach $form_fields as $m} 
                            {assign "tmpid" value = 12345 + $m@index }                           
                            <li id="{$tmpid}"><span class='contenteditable' contenteditable='true' >{$m->fields_title}</span> [{$m->fields_form_name}]</li>                            
                        {/foreach}
                    </ol>
                </div>
            </div>
           
            <div >
                <img id='trash_can' width="48" src="/images/trash_can.png" style = "float:right;" />
            </div>
          
       <div> <!-- field module -->    
    </div> 
    <!-- Box Style1 -->
     
<!-- end of FORM -->
 
  
  
  
  
  
  
  

