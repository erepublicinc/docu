<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html class=" ext-strict" xmlns="http://www.w3.org/1999/xhtml"> 
<head>

<meta charset="utf-8" />
<title>{if $page_title != '' }
           {$page_title} 
       {else}
            {$page->pages_display_title} 
       {/if}
</title>


{if $page->pages_no_robots || $environment != 'LIVE'}
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
{/if}
  
{* TODO do we need this ?*}
    <meta content="{$page->pages_meta_description}" name="description">
    
<!--    TODO
    #if($cms.content.keywords) ## for video articles
        <meta name="keywords" content="$cms.content.keywords">
    #end##
 
    #if($cms.websiteSection.publishRSS)##
        <link rel="alternate" type="application/rss+xml" title="RSS" href="#if($cms.websiteSection.feedburnerURL)$cms.websiteSection.feedburnerURL#else$cms.websiteSection.url/index.rss#end"> 
    #end##
-->    
    <link href="/includes/gt.css" rel="stylesheet" type="text/css"  >    
    {if $topLevelSection == "pcio"}
        <link href="includes/gt_pcio.css" rel="stylesheet" type="text/css"  >    
    {/if}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
    <!--[if lt IE 7]><link rel="stylesheet" href="$cms.include("gt_ie.css").url" type="text/css" media="screen, projection"><![endif]-->   
    <link rel="icon" href="{$designMediaPath}/gt_favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="{$designMediaPath}/gt_favicon.ico" type="image/x-icon">
 <!--  TODO  #if($cms.content && ($util.type($cms.content) == "GT_Article" || $util.type($cms.content) == "GT_Library_Item") &&  $req.intParam("page") > 1)##
       <link rel="canonical" href="$cms.content.url" />
    #end##  --> 
    <script type="text/javascript" src="http://www.governing.com/includes/jquery.cookie.js"></script>
    <script type="text/javascript" src="/common/includes/common_utils.js"></script>
    <script type="text/javascript" src="/includes/gt_common.js"></script>
   
    <script type="text/javascript" src="/common/includes/common_interstitial.js"></script>
    {if $site_code =='DC'}
        <meta name="google-site-verification" content="vXWPy_5ZuWH2q8A1sPU5qMhGOg6oAU3WLYkrAH16KAM" />
        <link href="/css/dc_new_cache.css" rel="stylesheet" type="text/css" />    
        <script type="text/javascript" src="/includes/basic.js"></script>
    {else}
 
    {/if}
    
    {* for advertising *}
    <script type="text/javascript">
       var AD_SOURCE = "{$ad_specs->ad_source}"
    </script>
    
</head>
<body class="  ext-gecko ext-gecko3"> 

<!-- GOOGLE ANALYTICS  -->
{if $environment == 'LIVE'}
    <script type="text/javascript">
       var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
       document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
        {if $site_code =='GT'}
            var pageTracker = _gat._getTracker("UA-732206-2");
        {elseif $site_code =='DC'}
            var pageTracker = _gat._getTracker("UA-732206-12");
        {/if}
            
        {literal}
        pageTracker._initData();
        pageTracker._trackPageview();
        
        function trackAnalytics(link)
        {
            pageTracker._trackPageview(link);
        }
        {/literal}
    </script>
{/if}

