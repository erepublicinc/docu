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
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">New</a>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Filter</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Copy</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Quick Edit</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Target</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Categorize</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 mr-10 pr-10 pl-10" href="#">Delete</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 ml-20" href="#"><<</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5" href="#"><</a>          
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">View All 125</a>     
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">></a>
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">>></a>                              
            </h6>
    </div>


    <div class="ui-widget-content ui-corner-all bk_color2">
        <table class="datatable">
            <tr>
                <th>&nbsp;</th>
                <th>Title</th>
                <th>Last Modified</th>
                <th>Status</th>
                <th>Assigned To</th>
            </tr>
            
            {foreach $contents as $c}
            <tr>
                <td><input name="" type="checkbox" value=""></td>
                <td><a href="/cms/gt/{$record_type}/{$c->contents_pk}">{$c->contents_title}</a></td>
                <td>{$c->contents_updated_date|date_format:$DATETIME_FORMAT}</td>
                <td><span {if $c->contents_status == 'LIVE'}  class="ui-icon ui-icon-circle-check float-l mr-5" 
                          {elseif $c->contents_status == 'PREVIEW'}  class="ui-icon ui-icon-circle-check float-l mr-5" 
                          {else} class="ui-icon-tan ui-icon-alert float-l mr-10"
                          {/if}
                   </span></td>
                <td>eRepublic - Editors</td>
            </tr>  
            {/foreach}
         </table>
     </div> 
