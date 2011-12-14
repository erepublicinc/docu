
{literal}
    <style>
    .date_time{
        width:70px;
    }
    </style>
{/literal}

<script>

    var targets = [];
    {foreach $params->targets as $t}
      targets.push( {ldelim} record_state:'CLEAN', targets_pages_id:{$t->targets_pages_id}, targets_contents_fk:{$t->targets_contents_fk}, title:'{$t->pages_title|escape}',targets_live_date:'{$t->targets_live_date}',targets_archive_date:'{$t->targets_archive_date}',targets_dead_date:'{$t->targets_dead_date}',targets_pin_position: {$t->targets_pin_position} {rdelim}); 
    {/foreach}      

    var maxTargetId = targets.length;
{literal}   

    var curTargetID = -1;  // edit or new  , so saveTarget knows what to do

    function gatherChangedTargets()
    {
        var dirtyTargets = [];
        for(var t in targets)
        {
            var target = targets[t];
            if(target.state != 'CLEAN')
            { 
                dirtyTargets.push(target);
            }
        }
        return JSON.stringify(dirtyTargets);
    }
    
    function editTarget(t)  // clicked on a target copy from the target list to the edit dialog 
    {   
        //alert(t);
        curTargetID = t;
        //$("#id_target_site").text(targets[t].title);
        $("#id_target_title").text(targets[t].title);
        $("#id_target_pid").text(targets[t].targets_pages_id);      
        $("#id_target_live").attr('value', prettyDate(targets[t].targets_live_date));
        $("#id_target_live_time").attr('value', prettyTime(targets[t].targets_live_date));       
        $("#id_target_archive").attr('value', prettyDate(targets[t].targets_archive_date));
        $("#id_target_archive_time").attr('value', prettyTime(targets[t].targets_archive_date));       
        $("#id_target_dead").attr('value', prettyDate(targets[t].targets_dead_date));
        $("#id_target_dead_time").attr('value', prettyTime(targets[t].targets_dead_date));       
        //alert(curTargetID);
    }
    
    function addTargetInfo(sitecode, title, pid) // clicked on the website section list to create a new target
    {   
        curTargetID = -1;  // means new
        $("#id_target_site").text(sitecode);
        $("#id_target_title").text(title);
        $("#id_target_pid").text(pid);      
        $("#id_target_live_time").attr('value', prettyTime());
        $("#id_target_live").attr('value',prettyDate());
       // alert(d.getFullYear());
    }
    
    function saveTarget() // clicked on save target
    { //alert(curTargetID);
       var liveDate =  $("#id_target_live").attr('value');
       var liveTime =  $("#id_target_live_time").attr('value');
       var archiveDate =  $("#id_target_archive").attr('value');
       var archiveTime =  $("#id_target_archive_time").attr('value');
       var deadDate =  $("#id_target_dead").attr('value');
       var deadTime =  $("#id_target_dead_time").attr('value');
       
        
        if(curTargetID == -1)
        {
            targets.push({ record_state:        'NEW',
                           targets_pages_id:    $("#id_target_pid").text(),
                           targets_contents_fk: 0, 
                           title:               $("#id_target_site").text(),
                           targets_live_date:   formatTheDate(liveDate, liveTime) ,
                           targets_archive_date:formatTheDate(archiveDate, archiveTime),
                           targets_dead_date:   formatTheDate(deadDate, deadTime)
                         });

             //update the grid
            var newId = maxTargetId++;
      
             var newRow = '<tr>  <td id="id_target_title_'+ newId + '"onclick="editTarget('+ newId +')">'+  $("#id_target_site").text() +' '+  $("#id_target_title").text()+ '</td>' ;
             newRow += ' <td id="id_target_live_'+ newId +'">'+ liveDate +' '+ liveTime +' </td> ';
             newRow += ' <td id="id_target_archive_'+ newId +'">'+ archiveDate +' '+ archiveTime +' </td> ';
             newRow += ' <td id="id_dead_archive_'+ newId +'">'+ deadDate +' '+ deadTime +' </td> ';
             newRow += ' <td> - </td></tr>';
             $(newRow).appendTo('#id_target_table');
        }    
        else
        {
            targets[curTargetID].record_state = 'DIRTY';
            targets[curTargetID].targets_live_date =    formatTheDate(liveDate, liveTime);
            targets[curTargetID].targets_archive_date = formatTheDate(archiveDate, archiveTime);
            targets[curTargetID].targets_dead_date =    formatTheDate(deadDate, deadTime);

            // update the grid
            var id =  '#id_target_live_'+curTargetID;
            $(id).text( $("#id_target_live").attr('value')+' '+ $("#id_target_live_time").attr('value'));

            var id =  '#id_target_archive_'+curTargetID;
            $(id).text( $("#id_target_archive").attr('value')+' '+ $("#id_target_archive_time").attr('value'));

            var id =  '#id_target_dead_'+curTargetID;
            $(id).text( $("#id_target_dead").attr('value')+' '+ $("#id_target_dead_time").attr('value'));

            
        }

        curTargetID = -1;  // means new
        $("#id_target_site").text('');
        $("#id_target_title").text('');
        $("#id_target_pid").text('');      
        $("#id_target_live_time").attr('value', '');
        $("#id_target_live").attr('value','');
        $("#id_target_dead_time").attr('value', '');
        $("#id_target_dead").attr('value','');
        $("#id_target_archive_time").attr('value', '');
        $("#id_target_archive").attr('value','');
    }
    </script>
{/literal}


    <!-- Targets -->
        <div class="border ui-widget-content ui-corner-all p-10 bk_color2">
            <h3>Targets</h3>
            <div class="align-r headlink">
                <h6><div onclick="$('#id_edit_targets').toggle();" class="ui-state-default ui-corner-all float-r mt-5 pr-10 pl-10 pt-5">
                    <span class="ui-icon ui-icon-pencil float-l mb-5 mr-5"></span>
                        Edit
                </div></h6>
            </div>
            <br clear="all">
            <br clear="all">
            <table id="id_target_table">
                <tr>
                    <th>Destination</th>                    
                    <th>Live Date</th>
                    <th>Archive date</th>
                    <th>Dead Date</th>
                    <th class="align-c">Pinned</th>                    
                </tr>
                
                {foreach  $params->targets as $t}
                <tr >
                    <td id="id_target_title_{$t@index}"onclick="editTarget({$t@index})">{$t->pages_site_code} {$t->pages_title}</td>                    
                    <td id="id_target_live_{$t@index}">{$t->targets_live_date|date_format:$DATETIME_FORMAT}</td>  
                    <td id="id_target_archive_{$t@index}">{$t->targets_archive_date|date_format:$DATETIME_FORMAT}</td>                
                    <td id="id_target_dead_{$t@index}">{$t->targets_dead_date|date_format:$DATETIME_FORMAT}</td>
                    <td id="id_target_pin_{$t@index}"class="align-c">{$t->targets_pin_position}</td>                    
                </tr>                            
                {/foreach }                                                    
            
            </table>
       

        <!-- / Targets --> 
  <br clear="all">
   <hr />
   <br clear="all">
 
  
  <div id="id_edit_targets"  style="display:block; ">
 
  
     <div class="grid_5 ui-widget ui-corner-all "  style="padding:5px; border: 1px solid #AAAAAA">    
      
             <div id="wss_accordion"  class="accordion grid_5 alpha" >
                  
                  {assign "site" "-"}
                  {foreach $params->pages as $p}            
                       {if $site != $p->pages_site_code}
                       {if $site != '-'} </div> {/if}
                         <h3><a href="#">{$p->pages_site_code}</a></h3>
                         <div>
                         <ul>
                         {assign "site" {$p->pages_site_code}}
                       {/if}
                       <li onclick= "addTargetInfo('{$p->pages_site_code}', '{$p->pages_title}', {$p->pages_id})">{$p->pages_title}</li>  
                  {/foreach}
                  </ul>
                  </div>       
             </div>   <!-- end of accordion --> 
             <br clear="all">        
     </div>
          
     <div class="grid_12   omega" >
        <h5> Target info</h5>
      
       <span id="id_target_pid" style="display:none"></span> <br/>
       
        <table>  
        <tr>
            <td>page:</td><td collspan='3'>  <span id="id_target_site"></span> - <span id="id_target_title"></span> </td>       
        </tr>
        
        <tr>
            <td>live date: </td><td><input type="text" id="id_target_live"  class="datepicker date_time"/></td>
            <td>time</td><td> <input type="text" id="id_target_live_time"  class="date_time"/></td>
        </tr>
        <tr>
             <td>archive date: </td><td><input type="text" id="id_target_archive"   class="datepicker date_time"/></td>
             <td>time </td><td><input type="text" id="id_target_archive_time" class="date_time"/>(existing links still work)</td> 
        </tr>
        <tr>
             <td>dead date: </td><td><input type="text" id="id_target_dead"   class="datepicker date_time"/></td>
             <td>time</td><td> <input type="text" id="id_target_dead_time" class="date_time"/> (existing links will break)</td>
        </tr> 
        </table>
         <h6  onclick="saveTarget();"><div class="ui-state-red ui-corner-all float-r m-5 pr-10 pl-10 pt-5 pb-5" >
         <span class="ui-icon ui-icon-disk float-l mr-5"></span>
         SAVE TARGET</div></h6>
     </div>
         
  </div> <!-- end of id="id_edit_targets" -->
   <br clear="all">
   </div><!-- end of targets -->
 <!-- =============================================================================== end of enEdit targets ======== -->
        