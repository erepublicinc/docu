{literal}
<script type="text/javascript">

    $(document).ready(function(){
         $(".slidingDiv").hide();
         $(".show_hide").show();
         $('.show_hide').click(function(){
         $(".slidingDiv").slideToggle();
         });
         CKEDITOR.replace( 'id_body',{toolbar :
            [
            ['Source', '-','Undo','Redo','PasteFromWord'],
            ['Find','Replace','-','SelectAll','RemoveFormat'],
            ['Link', 'Unlink', 'Image'],           
            ['Bold', 'Italic','Underline','TextColor','Blockquote', 'SpecialChar','NumberedList','BulletedList']
        ]});
    });
</script>

{/literal}

<!-- CONTENT -->
<div class="container_24">


  
  <div class="grid_18">
  
  <!-- PAGE TITLE -->
    <div class="boxstyle2">
        <div class="m-10">  
        <h3>Sacramento, Calif., Police Training Includes Sessions with Local Businesses </h3>
        <h6>Common Article</h6>          
        </div>
    </div>
    
      <div class="boxstyle2">
    <div style="margin: 15px 5px;">
             <form >
                <div class="grid_7">
                    Domains:
                        <select name="domainID" id="domainID" class="domain-select select-list-medium" style="width:200px">
                                        <option class="" value="10492" selected="true">
                    clk.navigatored.com
                </option>
                            <option class="" value="14408">
                    clk.navigatorem.com
                </option>
                            <option class="" value="10472">
                    clk.navigatorgov.com
                </option>
                            <option class="" value="16623">
                    m.emergencymgmt.com
                </option>
                            <option class="" value="14239">
                    m.governing.com
                </option>
                            <option class="" value="13664">
                    m.govtech.com
                </option>
                            <option class="" value="8157">
                    www.centerdigitaled.com
                </option>
                            <option class="" value="8167">
                    www.centerdigitalgov.com
                </option>
                            <option class="" value="7319">
                    www.convergemag.com
                </option>
                            <option class="" value="11949">
                    www.digitalcommunities.com
                </option>
                            <option class="" value="8717">
                    www.emergencymgmt.com
                </option>
                            <option class="" value="9139">
                    www.envtestdomain.com
                </option>
                            <option class="" value="8117">
                    www.erepublic.com
                </option>
                            <option class="" value="11302">
                    www.governing.com
                </option>
                            <option class="" value="8152">
                    www.govtech.com
                </option>
                            <option class="" value="8172">
                    z-deactivated-navigatorgov.com
                </option>
            </select> 
                </div>
                <div class="grid_8">
                    Destination:    
                        <select name="previewDestID" id="previewDestID" class="select-list-medium" style="width:200px">
                                <option class="" value="563137">
                    &nbsp;&nbsp;clk.navigatored.com
                </option>
                            <option class="" value="563532">
                    &nbsp;&nbsp;&nbsp;&nbsp;Blogs
                </option>
                            <option class="" value="563537">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Economic Stimulus
                </option>
                            <option class="" value="563547">
                    &nbsp;&nbsp;&nbsp;&nbsp;CIO Interviews
                </option>
                            <option class="" value="563552">
                    &nbsp;&nbsp;&nbsp;&nbsp;News
                </option>
            </select>   
                </div>
                <img src="/images/btn_preview.png" style="margin-top: 3px;">
                <br clear="all">
            </form>
        </div>
    </div> 
  </div>    
  <!-- / PAGE TITLE -->
   <!-- Upper RIGHT COLUMN -->
  <div class="grid_6">
  
        <div class="border boxstyle2">
            <div class="m-10">
                <h3>ID: 123123</h3>
                <h6>Status: Ready</h6>
            </div>                                                           
        </div> 
        <div class="border boxstyle2">
            <div class="m-10">
                Created: 10/10/11 - 08:48AM<br>
                Modified: 10/11/11 - 04:30PM
            </div>                                                           
        </div>         
  </div>

  

 
<!-- /  Upper RIGHT COLUMN -->  

   
   

   
  
<!-- CENTER COLUMN -->

<div class="grid_24">
    <div class="boxstyle1">
                <div class="formactions">            
                    <img src="/images/btn_save.png"  onclick="$('#id_details_form').submit();">
                    <img src="/images/btn_cancel.png">
                </div>  
     </div>    
