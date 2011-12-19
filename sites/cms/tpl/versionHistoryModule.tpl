<script>

</script>

<!-- versionHistoryModule.tpl -->
    <!-- Version History -->       
    <div class="ui-widget-content ui-corner-all p-10 bk_color1">
        <h3>Version History </h3>
        <br clear="all">
        <table class="bk_color3 ui-corner-all">
            	<tr>
                	<td colspan="2" class="align-r ui-corner-tl" width="60%"><h6>Live Version:</h6> </td>    
                    <td class="ui-corner-tr">{$params->live_version}</td>            
                </tr>  
            	<tr>
                	<td colspan="2" class="align-r" width="60%"><h6>Preview Version:</h6> </td>    
                    <td>{$params->preview_version}</td>            
                </tr>
            	  
		</table>         
        <div class="m-10">
        {if $record_type == 'page'}
            <form method="post" action="/cms/{$site_code}/pages">
             <input type="hidden" name="rev" value="{$p->pages_rev}" />
             <input type="hidden" name="id" value="{$p->pages_id}" />
        {else}
            <form method="post" action="/cms/{$site_code}/articles">
            <input type="hidden" name="id" value="{$content->contents_id}" />
        {/if}
                       
			<table class="condenced">
              {foreach $params->history as $h}
            	<tr>
                	<td><input type="radio" name="version" value="{$h->version}"></td>
                	<td>{$h->version}</td>
                    <td>
                        {if $record_type == 'page'}
                           <a  target='_blank' href="/cms/{$site_code}/page/{$h->id}?version={$h->rev}" >
                        {else} 
                            <a  target='_blank' href="/cms/{$site_code}/{$content->contents_type}/{$content->contents_id}?version={$h->version}" >
                        {/if}
                        {$h->version_date|date_format:$DATETIME_FORMAT}</a></td>
	            </tr>
                <tr><td colspan="3" style="padding:0 0 0px 40px;">Author:	{$h->users_first_name} {$h->users_last_name }</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;">Comment: <br>{$h->version_comment }</td></tr>
            	<tr style="padding-bottom:15px;" />
              {/foreach}       
            </table>
         
        
           
           
         <h6 class="mt-15">
              <input type="submit" name="makelive"    value="Make Live"    style="width:70px;" class="ui-state-default ui-corner-all pr-10 pl-10 p-5" />
              <input type="submit" name="makepreview" value="Make Preview" style="width:70px;" class="ui-state-default ui-corner-all pr-10 pl-10 p-5" />
         </h6>
         
        </form>   
        </div>        
    </div> 


    
    