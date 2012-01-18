<!-- listContent.tpl -->

<!--  
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        <span class="ui-icon ui-icon-info-tan float-l mr-10"></span>This view contains items that were created or changed in the last 180 days.       
        </div>
    </div>
-->
   

  <div class="ui-widget-content ui-corner-all bk_color2 p-10">          
            <h6>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="new_{$record_type}">remove from page</a>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Filter</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Copy</a>

          <a class="ui-state-inactive ui-corner-all mt-10 p-5 ml-20" href="#"><<</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5" href="#"><</a>          
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">View All 125</a>     
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">></a>
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">>></a>                              
            </h6>
    </div>

    <style>
     .cell{
           padding: 0 1em 0 0; 
           float:left;
     }
 
    
     #sortable { list-style-type: none; margin: 0; padding: 0; widtheee: 60%; }
     #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1em; height: 14px; }
     #sortable li span { position: absolute; margin-left: -1.3em; }
      #sortable li div {  padding: 0 1em; }
    </style>
    
    <script>
    $(function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    });
    </script>





    <div class="ui-widget-content ui-corner-all bk_color2">
      
        <div style="clear:both">
                <div class="cell" style="width:55px;"> &nbsp;(select)</div>
                <div class="cell" style="width:320px;"> Title</div>
                <div class="cell" style="width:100px;">Updated</div>
                <div class="cell" style="width:30px;">Status</div>
                <div class="cell" style="width:35px;">pinned</div>
                <div class="cell" style="width:80px;">Content Type</div>
                <br clear="all">
                
       </div>
        <br clear="all">
       <hr>
        <ul id="sortable">
        {foreach $contents as $c}
            <li style="padding:0.6em 0 0"><div style="clear:both">
                <div class="cell" style="width:15px;"><input type="checkbox" > </div>
                <div class="cell" style="width:300px;"><a href="/cms/gt/{$c->contents_type}/{$c->contents_id}">{$c->contents_title}</a></div>
                <div class="cell" style="width:120px;">{$c->contents_mod_date|date_format:$DATETIME_FORMAT}</div>
                <div style="width:5px;"{if $c->contents_live_rev > 0}   class="ui-icon ui-icon-circle-check cell"                         
                                          {else} class="ui-icon-tan ui-icon-alert cell"
                                          {/if} >
                   </div>
                <div class="cell" style="width:15px;"><input type="checkbox" {if $c->targets_pin_position < 9999}checked="checked" {/if}> </div>  
                <div class="cell" style="width:60px;">{$c->contents_type}</div>
                </div>
             
            </li>
        {/foreach}
        </ul>
     <br clear="all">
    </div>



   