</div>




  
  <div class="grid_18">        
    <div class="boxstyle1">
                <h5>Details</h5>
    
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
                
                <input type="hidden" name='pk' value="{$article->pk}" />
                
                               
                <fieldset>                      
                        <div>
                            <label class="error">Title:
                            <span class="ml-10">This line explains where this is displayed.)
                            </span></label>
                            <input type="text" name="title" class="required" 
                            value="{$article->title}">
                        </div>
                        <div>
                            <label>Display Title:
                            <span class="ml-10">This line explains where this is displayed.
                            </span></label>
                            <input type="text" name="display_title" class="required" 
                            value="{$article->display_title}">
                        </div>          
                        <div>
                            <label>Author:</label>
                            <select  name="author" class="required">
                                 <option value="{$article->main_author_fk}" >{$article->first_name} {$article->last_name}</option>
                            </select> 
                        </div>
                       
                        <div>
                            <label>URL Resource Name: <a href="#">Edit</a><br>
                            <span>Do not include the '.html' suffix, it will be appended to the URL automatically.
                            </span></label>
                            <input type="text" name="url_name" class="restricted" 
                            value="{$article->url_name}">
                        </div>                                                                                             
                        <div>
                            <label>Published Date:</label>
                            <input type="text" name="create_date" class="required" 
                            value="{$article->create_date}">                                        
                        </div>
           
                        <div>
                            <label>Abstract:</label>
                            <textarea type="text" name="summary" >{$article->summary}
                            </textarea>
                        </div> 
                        <div >
                            <label>Body:</label>
                            <textarea id="id_body" type="text" name="body" rows="25" class="required"> {$article->body}</textarea>
                        </div>   
                                              
                </fieldset>              
            </form> 
            <!-- / Main Form -->
            
    </div> 
    <!-- Box Style1 -->
    

    
    
    
  </div> 
  
  <div class="grid_6">

  
  
    <div class="boxstyle1">
    <h5>Media</h5>
        <div class="float-r headlink">
        
            <a href="media_form.html#TB_inline?height=300&width=930&inlineId=myOnPageContent" class="thickbox">
                <img src="/images/btn_edit.png">
            </a>
            
            <div id="myOnPageContent" style="display:none;">
            <div class="container_24">
            <div class="grid_24">
                <div class="boxstyle2">
                <div class="m-10">
                <h3>Edit Media Items</h3>
                </div>
                </div>                
            </div>            

            <!-- IMAGE BOX -->
            <div class="grid_8">
                <div class="boxstyle1">
                    <h5>Main Image</h5>
                <div class="m-10">
                    <h6>Cars Stuck in Traffic in Los Angeles</h6>
                       Credit: Joe Photographer / e.Republic Inc.

                    <img src="/images/GT_traffic_jam_flickr1.jpg" alt="Cars Stuck in Traffic in Los Angeles" 
                        class="mr-10 mt-10 float-l border">
                    
                    <!-- Thumb Details -->
                        <div class="float-l" style="width: 118px;">
                            <p class="align-l">
                            169 x 144<br>
                            20 KB<br>
                            Created: 10/17/2011<br>
                            </p>               
                            <a href="#" class="show_hide">Edit Details</a><br>
                            <a href="#" class="show_hide">Replace Image</a><br>
                            <a href="#" class="show_hide">Delete</a>
                            <br clear="all"> 
                        </div>
                    <!-- / Thumb Details -->
                    <br clear="all">
                    <!-- Sliding Div -->
                    <div class="slidingDiv">                    
                            <!-- FORM -->
                                <form>          
                
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
        
                                        <div>
                                            <label>Credit:</label>
                                            <input type="text" name="" class="required" 
                                            value="Joe Photographer / e.Republic Inc.">
                                        </div>                                      
                                        <div>
                                            <label class="error">Title:</label>
                                            <input type="text" name="" class="required" 
                                            value="Cars Stuck in Traffic in Los Angeles">
                                        </div>                      
                                        <div>
                                            <label>Alt Text:</label>
                                            <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">
                                        </div>
                                        <div>
                                            <label>Caption:</label>
                                            <textarea type="text" name="" >Cars Stuck in Traffic in Los Angeles is a big problem for commuters!</textarea>                                        
                                        </div> 
                                        <div>
                                            <label>Short Caption:</label>    
                                                <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">                       
                                        </div>  
                            </form> 
                            <!-- / FORM -->
    
                        <br clear="all"><br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
   
                    </div>
                    <!-- / Sliding Div -->
                </div>
                </div>
            </div>
            <!-- / IMAGE BOX -->
            <!-- IMAGE BOX -->
            <div class="grid_8">
                <div class="boxstyle1">
                    <h5>Feature Image</h5>
                <div class="m-10">
                    <h6>Cars Stuck in Traffic in Los Angeles</h6>
                       Credit: Joe Photographer / e.Republic Inc.

                    <img src="/images/GT_traffic_jam_flickr1.jpg" alt="Cars Stuck in Traffic in Los Angeles" class="mr-10 mt-10 float-l border">
                    
                    <!-- Thumb Details -->
                    <div class="float-l" style="width: 118px;">
                            <p class="align-l">
                            169 x 144<br>
                            20 KB<br>
                            Created: 10/17/2011<br>
                            </p>               
                            <a href="#" class="show_hide">Edit Details</a><br>
                            <a href="#" class="show_hide">Replace Image</a><br>
                            <a href="#" class="show_hide">Delete</a>
                            <br clear="all"> 
                        </div>
                    <!-- / Thumb Details -->
                    <br clear="all">
                    <!-- Sliding Div -->
                    <div class="slidingDiv">                    
                            <!-- FORM -->
                                <form>          
                
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
        
                                        <div>
                                            <label>Credit:</label>
                                            <input type="text" name="" class="required" 
                                            value="Joe Photographer / e.Republic Inc.">
                                        </div>                                      
                                        <div>
                                            <label class="error">Title:</label>
                                            <input type="text" name="" class="required" 
                                            value="Cars Stuck in Traffic in Los Angeles">
                                        </div>                      
                                        <div>
                                            <label>Alt Text:</label>
                                            <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">
                                        </div>
                                        <div>
                                            <label>Caption:</label>
                                            <textarea type="text" name="" >Cars Stuck in Traffic in Los Angeles is a big problem for commuters!</textarea>                                        
                                        </div> 
                                        <div>
                                            <label>Short Caption:</label>    
                                                <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">                       
                                        </div>  
                            </form> 
                            <!-- / FORM -->
    
                        <br clear="all">   
                    </div>
                    <!-- / Sliding Div -->
                </div>
                </div>
            </div>                
            <!-- / IMAGE BOX -->            
            <!-- IMAGE BOX -->
            <div class="grid_8">            
                <div class="boxstyle1">
                    <h5>Extra Image</h5>
    
    
                    
                    <div class="m-10">
                        <h6>Cars Stuck in Traffic in Los Angeles</h6>
                           Credit: Joe Photographer / e.Republic Inc.
    
                        <img src="/images/GT_traffic_jam_flickr1.jpg" alt="Cars Stuck in Traffic in Los Angeles" class="mr-10 mt-10 float-l border">
                        
                        <!-- Thumb Details -->
                        <div class="float-l" style="width: 118px;">
                                <p class="align-l">
                                169 x 144<br>
                                20 KB<br>
                                Created: 10/17/2011<br>
                                </p>               
                                <a href="#" class="show_hide">Edit Details</a><br>
                                <a href="#" class="show_hide">Replace Image</a><br>
                                <a href="#" class="show_hide">Delete</a>
                                <br clear="all"> 
                            </div>
                        <!-- / Thumb Details -->
                        <br clear="all">
                        <!-- Sliding Div -->
                        <div class="slidingDiv">                    
                                <!-- FORM -->
                                    <form>          
                    
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
            
                                            <div>
                                                <label>Credit:</label>
                                                <input type="text" name="" class="required" 
                                                value="Joe Photographer / e.Republic Inc.">
                                            </div>                                      
                                            <div>
                                                <label class="error">Title:</label>
                                                <input type="text" name="" class="required" 
                                                value="Cars Stuck in Traffic in Los Angeles">
                                            </div>                      
                                            <div>
                                                <label>Alt Text:</label>
                                                <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">
                                            </div>
                                            <div>
                                                <label>Caption:</label>
                                                <textarea type="text" name="" >Cars Stuck in Traffic in Los Angeles is a big problem for commuters!</textarea>                                        
                                            </div> 
                                            <div>
                                                <label>Short Caption:</label>    
                                                    <input type="text" name="" value="Cars Stuck in Traffic in Los Angeles">                       
                                            </div>  
                                </form> 
                                <!-- / FORM -->
        
                            <br clear="all">   
                        </div>
                        <!-- / Sliding Div -->
                    </div>
                    </div>
                </div>
            <!-- / IMAGE BOX -->

            

                <br clear="all">
                <div class="m-10">
                <div class="formactions">            
                    <img src="/images/btn_save.png">
                    <img src="/images/btn_cancel.png">
                </div>
                </div>
                 
                </div>
            
            
            </div>
        
        
        </div>    
    <div class="m-10 thumblist">
         <ul>
            <li><img src="/images/GT_traffic_jam_flickr1.jpg" width="90">
            <br>Main</li>
            <li><img src="/images/GT_traffic_jam_flickr1.jpg" width="90">
            <br>Featured</li>
            <li><img src="/images/GT_traffic_jam_flickr1.jpg" width="90">
            <br>Extra</li>
         </ul>
         
         
         
         
         
        <br clear="all">
         
    </div>
    </div>
    <div class="boxstyle1">
    <h5>Related Items</h5>
    <div class="align-r headlink"><img src="/images/btn_edit.png"></div>
    <div class="m-10">
        <ul class="condenced">
            <li>Breaking the Cycle of Preparing for the Last Disaster 
                <ul><li>ID: 123123</li>
                <li>Common Article</li></ul>
            </li>
        </ul>
        <ul class="condenced">
            <li>Breaking the Cycle of Preparing for the Last Disaster 
                <ul><li>ID: 123123</li>
                <li>Common Article</li></ul>
            </li>
        </ul>        




    </div>
    </div>  
            <!-- Version History -->       
        <div class="border boxstyle2">
        <h5>Version History</h5>
        <table style="background:#eeeeee;">
                <tr>
                    <td colspan="2" class="align-r" width="60%">Version: </td> 
                    <td>3</td>                 
                </tr>
                <tr>
                    <td colspan="2" class="align-r" width="40%">Live Version: </td>    
                    <td>3</td>            
                </tr>  
        </table>         
        <div class="m-10">
            <table class="condenced">
                <tr>
                    <td><input type="checkbox" name="Name"></td>
                    <td>3</td>
                    <td><a href="#">10/10/11 - 08:57AM</a></td>
                </tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Action: Approved</td></tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Status: Ready</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Assigned:<br>
 eRepubic - Editors</td></tr>
                <tr style="padding-bottom:15px;">
                    <td><input type="checkbox" name="Name"></td>
                    <td>2</td>
                    <td><a href="#">10/10/11 - 08:57AM</a></td>
                </tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Action: Approved</td></tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Status: Ready</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Assigned:<br>
