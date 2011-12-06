{if !$PRINTING}
<div class="rounded-bottom-980" style="margin-left:-1px;"  id="body-round-bottom"> </div> 
</div> <!-- content_container -->
<br clear="all" />
   

$cms.template("gt_footer_content")

    {if $site_code == 'DC'}
        <div id="ads-bottom"> 
            <br style="height: 10px; clear: both;">          
            {include file="common/adModule.tpl" position="B1" ad_class="bottom-ad-pair"}
            {include file="common/adModule.tpl" position="B2"}                   
        </div>     
    {/if}   
    
</div>

<!-- Eloqua -->
<script src="http://www.govtech.com/includes/elqCfg.js" type="text/javascript"></script>
<script src="http://www.govtech.com/includes/elqImg.js" type="text/javascript"></script>

{literal}
<script type="text/javascript">
$(document).ready(function() {
    addInputDefaultToggle("#C_EmailAddress");
    addInputDefaultToggle("#search-field");
});
</script>
{/literal}

<!-- 
#if($req.param("menu") == "expand")
    $cms.template("gt_menu_expand")
#end##
 -->
 
{/if} {* end of:  if PRINTING == false *}



<script type="text/javascript">
<!--
    writeAds();   
// -->
</script>


{if $site_code != 'DC'}
    <!-- START Nielsen//NetRatings SiteCensus V5.3 -->
    <!-- COPYRIGHT 2007 Nielsen//NetRatings -->
    <script type="text/javascript">
    var _rsCI="us-bpaww";
    var _rsCG="0";
    var _rsDN="//secure-us.imrworldwide.com/";
    var _rsPLfl=0;
    var _rsSE=1;
    var _rsSM=1.0;
    var _rsCL=1;
    </script>
    <script type="text/javascript" src="//secure-us.imrworldwide.com/v53.js"></script>
    <noscript>
    <div><img src="//secure-us.imrworldwide.com/cgi-bin/m?ci=us-bpaww&cg=0&cc=1" alt=""/></div>
    </noscript>
    <!-- END Nielsen//NetRatings SiteCensus V5.3 -->
{/if}

</body>  
</html>

