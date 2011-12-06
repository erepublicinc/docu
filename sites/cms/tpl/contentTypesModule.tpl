<!-- contentTypesModule.tpl -->
		<!-- Accordion -->
		<div id="accordion_content_types" class="accordion">
			<div>
				<h3><a href="#">Content</a></h3>
				<div>
                    <a href="#">Ad Module</a>
    				<a href="/cms/{$site_code}/articles">Articles</a>
    				<a href="/cms/{$site_code}/modules">Modules</a>              
    				<a href="/cms/{$site_code}/pages">Pages</a>          
			    </div>	
			</div>
			<div>
				<h3><a href="#">Media</a></h3>
				<div><a href="#">Images</a>
    				<a href="#">Audio</a>
    				<a href="#">Video</a>
    				<a href="#">Documents</a>
    				<a href="#">Binary</a>
    				<a href="#">Import Media</a>
    			</div>       
			</div>
			<div>
				<h3><a href="#">Placement</a></h3>
				<div>
                 {foreach $params->pages as $page}
                    <a href="/cms/{$site_code}/placement/{$page->pages_id}">{$page->pages_title}</a>
    			 {/foreach}	
				</div>
			</div>
		</div>