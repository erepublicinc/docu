<script>

</script>

<!-- revisionHistoryModule.tpl -->
    <!-- rev History -->       
    <div class="ui-widget-content ui-corner-all p-10 bk_color1">
        <h3>Revision History </h3>
        <br clear="all">
        <table class="bk_color3 ui-corner-all">
            	<tr>
                	<td colspan="2" class="align-r ui-corner-tl" width="60%"><h6>Live Revision:</h6> </td>    
                    <td class="ui-corner-tr">{$params->live_rev}</td>            
                </tr>  
            	<tr>
                	<td colspan="2" class="align-r" width="60%"><h6>Preview Revision:</h6> </td>    
                    <td>{$params->preview_rev}</td>            
                </tr>
            	  
		</table>         
        <div class="m-10">
        {if $model_name == 'Page'}
            <form method="post" action="/cms/{$site_code}/Page">
             <input type="hidden" name="rev" value="{$p->pages_rev}" />
             <input type="hidden" name="id" value="{$p->pages_id}" />
        {else}
            <form method="post" action="/cms/{$site_code}/{$model_name}">
            <input type="hidden" name="id" value="{$content->contents_id}" />
        {/if}
                       
			<table class="condenced">
              {foreach $params->history as $h}
            	<tr>
                	<td><input type="radio" name="rev" value="{$h->rev}"></td>
                	{* <td>{$h->rev}</td> *}
                    <td>
                        {if $model_name == 'Page'}
                           <a  target='_blank' href="/cms/{$site_code}/{$model_name}/{$h->id}?rev={$h->rev}" >
                        {else} 
                            <a  target='_blank' href="/cms/{$site_code}/{$model_name}/{$content->contents_id}?rev={$h->rev}" >
                        {/if}
                        {$h->rev_date|date_format:$DATETIME_FORMAT}</a></td>
	            </tr>
                <tr><td colspan="3" style="padding:0 0 0px 40px;">Edited:	{$h->users_first_name} {$h->users_last_name }</td></tr>
                <tr><td colspan="3" style="padding:0 0 15px 40px;"><i>{$h->rev_comment }</i></td></tr>
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


    
    