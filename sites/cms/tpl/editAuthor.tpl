
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
         CKEDITOR.replace( '.html_field',ckConfig);        
         
    });



function saveContent()
{
	$('#id_details_form').submit();
}
    
</script>

{/literal}



<!-- CONTENT -->
  <!-- PAGE TITLE -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">  
            <h2>{$page_title}</h2>              
        </div>
    </div>
    <!-- / PAGE TITLE -->  
    



    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>{$page_title}</h3>  </div>
                      
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
                                              
            <input type="hidden" name='authors_pk' value="{$author->authors_pk}" />
            <fieldset>                      
                <div>
                    <label class="grid_2">Active:</label>
                      <input type="checkbox" name="authors_active"  {if $author->authors_active ==1}checked="checked" {/if} class=""/> 
                </div>
                
                <div>
                    <label class="grid_12">Name:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                   
                    </div>
                    <input type="text" name="authors_name"  value="{$author->authors_name}">
                </div>
                
                <div>
                    <label class="grid_12">Display Name:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                   
                    </div>
                    <input type="text" name="authors_display_name"  value="{$author->authors_display_name}">
                </div>

               
                                
                <div>
                    <label class="grid_12">public Email:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="authors_public_email" class="required"  value="{$author->authors_public_email}"/>
                </div>
               
                                                                                                             
                <div>
                    <label class="grid_12">Bio:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                       
                    </div>                            
                    <textarea class=".html_field" type="text" name="authors_bio" >{$author->authors_bio}</textarea>
                </div> 
                
               <div>
                    <label class="grid_12">User owning this Author profile:</label>
                    <select  name="authors_users_fk" class="required">
                       {foreach $users as $user}
                                 <option value="{$user->users_pk}" {if $user->users_pk == $author->authors_users_fk} selected="selected"{/if} >{$user->users_first_name} {$user->users_last_name}</option>
                       {/foreach}
                    </select> 
                </div>
                 
                 
                 
               <br clear="all">  
               
                 
                                         
            </fieldset>              
     
     
     
     
     
     
 
     
     
     
     
     
     
     
     
     
     
     
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     

<!-- end of CONTENT -->
 
  
  
  
  
  
  
  


