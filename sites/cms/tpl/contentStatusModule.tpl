<!-- contentStatushModule.tpl -->
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
           
            <h2>ID: {$content->contents_fid}      Revision: {$content->contents_rev}</h2> 
          
			<h6>
            <span class="float-l mr-10">Status:</span> <span class="ui-icon ui-icon-circle-check float-l mr-5"></span>
             </h6>
               
        </div> 
        <br clear="all">                                                          
    </div> 
	
    <div class="ui-widget-content ui-corner-all">
        <div class="m-10">
        	<h6>
           
               Created:  {$content->contents_create_date|date_format:$DATETIME_FORMAT}<br>
               Modified: {$content->contents_mod_date|date_format:$DATETIME_FORMAT}</h6>
           
        </div>                                                           
    </div>
     
