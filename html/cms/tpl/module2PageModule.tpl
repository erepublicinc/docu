<!-- ================== start of editModule2Page.tpl ================== -->


{literal}

<meta charset="utf-8">
	
	
	
<meta charset="utf-8">
	
	

	
	<style>
	h1 { padding: .2em; margin: 0; }
	#module_list { float:left; width: 150px; margin-right: 2em; }
    #module_list ul { margin: 0; padding: 1em 0 1em 0;}
	.col_list    { float:left; width: 135px; margin-right: 2em; }
	/* style the list to maximize the droppable hitarea */
	.col_list ol { margin: 0; padding: 1em 0 1em 0; }
    
    .border {border: solid black 2px}
	</style>
    
    
	<script>

$(function() {
		
		$( "#tabs2" ).tabs();
        
		$( "#all_modules" ).accordion();
		$( "#all_modules li" ).draggable({
			appendTo: "body",
			helper: "clone"
		});
        
		$( ".col_list ol" ).droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ) {
				$( this ).find( ".placeholder" ).remove();
				$( "<li></li>" ).text( ui.draggable.text() ).appendTo( this );
			}
		}).sortable({
			items: "li:not(.placeholder)",
			connectWith: "#trash_can",
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$( this ).removeClass( "ui-state-default" );
			}
		});

		$( "#trash_can" ).sortable();
        /*{
            activeClass: "border",
            hoverClass: "border"
                
             }); 
          */   
             
        
	});

    // collects all data from the 6 module lists and retruns it as a json string 
    function collectModuleInfo()
    {
       // alert('start');
        var dta = [];
        
    	$(".col_list").each(function(index){
    		
            var placemnt = $(this).attr('id');
            var linkOrder = 0;
            //alert('p: '+ placement);
            $(this).find('li').each(function(index){
            	//alert(index + ': ' + placement + ' ' + $(this).text());
                var pieces = $(this).text().split('#');
                if(pieces.length > 1)
                {
                	linkOrder++; 
                    var num = parseInt(pieces[pieces.length -1 ]);
                   // alert(placemnt+ ' '+num);
                    dta.push({placement: placemnt, contents_fk: num, link_order: linkOrder });         
                }
            });
    		
        });
       return JSON.stringify(dta); 
    } 

    
	</script>
    
{/literal}


<div class="demo">
	
    <p><i> Drag the modules to the column lists </i></p>
    
<div id="module_list">
	<h2 class="ui-widget-header">Modules</h2>	
	<div id="all_modules">
		<h5><a href="#">Common</a></h5>
		<div>
			<ul>
            {foreach $params->modules as $m}
                {if $m->modules_site_code =="COMMON" } 
                    <li> {$m->contents_title} #{$m->contents_pk}</li>
                {/if}
            {/foreach}           
			</ul>
		</div>
		<h5><a href="#">{$site_code}</a></h5>
		<div>
			<ul>
			{foreach $params->modules as $m}
                {if $m->modules_site_code == $site_code }    
                    <li> {$m->contents_title} #{$m->contents_pk}</li>
                {/if}
            {/foreach}
			</ul>
		</div>
		
	</div>
</div>



<div id="tabs2" style="float:left;">
    <ul>
        <li><a href="#listing_tab">Listing Page</a></li>
        <li><a href="#details_tab">Detail Pages</a></li>
        
    </ul>

    <div id="listing_tab">
        <div id="LISTING_LEFT_COLUMN" class="col_list" >
        	<h3 class="ui-widget-header">Left Column</h3>
        	<div class="ui-widget-content">
        		<ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "LISTING_LEFT_COLUMN" }
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
        			<!--  li class="placeholder">Drop module here</li> -->
        		</ol>
        	</div>
        </div>
        
        <div id="LISTING_CENTER_COLUMN" class="col_list"  >
            <h3 class="ui-widget-header">Center Column</h3>
            <div class="ui-widget-content">
                <ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "LISTING_CENTER_COLUMN" }
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
                </ol>
            </div>
        </div>
        
        <div id="LISTING_RIGHT_COLUMN" class="col_list"  >
            <h3 class="ui-widget-header">Right Column</h3>
            <div class="ui-widget-content">
                <ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "LISTING_RIGHT_COLUMN" }
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
                </ol>
            </div>
        </div>
        
    </div> <!-- end of tab 1 -->

    <div id="details_tab">
        <div id="DETAIL_LEFT_COLUMN" class="col_list">
            <h3 class="ui-widget-header">Left Column</h3>
            <div class="ui-widget-content">
                <ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "DETAIL_LEFT_COLUMN" }
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
                </ol>
            </div>
        </div>
        
        <div id="DETAIL_CENTER_COLUMN" class="col_list">
            <h3 class="ui-widget-header">Center Column</h3>
            <div class="ui-widget-content">
                <ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "DETAIL_CENTER_COLUMN" }
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
                </ol>
            </div>
        </div>
        
        <div id="DETAIL_RIGHT_COLUMN" class="col_list">
            <h3 class="ui-widget-header">Right Column</h3>
            <div class="ui-widget-content">
                <ol>
                    {foreach $params->page_modules as $m}
                        {if $m->placement == "DETAIL_RIGHT_COLUMN" } 
                            <li>{$m->contents_title} #{$m->contents_pk}</li>
                        {/if}
                    {/foreach}
                </ol>
            </div>
        </div>
       
    </div> <!-- end of details_tab  -->
    
    
</div> <!-- end of tabs -->    
    
    
    
<div >
   <img id='trash_can' width="48" src="/images/trash_can.png" style = "float:right;" />
</div>

</div>
</div>

</div><!-- End demo -->





 <!-- =============================================================================== end of editModule2Page ======== -->
 