<div id="global-container"> 
<!-- INTERSTITIAL AD -->
        {if $site_code !='DC'}
            {include file="common/adModule.tpl" position="I1"}   
        {/if}    
        <!--- ADS TOP --->        
    <div id="ads-top" style="z-index: 300;">
            {literal}  
            <style type="text/css"> 
                #small, #large {position: absolute; right:0; top:0;}
                #small {z-index: 121;}
                #large {z-index: 120;}
            </style> 
            {/literal}
        {if $site_code !='DC'}
            {include file="common/adModule.tpl" position="T1"}
        {/if}
        {include file="common/adModule.tpl" position="T2" ad_class ="f-left mr-10" }
        {include file="common/adModule.tpl" position="T3"}
    </div>
       <!--- END - ADS TOP --->     
       <!--- SMALL GOVTECH --->                
    <a href="www.govtech.com" style="float: right;"> 
        <img src="{$designMediaPath}/gt_logo_sm.gif" alt="Government Technology" style="height: 14px; margin:0px!important; padding:0px!important;"> 
    </a> 
    <!--- END - SMALL GOVTECH ---> 
        <!--- TOP NAV ---> 
        <div id="top-nav" class="spcr"> 
        {if $site_code =='DC'}       
            <ul>                    
                <li {if $topLevelSection == "/"} id="current" {/if}><a href="/">Home</a></li>
                <li {if $topLevelSection == "magazine"} id="current" {/if}><a href="/magazine">Issues</a></li>
                <li {if $topLevelSection == "library"} id="current" {/if}><a href="/library">Library</a></li>
                <li {if $topLevelSection == "events"} id="current" {/if}><a href="/events">Events</a></li>
                <li {if $topLevelSection == "blogs"} id="current" {/if}><a href="/blogs">Blogs</a></li>
                <li {if $topLevelSection == "videos"} id="current" {/if}><a href="/videos">Videos</a></li>
                <li {if $topLevelSection == "survey" && $page->pages_id == "Counties"} id="current" {/if}><a href="/survey/counties/">Counties Surveys</a></li>
                <li {if $topLevelSection == "survey" && $page->pages_id == "Cities"} id="current"{/if}><a href="/survey/cities/">Cities Surveys</a></li>
                <li {if $topLevelSection == "jobs"} id="current" {/if}><a href="/jobs">Jobs</a></li>
                <li {if $topLevelSection == "members"} id="current" {/if}><a href="/members/Become-a-Digital-Communities-Industry-Member.html">Advertise</a></li>
            </ul>
        {else}
            <ul>            
                <li {if $topLevelSection == "/"} id="current" {/if}><a href="/">Home</a></li> 
                <li {if $topLevelSection == "topics"} id="current" {/if}><a href="/topics">News Topics</a></li>            
                <li {if $topLevelSection == "jobs"} id="current" {/if}><a href="/jobs">Jobs</a></li> 
                <li><a href="www.digitalcommunities.com">Digital Communities</a></li> 
                <li {if $topLevelSection == "videos"} id="current" {/if}><a href="/videos">Video</a></li> 
                <li {if $topLevelSection == "events"} id="current" {/if}><a href="http://events.govtech.com/events">Events</a></li> 
                <li {if $topLevelSection == "webinars"} id="current" {/if}><a href="/webinars">Webinars</a></li> 
                <li {if $topLevelSection == "library"} id="current" {/if}><a href="/library">Papers & Books</a></li> 
                <li {if $topLevelSection == "grants"} id="current" {/if}><a href="/grants">Grants</a></li> 
                <li {if $topLevelSection == "magazines"} id="current" {/if}><a href="/magazines">Magazines</a></li> 
                 {if $topLevelSection == "pcio"}<li {if $topLevelSection =="/pcio/special_reports"} id="current"{/if}><a href="/pcio/special_reports">CIO Special Reports</a></li> {/if}
                <li><a href="http://www.erepublic.com/advertise/govtech">Advertise</a></li> 
            </ul>
        {/if}
        </div> 

        <!---END - TOP NAV ---> 
    <div class="masthead-top"></div> 
        <!--- MASTHEAD ---> 
        <div id="masthead">
        {if $site_code == "PCIO"}  {$homePath = "/pcio"} {else} {$homePath = "/"} {/if}
        <a href="{$homePath}" onclick="trackAnalytics('homepage_logo_big');" class="f-left mr-10"> 
        <img class="ml-10" src="{$designMediaPath}/$logo->media_url}" alt="{$logo->media_altText}" align="bottom"></a> 
        {if $site_code =="PCIO"}
            <div id="navbanner">
                {include file="common/adModule.tpl" position="T4"}
            </div>
        {else}
        <!--- SEARCH / LOGIN ---> 
        <div id="mygt-login">        
            <form action="/search" method="get" id="search_form">                 
                <input name="k" id="search-field" class="search-field color3" value=" Search" type="text"> 
                <input id="top-submit" src="{$designMediaPath}" alt="Search" type="image"> 
            </form> 
            <br> 
        </div> 
       {/if}
        <!--- END - SEARCH / LOGIN ---> 
    </div> 
    <!--- END - MASTHEAD  ---> 
 
<div class="rounded-bottom-980"></div> 

<div class="rounded-top-980 mt-10 hd" id="body-round-top"> </div>
<div id="content-container"> <!-- the end tag is in gt_foot -->

<!-- ================== end of head.tpl ================= -->