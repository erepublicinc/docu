<!-- contentTypesModule.tpl -->
		<!-- Accordion -->
		<div id="accordion_content_types" class="accordion">
			<div>
				<h3><a href="#">Content</a></h3>
				<div>
    				<a href="/cms/{$site_code}/Article">Articles</a>
    				       
			    </div>	
			</div>
            <div>
                <h3><a href="#">Design</a></h3>
                <div>          
                    <a href="/cms/{$site_code}/Module">Modules</a>              
                    <a href="/cms/{$site_code}/Page">Pages</a>          
                </div>  
            </div>
            <div>
                <h3><a href="#">Admin</a></h3>
                <div>
                    <a href="/cms/{$site_code}/User">Users</a>
                    <a href="/cms/{$site_code}/Author">Author Profiles</a>                      
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
                    <a href="/cms/{$site_code}/Placement/{$page->pages_id}">{$page->pages_title}</a>
    			 {/foreach}	
				</div>
			</div>
		</div>