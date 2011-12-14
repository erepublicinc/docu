/*
* Common Functions
*/
function getBrowserType()
{
    var u = ""+navigator.userAgent;
    if(u.indexOf("Android") >= 0 )
        return('Android');
    if(u.indexOf("iPad") >= 0)
        return 'iPad';	
    if(u.indexOf("iPhone") >= 0)
        return 'iPhone';	
    if(u.indexOf("BlackBerry") >= 0)
        return 'BlackBerry';	
    if(u.indexOf("BOLT") >= 0)
        return 'BlackBerry';			
    if(u.indexOf("IEMobile") >= 0)// Windows CE
        return 'IEMobile';		
    if(u.indexOf("IE6-7") >= 0)
        return 'IE6-7';
    if(u.indexOf("Opera") >= 0)
        return 'Opera';		
    if(u.indexOf("Chrome") >= 0)
        return 'Chrome';
    if(u.indexOf("Firefox") >= 0)
        return 'Firefox';			
    return 'other';  
}

// this is called from the COM_ad_load  script which is included just before the /body tag
function writeAds() 
{
 
    for (var i = 0; i < ads.length; i++)
    {   
        var ad 		 = ads[i];    
        var div 	 = ad.div;
        var position 	 = ad.position;
        var width 	 = ad.width;
        var height 	 = ad.height;
        var site 	 = ad.site;
        var id		 = ad.id;
        var zone	 = ad.zone;
        var tmpDiv  	 = 'tmp_' + div;
        var frame   	 = 'frame_' + div;
        var isrc 	 = '"/templates/common_ad_iframe.html?timer=0&position=' + position 
    				+ '&height=' + height 
    				+ '&width=' + width
    				+ '&site=' + site
    				+ '&id=' + id
    				+ '&zone=' + zone    
    			        +  '"';
    	var onloadFn = '';
    	if(position.toLowerCase() != "t2")
    	{
    		onloadFn = 'onload="squashFrame(this);"';
    	}
    	document.write('<div id="'+tmpDiv+'">'+
        '<iframe id="'+frame+'" name="'+frame+'" '+
        onloadFn +
        'src='+ isrc +
        'width="'+ width +'" height="'+height+'" noresize scrolling="no" '+
        'frameborder="0" marginheight="0" marginwidth="0" '+
        'allowTransparency="true" style="background:transparent"></iframe></div>');

		document.getElementById(div).appendChild(document.getElementById(tmpDiv));
    }
}

function squashFrame(frame) {
	var theIFrame = frame.contentWindow || frame.contentDocument;
	if (theIFrame.document) {
		theIFrame = theIFrame.document;
	}
	var content = theIFrame.documentElement.innerHTML;
	if (content.indexOf("817-grey.gif") != -1) {
		frame.style.height = "1px";
	}
}
   	
// extracts the id parameter from the iframe src query string params,
// replaces it with a new contentID value, and reloads the iframe with 
// the updated src
function reloadAd(frameID, contentID)
{
	var frame = document.getElementById(frameID);
	if (frame == undefined) return;
	var d    = frame.src;
	if (d == undefined) return;
		var pos1  = d.indexOf("&video_id=");

    if (pos1 == -1)
    {
        pos1 = d.indexOf("&id=") + 1;
        pos2 = d.indexOf('&', pos1);
        var s1 = d.substr(0, pos2 - 1);
        var s2 = d.substr(pos2);
    }
    else
    {
        var pos2 = d.indexOf('&', ++pos1);
        var s1 = d.substr(0, --pos1);
        var s2 = d.substr(pos2);
    }
    var newSrc = s1 + '&video_id=' + contentID + s2;
    document.getElementById(frameID).src = newSrc;
}

function hideAdZone(elem)
{
	var ad_content = elem.innerHTML;
	 if (ad_content.indexOf('-grey.gif') != -1)
	 {
		elem.style.width = 0;
		elem.style.height = 0;
		elem.style.display = 'none';
	}
}
            
function readCookie(c_name)
{
    if (document.cookie.length>0)
    {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1)
		{
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) 
			{
				c_end=document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}
    
    
function setRequestorIP()
{
	var randomnumber=Math.floor(Math.random()*1000000);
	document.write('<scr'+'ipt language="JavaScript" type="text/javascript" src="http://' + window.location.host + '/templates/common_set_ip.js?c=n&ran=' + randomnumber + ' "></scr' + 'ipt>');
}

function display_ad(id, newsletterID, site, zone, position, width, height, debug, iframe_enabled)
{
    if (newsletterID != 'false')
    {
        id += ";nlid=" + newsletterID;
    }
    if (typeof(DELVE_ID) != "undefined")
    {
        id += ";video_id=" + DELVE_ID;
    }
	
    if(typeof(cachebuster) == "undefined") {var cachebuster = Math.floor(Math.random()*10000000000)}
    if(typeof(dcopt) == "undefined") {var dcopt = "dcopt=ist;"} else {var dcopt = ""}
    if(typeof(tile) == "undefined") {var tile = 1} else {tile++}

    var srcUrl = '<scr'+'ipt src="http://ad.doubleclick.net/adj/'
	   + site
	   + zone
	   + ';pos='
	   + position
	   + ';tile='
	   + tile + ';'
	   + dcopt
	   + 'sz='
	   + width + 'x' + height
	   + ';id='
	   + id
	   + ';ord='
	   + cachebuster
	   + '?"></scr'+'ipt>';

    // ON GT and DC, the ads may be served via an iframe for certain positions and zones (fyi: video page requires T2 to always be in an iframe)
    if (iframe_enabled == 'true')
    {
        if ((position == 'R4') || (position == 'T1') || (position == 'I1') 
		|| (position == 'T4') || (position.indexOf('tl') != -1) 
		|| (position == 'T2' && zone.indexOf('videos') == -1 && zone.indexOf('video') == -1))
        {
            document.write(srcUrl);
        }
        else
        {
            var ad 		= {};
            ad.div		= 'ad_' + position;
            ad.position	= position;
            ad.height	= height;
            ad.width	= width;
            ad.site		= site;
            ad.newsletterID = newsletterID;
            ad.requestorIP	= requestorIP;
            ad.id		= id;
            ad.zone		= zone;
            ads.push(ad);
        }
    }
    else
    {
        document.write(srcUrl);
    }
}

/*
* Common ad setup
*/
var ads = new Array();
var accipiterAds = new Array();
var requestorIP = readCookie("requestorIP");
if(requestorIP == "")
{ 
    setRequestorIP(); 
}
