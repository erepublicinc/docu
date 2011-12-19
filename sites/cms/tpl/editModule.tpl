<!-- ===================== editModule.tpl ===============     -->

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

    // Note:   saveModule() is slightkly differnt than saveContent()
    function saveModule()
    {        
        var $dialog = $('#save_dialog').dialog({title: 'Save', modal:true
            ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                       {text: "Save", click: function() { saveContentPart2(); }  }
                      ]
        });   
    }
    
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
        <div class="grid_6 m-10"> <h3>Module Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/{$site_code}/{$record_type}">
        <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
        CANCEL</a></h6>

        <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="saveModule();">
        <span class="ui-icon ui-icon-disk float-l mr-5"></span>
        SAVE</span></h6>
{*      
        <div class="m-5 ml-20 float-r">
            Save to: 
            <select  id="id_status_dropdown" class="select-list-medium" style="width:170px">
                <option {if $content->contents_status == 'DRAFT'} selected='selected'{/if}> DRAFT </option>
                <option {if $content->contents_status == 'REVIEW'} selected='selected'{/if}> REVIEW  </option>
                <option {if $content->contents_status == 'READY'} selected='selected'{/if}> READY </option>
            </select> 
        </div>
        <br clear="all">
*}        
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
            <input type="hidden" name='contents_id' value="{$content->contents_id}" />
            <input type="hidden" name='contents_latest_version' value="{$content->contents_latest_version}" />
            <input type="hidden" id="id_comment" name='contents_version_comment' value="{$content->contents_version_comment}" />
            <input type="hidden" id="id_status" name='contents_version_status' value="{$content->contents_version_status}" />        
            <fieldset>                      

                <div>
                    <label class="grid_12">Title:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>
                    <input type="text" name="contents_title" class="required" 
                     value="{$content->contents_title}">
                </div>

                <div>
                    <label class="grid_12">Display Title:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="contents_display_title" class="required"  value="{$content->contents_display_title}"/>
                </div>
                                
                <div>
                    <label class="grid_12">Author:</label>
                    <select  name="contents_authors_id" class="required">
                                 <option value="{$content->contents_authors_id}" >{$content->users_first_name} {$content->users_last_name}</option>
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
                    <textarea type="text" name="contents_summary" >{$content->contents_summary}</textarea>
                </div> 
                
                <div>
                    <label class="grid_12">php class:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="modules_php_class" class="required"   value="{$content->modules_php_class}"/>
                </div>              
                
                 <div>
                    <label class="grid_12">json params:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="modules_json_params" class=""  value="{$content->modules_json_params}"/>
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
                    <textarea id="id_body" type="text" name="modules_body" rows="25" class="">{$content->modules_body}</textarea>
                </div>   
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     
    
   



<!-- end of CONTENT -->
 
  
  
  
  
  
  
  


