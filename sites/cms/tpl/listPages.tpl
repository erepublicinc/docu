
<!-- listPages.tpl -->
 <div class="ui-widget-content ui-corner-all bk_color2 p-10">          
            <h6>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="{$record_type}/new">New</a>
          <a class="ui-state-red ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Filter</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Copy</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Quick Edit</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Target</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 pr-10 pl-10" href="#">Categorize</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 mr-10 pr-10 pl-10" href="#">Delete</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5 ml-20" href="#"><<</a>
          <a class="ui-state-inactive ui-corner-all mt-10 p-5" href="#"><</a>          
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#">View All 125</a>     
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#"></a>
          <a class="ui-state-red ui-corner-all mt-10 p-5" href="#"></a>                              
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
            
            {foreach $pages as $p}
            <tr>
                <td><input name="" type="checkbox" value=""></td>
                <td><a href="/cms/{$site_code}/page/{$p->pages_pk}">{$p->pages_title}</a></td>
                <td>{$p->pages_version_date|date_format:$DATETIME_FORMAT}</td>
                <td><span {if $p->pages_status == 'READY'}  
                                class="ui-icon ui-icon-circle-check float-l mr-5" >
                          {elseif $p->pages_status == 'REVIEW'}  
                                class="ui-icon ui-icon-alert float-l mr-5" >
                          {else} 
                                class=""> -
                          {/if}
                   </span></td>
                <td>eRepublic - Editors</td>
            </tr>  
            {/foreach}
         </table>
     </div> 