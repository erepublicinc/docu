<?php /* Smarty version Smarty 3.1.0, created on 2011-12-08 09:25:37
         compiled from "/var/www/newgt/sites/cms/tpl/targetsModule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6352722114ee0f311782172-30878379%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1cb9f2c5f07bc961fe0f990bc306a9398a8eb44d' => 
    array (
      0 => '/var/www/newgt/sites/cms/tpl/targetsModule.tpl',
      1 => 1322093493,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6352722114ee0f311782172-30878379',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'params' => 0,
    't' => 0,
    'DATETIME_FORMAT' => 0,
    'site' => 0,
    'p' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.0',
  'unifunc' => 'content_4ee0f3119a2c4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ee0f3119a2c4')) {function content_4ee0f3119a2c4($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/newgt/includes/plugins/modifier.date_format.php';
?>

    <style>
    .date_time{
        width:70px;
    }
    </style>


<script>

    var targets = [];
    <?php  $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->targets; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['t']->index=-1;
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['t']->key => $_smarty_tpl->tpl_vars['t']->value){
$_loop = true;
 $_smarty_tpl->tpl_vars['t']->index++;
?>
      targets.push( { record_state:'CLEAN', targets_pages_id:<?php echo $_smarty_tpl->tpl_vars['t']->value->targets_pages_id;?>
, targets_contents_fk:<?php echo $_smarty_tpl->tpl_vars['t']->value->targets_contents_fk;?>
, title:'<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['t']->value->pages_title, ENT_QUOTES, 'UTF-8', true);?>
',targets_live_date:'<?php echo $_smarty_tpl->tpl_vars['t']->value->targets_live_date;?>
',targets_archive_date:'<?php echo $_smarty_tpl->tpl_vars['t']->value->targets_archive_date;?>
',targets_dead_date:'<?php echo $_smarty_tpl->tpl_vars['t']->value->targets_dead_date;?>
',targets_pin_position: <?php echo $_smarty_tpl->tpl_vars['t']->value->targets_pin_position;?>
 }); 
    <?php } ?>      

   

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
    
    function editTarget(t)
    {
        //alert(t);
        curTargetID = t;
        //$("#id_target_site").text(targets[t].title);
        $("#id_target_title").text(targets[t].title);
        $("#id_target_pid").text(targets[t].targets_pages_id);      
        $("#id_target_live").attr('value', prettyDate(targets[t].targets_live_date));
        $("#id_target_live_time").attr('value', prettyTime(targets[t].targets_live_date));       
        //alert(curTargetID);
    }
    
    function addTargetInfo(sitecode, title, pid)
    {
        curTargetID = -1;  // means new
        $("#id_target_site").text(sitecode);
        $("#id_target_title").text(title);
        $("#id_target_pid").text(pid);      
        $("#id_target_live_time").attr('value', prettyTime());
        $("#id_target_live").attr('value',prettyDate());
       // alert(d.getFullYear());
    }
    function saveTarget()
    { //alert(curTargetID);
        if(curTargetID == -1)
        {
            targets.push({ record_state:'NEW',
                           targets_pages_id:$("#id_target_pid").text(),
                           targets_contents_fk:0, 
                           title:$("#id_target_site").text(),
                           targets_live_date:formatTheDate($("#id_target_live").attr('value'), $("#id_target_live_time").attr('value')) ,
                           targets_archive_date:formatTheDate($("#id_target_archive").attr('value'), $("#id_target_archive_time").attr('value')), 
                           targets_dead_date:formatTheDate($("#id_target_dead").attr('value'), $("#id_target_dead_time").attr('value'))  
                         });
            
        }    
        else
        {
            targets[curTargetID].record_state = 'DIRTY';
            targets[curTargetID].targets_live_date =    formatTheDate($("#id_target_live").attr('value'), $("#id_target_live_time").attr('value'));
            targets[curTargetID].targets_archive_date = formatTheDate($("#id_target_archive").attr('value'), $("#id_target_archive_time").attr('value'));
            targets[curTargetID].targets_dead_date =    formatTheDate($("#id_target_dead").attr('value'), $("#id_target_dead_time").attr('value'));
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
            <table>
                <tr>
                    <th>Destination</th>                    
                    <th>Live Date</th>
                    <th>Archive date</th>
                    <th>Dead Date</th>
                    <th class="align-c">Pinned</th>                    
                </tr>
                
                <?php  $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->targets; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['t']->index=-1;
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['t']->key => $_smarty_tpl->tpl_vars['t']->value){
$_loop = true;
 $_smarty_tpl->tpl_vars['t']->index++;
?>
                <tr >
                    <td onclick="editTarget(<?php echo $_smarty_tpl->tpl_vars['t']->index;?>
)"><?php echo $_smarty_tpl->tpl_vars['t']->value->pages_site_code;?>
 <?php echo $_smarty_tpl->tpl_vars['t']->value->pages_title;?>
</td>                    
                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['t']->value->targets_live_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</td>  
                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['t']->value->targets_archive_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</td>                
                    <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['t']->value->targets_dead_date,$_smarty_tpl->tpl_vars['DATETIME_FORMAT']->value);?>
</td>
                    <td class="align-c"><?php echo $_smarty_tpl->tpl_vars['t']->value->targets_pin_position;?>
</td>                    
                </tr>                            
                <?php } ?>                                                    
            
            </table>
       

        <!-- / Targets --> 
  <br clear="all">
   <hr />
   <br clear="all">
 
  
  <div id="id_edit_targets"  style="display:block; ">
 
  
     <div class="grid_5 ui-widget ui-corner-all "  style="padding:5px; border: 1px solid #AAAAAA">    
      
             <div id="wss_accordion"  class="accordion grid_5 alpha" >
                  
                  <?php $_smarty_tpl->tpl_vars["site"] = new Smarty_variable("-", null, 0);?>
                  <?php  $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['params']->value->pages; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
$_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['p']->key => $_smarty_tpl->tpl_vars['p']->value){
$_loop = true;
?>            
                       <?php if ($_smarty_tpl->tpl_vars['site']->value!=$_smarty_tpl->tpl_vars['p']->value->pages_site_code){?>
                       <?php if ($_smarty_tpl->tpl_vars['site']->value!='-'){?> </div> <?php }?>
                         <h3><a href="#"><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_site_code;?>
</a></h3>
                         <div>
                         <ul>
                         <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_site_code;?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["site"] = new Smarty_variable($_tmp1, null, 0);?>
                       <?php }?>
                       <li onclick= "addTargetInfo('<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_site_code;?>
', '<?php echo $_smarty_tpl->tpl_vars['p']->value->pages_title;?>
', <?php echo $_smarty_tpl->tpl_vars['p']->value->pages_id;?>
)"><?php echo $_smarty_tpl->tpl_vars['p']->value->pages_title;?>
</li>  
                  <?php } ?>
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
        <?php }} ?>