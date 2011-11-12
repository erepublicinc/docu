
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

function saveArticle()
{
	$('#id_changed_targets').attr('value', gatherChangedTargets()); 
	$('#id_status').attr('value', $('#id_status_dropdown').attr('value') );
    
    var $dialog = $('#save_dialog').dialog({title: 'Save', modal:true
        ,buttons: [{text: "Cancel", click: function() { $(this).dialog("close"); }  },
                   {text: "Save", click: function() { saveArticlePart2(); }  }
                  ]
    });
    
	

}

function saveArticlePart2()
{
    var comment = $('#id_enter_comment').attr('value') 
	$('#id_comment').attr('value', comment );

    //alert('saving: '+comment);
	$('#id_details_form').submit();
}
    
</script>

{/literal}

<div id='save_dialog' class="ui-dialog ui-widget ui-widget-content ui-corner-all"  style="display:none; background-color:white">
    <textarea id="id_enter_comment">Please enter a comment</textarea>
</div>




<!-- CONTENT -->
  <!-- PAGE TITLE -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">  
        <h2>{$content->contents_title}</h2>
        <h6>{$content->contents_type}</h6>          
        </div>
    </div>
    <!-- / PAGE TITLE -->  
    
{*           
    <div class="ui-widget-content ui-corner-all p-10">
        <div>
            <form>
                <div class="grid_7">
                    Domains:
                    <select name="domainID" id="domainID" class="domain-select select-list-medium" style="width:200px">
                        <option class="" value="8157"> www.centerdigitaled.com </option>
                        <option class="" value="8167"> www.centerdigitalgov.com </option>
                        <option class="" value="7319"> www.convergemag.com</option>
                        <option class="" value="11949"> www.digitalcommunities.com </option>
                        <option class="" value="8717"> www.emergencymgmt.com</option>
                        <option class="" value="8117"> www.erepublic.com </option>
                        <option class="" value="11302"> www.governing.com</option>
                        <option class="" value="8152"> www.govtech.com </option>
                    </select> 
                </div>
                
                <div class="grid_7">
                    Destination:    
                    <select name="previewDestID" id="previewDestID" class="select-list-medium" style="width:170px">
                        <option class="" value="563137">&nbsp;&nbsp;clk.navigatored.com</option>
                    </select>   
                </div>
                <a class="ui-state-default ui-corner-all float-r pb-5 pl-10 pr-10 pt-5" href="#">
                    <span class="ui-icon ui-icon-zoomin float-l mr-5"></span>
                        Preview</a>
                <br clear="all">
            </form>
        </div>
    </div>      
*}


    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>Article Details</h3>  </div>
                      
        <h6><a class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" href="/cms/{$site_code}/articles">
        <span class="ui-icon ui-icon-cancel float-l mr-5"></span>
        CANCEL</a></h6>

        <h6><span class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5"   onclick="saveArticle();">
        <span class="ui-icon ui-icon-disk float-l mr-5"></span>
        SAVE</span></h6>
      
        <div class="m-5 ml-20 float-r">
            Save to: 
            <select  id="id_status_dropdown" class="select-list-medium" style="width:170px">
                <option {if $content->contents_status == 'LIVE'} selected='selected'{/if}> DRAFT </option>
                <option {if $content->contents_status == 'LIVE'} selected='selected'{/if}> PREVIEW  </option>
                <option {if $content->contents_status == 'LIVE'} selected='selected'{/if}> LIVE </option>
            </select> 
        </div>
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
            <input type="hidden" name='contents_pk' value="{$content->contents_pk}" />
            <input type="hidden" name='contents_latest_version' value="{$content->contents_latest_version}" />
            <input type="hidden" id="id_comment" name='contents_version_comment' value="{$content->contents_version_comment}" />
            <input type="hidden" name='contents_status' value="{$content->contents_status}" />        
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
                    <input type="text" name="contents_display_title" class="required" 
                    value="{$content->contents_display_title}">
                </div>
                                
                <div>
                    <label class="grid_12">Author:</label>
                    <select  name="contents_main_authors_fk" class="required">
                                 <option value="{$content->contents_main_authors_fk}" >{$content->users_first_name} {$content->users_last_name}</option>
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
                    value="{$content->contents_url_name}">
                </div>                                                                                             
                <div>
                    <label class="grid_12">Published Date:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="contents_create_date" class="required" 
                    value="{$content->contents_create_date|date_format:$DATETIME_FORMAT}">                                       
                </div>
                <div>
                    <label class="grid_12">Abstract:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                        <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <textarea type="text" name="contents_summary" >{$content->contents_summary}</textarea>
                </div> 
                <div>
                    <label class="grid_12">Body:</label>                   
                    <div class="float-r" style="width: 100px;">
                      <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                      <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>     
                     <br clear="all">                        
                    <textarea id="id_body" type="text" name="contents_article_body" rows="25" class="required">{$content->contents_article_body}</textarea>
                </div>   
                                        
            </fieldset>              
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     
    
    {include file="targetsModule.tpl"}



<!-- end of CONTENT -->
 
  
  
  
  
  
  
  

