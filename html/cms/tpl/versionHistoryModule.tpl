<!-- versionHistoryModule.tpl -->
    <!-- Version History -->       
    <div class="ui-widget-content ui-corner-all p-10 bk_color1">
        <h3>Version History</h3>
        <br clear="all">
        <table class="bk_color3 ui-corner-all">
            	<tr>
                	<td colspan="2" class="align-r ui-corner-tl" width="60%"><h6>Live Version:</h6> </td>    
                    <td class="ui-corner-tr">{$content->contents_live_version}</td>            
                </tr>  
            	<tr>
                	<td colspan="2" class="align-r" width="60%"><h6>Preview Version:</h6> </td>    
                    <td>{$content->contents_preview_version}</td>            
                </tr>
            	  
		</table>         
        <div class="m-10">
        
			<table class="condenced">
              {foreach $history as $h}
            	<tr>
                	<td><input type="checkbox" name="Name"></td>
                	<td>{$h->version}</td>
                    <td><a  target='_blank' href="/cms/ALL/{$content->contents_type}/{$content->contents_pk}?version={$h->version}" >{$h->update_date|date_format:$DATETIME_FORMAT}</span></td>
	            </tr>
                <tr><td colspan="3" style="padding:0 0 0px 40px;">Author:	{$h->users_first_name} {$h->users_last_name }</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Comment: <br>{$h->comment }</td></tr>
            	<tr style="padding-bottom:15px;" />
              {/foreach}       
            </table>
            
           	
            <h6 class="mt-15">
              <a href="#" class="ui-state-default ui-corner-all pr-10 pl-10 p-5">Make Live</a></h6>
        </div>        
    </div> 


    
    