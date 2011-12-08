
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



function saveContent()
{
 /*
    var comment = $('#id_enter_comment').attr('value') 
	$('#id_comment').attr('value', comment );

    var roles_str = '';
    $('.role_checkbox').each(function(){
        if( $(this).checked())
            roles_str .= $(this).attr('name')
    });
*/
    
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
            <h2>{$user->users_first_name} {$user->users_last_name}</h2>              
        </div>
    </div>
    <!-- / PAGE TITLE -->  
    



    <div class="ui-widget-content ui-corner-all bk_color3">
        <div class="grid_6 m-10"> <h3>User Details</h3>  </div>
                      
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
            <input type="hidden" id="id_changed_roles" name='changed_roles' value="" />                                              
            <input type="hidden" name='users_pk' value="{$user->users_pk}" />
            <fieldset>                      
                <div>
                    <label class="grid_2">Active:</label>
                      <input type="checkbox" name="users_active"  {if $user->users_active ==1}checked="checked" {/if} class=""/> 
                </div>
                
                <div>
                    <label class="grid_12">First Name:</label>                        
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                   
                    </div>
                    <input type="text" name="users_first_name" 
                     value="{$user->users_first_name}">
                </div>

                <div>
                    <label class="grid_12">Last Name:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_last_name" class="required"  value="{$user->users_last_name}"/>
                </div>
                                
                <div>
                    <label class="grid_12">Email:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_email" class="required"  value="{$user->users_email}"/>
                </div>
               
                <div>
                    <i>setting this field will reset the password</i>
                    <label class="grid_12">Password:</label>
                    <div class="float-r" style="width: 100px;">
                    <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>                               
                    <span href="#"class="ui-icon ui-icon-stop float-r"></span>
                    </div>                            
                    <input type="text" name="users_password" value=""/>
                </div>
                                                                                               

                <div>
                    <label class="grid_12">Notes:</label>
                    <div class="float-r" style="width: 100px;">                   
                        <a href="#" class="ui-icon-tan ui-icon-info float-r"></a>
                       
                    </div>                            
                    <textarea type="text" name="users_notes" >{$user->users_notes}</textarea>
                </div> 
  
               
  
                <div >
                    <table class="grid_6">
                        {foreach $roles as $role => $checked}
                           <tr><td>  {$role}: </td><td><input type="checkbox" name="user_role_{$role}" 
                                {if $checked == 1} checked="checked"{/if} />  
                           </td></tr>
                        {/foreach}
                    </table>
                </div>
                 
                 
               <br clear="all">  
               
                 
                                         
            </fieldset>              
     
     
     
     
     
     
 
     
     
     
     
     
     
     
     
     
     
     
     
           </form> 
        <!-- / Main Form -->   
    </div> 
    <!-- Box Style1 -->
     

<!-- end of CONTENT -->
 
  
  
  
  
  
  
  


