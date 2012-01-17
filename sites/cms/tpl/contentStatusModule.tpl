<!-- contentStatushModule.tpl -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
            {if $form_data}
               <h2>ID: {$form_data.contents_fid.value}      version: {$form_data.contents_version.value}</h2> 
            {else}
               <h2>ID: {$content->contents_fid}      version: {$content->contents_version}</h2> 
            {/if}
			<h6>
            <span class="float-l mr-10">Status:</span> <span class="ui-icon ui-icon-circle-check float-l mr-5"></span>
             </h6>
               
        </div> 
        <br clear="all">                                                          
    </div> 
	
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        	<h6>
            {if $form_data}
               Created:  {$form_data.contents_create_date.value|date_format:$DATETIME_FORMAT}<br>
 			   Modified: {$form_data.contents_change_date.value|date_format:$DATETIME_FORMAT}</h6>
            {else}
               Created:  {$content->contents_create_date|date_format:$DATETIME_FORMAT}<br>
               Modified: {$content->contents_change_date|date_format:$DATETIME_FORMAT}</h6>
            {/if}
        </div>                                                           
    </div>
     