Elain Pittman</td></tr>
                <tr style="padding-bottom:15px;">
                    <td><input type="checkbox" name="Name"></td>
                    <td>1</td>
                    <td><a href="#">10/10/11 - 08:57AM</a></td>
                </tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Action: Approved</td></tr>
                <tr><td colspan="3" style="padding:0 0 0 40px;">Status: Ready</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Assigned:<br>
Elaine Pittman</td></tr>                            


                
            </table>
                Compare | Make Current

        </div>
           
        
        </div> 
                <!-- / Version History --> 
                
        <!-- Author History -->       
        <div class="border boxstyle2">
        <h5>Author Assoc. History</h5>  
        <div class="m-10">
            <p>No history has been logged.</p>
        </div>
           
        
        </div> 
                <!-- / Author History --> 
    
      
  </div> 
   

  <div class="clear"></div>
  
  <div class="grid_24">
  
    <!-- Targets -->
        <div class="border boxstyle1">
            <h5>Targets</h5>
            <div class="float-r headlink"><img src="/images/btn_edit.png"></div>
            
            <table>
                <tr>
                    <th>Destination</th>                    
                    <th>Live Date</th>
                    <th>Archive date</th>
                    <th>Dead Date</th>
                    <th class="align-c">Placement</th>                    
                </tr>
                <tr>
                    <td>emergencymgmt.com/emergency-blogs/incident-management</td>                    
                    <td>10/13/11 - 12:00AM</td>
                    <td>Never</td>
                    <td>Never</td>
                    <td class="align-c">1</td>
                </tr>   
                <tr>
                    <td>emergencymgmt.com</td>                    
                    <td>10/13/11 - 12:00AM</td>
                    <td>Never</td>
                    <td>Never</td>
                    <td class="align-c">1</td>                    
                </tr>    
                <tr>
                    <td>clk.navigatorem.com/news</td>                    
                    <td>10/13/11 - 12:00AM</td>
                    <td>Never</td>
                    <td>Never</td>
                    <td class="align-c">1</td>                    
                </tr>                                                                
            
            </table>
        </div>  
        <!-- / Targets -->   
  </div>
  
  
  
    <div class="grid_24">
        <div class="boxstyle1">
                <div class="formactions">            
                    <img src="/images/btn_save.png">
                    <img src="/images/btn_cancel.png">
                </div> 
        </div>
    </div>
    
    <br>
<br>
<br>
<br>

  
  
</div>
  <!-- /CONTENT -->
  
 
  
  
  
  
  
  
  

