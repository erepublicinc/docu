<!-- ===================== editArticle.tpl ===============     -->

{include file="saveContentDialog.tpl"}


{literal}
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
{/literal}


    

<!-- CONTENT -->
  <!-- PAGE TITLE -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">  
        <h2>{$content->contents_title}</h2>
        <h6>{$content->contents_type}</h6>          
        </div>
    </div>
    <!-- / PAGE TITLE -->  

    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>Article Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/{$site_code}/{$record_type}">
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
    
            <input type="hidden"id="id_changed_targets" name='changed_targets' value="" />                                              
            <input type="hidden" name='contents_id' value="{$content->contents_id}" />
            <input type="hidden" name='contents_latest_rev' value="{$content->contents_latest_rev}" />
            <input type="hidden" id="id_comment" name='contents_rev_comment' value="{$content->contents_rev_comment}" />
            <input type="hidden" id="id_status" name='contents_rev_status' value="{$content->contents_rev_status}" />     
            <input type="hidden" id="id_make_preview" name='make_preview'  />  
            <input type="hidden" id="id_make_live" name='make_live'  />   
               
            <fieldset>                      

          
                {foreach $form_data as $field_name => $field}
                {if $field.label}
                    
                    <div>
                        <label class="grid_12">{$field.label}</label>                   
                        <div class="float-r" style="width: 100px;">
                            <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                            <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                        </div>     
                        <br clear="all">                                              
                         
                        {if $field.type == 'text'}                         
                            <textarea id="id_body" type="text" name="{$field_name}" rows="25" class="required">{$value.$field_name}</textarea>
                        {elseif $field.type == 'bit'}
                            <input name="{$field_name}" type="checkbox" {if $value.field_name == 1} selected="selected"{/if} />
                        {elseif $field.form_element == 'select'}                        
                            <select  name="{$field_name}" class="required">
                               {foreach $field.options as $option}
                                     <option value="{$option->id}" {if $option->id == $value.$field_name} selected="selected"{/if} >{$option->title}</option>                            
                               {/foreach}
                            </select>                            
                        {elseif $field.type == 'varchar'}
                            <input type="text" name="{$field_name}" class="required" value="{$value.$field_name}">    
                        {elseif $field.type == 'datetime'}
                            <input type="text" name="{$field_name}" class="required" value="{$value.$field_name|date_format:$DATETIME_FORMAT}">                                                             
                        {/if}
                            
                    </div>    
                
                {/if}
                
                {/foreach}
               
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     
    
   



<!-- end of CONTENT -->
 
  
  
  
  
  
  
  

