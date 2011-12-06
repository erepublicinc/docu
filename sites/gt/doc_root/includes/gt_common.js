        function validateEmail(formId)
	{
	    var pattern = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	    var form = document.getElementById(formId);	
            for (var i = 0; i < form.length; i++)
	    {
                if (form.elements[i].id == 'C_EmailAddress')
		{
		    if (pattern.test(form.elements[i].value))
		    {
		        return true;
                    }
                    else
		    {
		        alert("A valid email address is required.");
			return false;
		    }
		}
	    }				
            return true;
	}
	
        function formSubmit(formId)
	{	 
	    var form = document.getElementById(formId);		
            if (validateEmail(formId))
	    {
	        form.submit();
	    }
	    return true;		
	 }
                
            var _search_clicked = false;                
            function checkSearch(field)
            {
                if (_search_clicked && '' == field.value.replace(/\s/g, ''))
                {
                    _search_clicked = false;
                    field.value = ' Search';
                }
                else if (!_search_clicked)
                {
                    _search_clicked = true;
                    field.value = '';
                }
            }

    function navImage(direction)
    {
        thumbs = $("#Imagesamples a img");
        thumbs.each(function(index) {
            if ($("#photo-image").attr("src") == $(this).attr('main'))
            {
                if (index == 0 && direction == 'prev')
                {
                    cur_index = thumbs.length - 1;
                }
                else if ((index + 1) == thumbs.length && direction == 'next')
                {
                    cur_index = 0;
                }
                else
                {
                    if (direction == "prev")
                    {
                        cur_index = index - 1;
                    }
                    else
                    {
                        cur_index = index + 1;
                    }
                }
            }
        });
        changeImage(thumbs[cur_index]);
    }

    function changeImage(new_image)
    {
        img_el = $(new_image);
        $("#photo-image").attr("src", img_el.attr("main"));
        $("#photo-title").text(img_el.attr("displayTitle"));
        if (img_el.attr("displayTitle") == img_el.attr("caption"))
        {
            $("#photo-description").html(" ");
        }
        else
        {
            $("#photo-description").html(img_el.attr("caption"));
        }
        $("#Imagesamples a").each(function(index) { $(this).attr("class", ""); });
        img_el.parent().attr("class", "current");

    }
    
    function processComment(frm)
{
    var MAX_COMMENT_LENGTH = 2000;
    
    if (document.comment.name.value == "" || document.comment.field.value == "")
    {
        window.alert("Complete all required fields");
    }
    else
    {
 	var comment = document.comment.field.value;
    	var commentLength = comment.length;
    	
    	if (commentLength > MAX_COMMENT_LENGTH)
    	{
    		alert("Sorry, your comment is too long. Please ensure your comment does not exceed 2000 characters. "
    			+ "Current number of characters: " + commentLength);
    	}
    	else
    	{
        	postForm(frm, true, function() { });
    		$("#comment-form-container").html("<div style='padding: 5px 5px 5px 10px;font-weight: bold;font-style: italic;'>Thank you for submitting your comment.</div>");
    	}
    }
    return false;
}

var form_defaults = [];
function addInputDefaultToggle(el)
{
    form_defaults[$(el).attr("id")] = $(el).val();
    $(el).focus(function() {
        $(this).val("");
        $(this).removeClass("color3");
    });

    $(el).blur(function() {
        if ($(this).val() == "")
        {
            $(this).val(form_defaults[this.id]);
            $(this).addClass("color3");
        }
    });
}

	
function check4Mobile()
{
    
    if(document.cookie.indexOf("NoMobileSite") >= 0 )
       return;
   
    var br = getBrowserType();
    if(br == "Android" || br == "IEMobile" || br == "iPhone" || br == "BlackBerry")
    {
        if(window.location.href.indexOf('govtech.com') > 0 )
        {
           var newLocation = "http://m.govtech.com";
        }
        else if((window.location.href.indexOf('digitalcommunities.com') > 0 ))
        {
             //var newLocation = "http://m.digitalcommunities.com";
             return;  // temporary
        }
        else if((window.location.href.indexOf('governing.com') > 0 ))
        {
             var newLocation = "http://m.governing.com";
             //return;  // temporary
        }
        else
        {
             return;
        }
        
	if( confirm("Like to use our mobile site?\r\n (ok = yes, cancel = no) "))	
	{
	  window.location = newLocation;
	}
	else
	{
	   //alert("declined");
	   var date = new Date();
	   date.setTime(date.getTime()+(30*24*60*60*1000)); // cookie will last a month
	   var expires = "; expires="+date.toGMTString();
	   document.cookie = "NoMobileSite=true" + expires + "; path=/";
	} 
    }
}

function subscribeFormSurvey(dest)
{
	if (dest.indexOf('http') < 0 && dest.indexOf('mailto') < 0)
	{
		dest = 'http://www.govtech.com' + dest;
	}
	
	if (window.location.hash == "#submitted")
	{
		window.location.href = dest;
		return;
	}
	
	interstitialBox.standardbody = document.body;
	$('#global-container').prepend('<div id="interVeil"></div>');
	$('#global-container').prepend('<div id="interContainer" style="position:fixed;width:300px;top:50px;">&nbsp;</div>');
	$('#interContainer').append('<div id="interContent"></div>');
	$('#interContent').prepend('<iframe src="http://forms.erepublic.com/forms/GT-SubscribeAbandonSurvey?dest=' + dest + '" height="400" width="296" scrolling="no">');
	interstitialBox.interContainer = document.getElementById('interContainer');
	interstitialBox.interVeil = document.getElementById('interVeil');
	interstitialBox.autohidetimer = 30000; 
	interstitialBox.showcontainer();
	interstitialBox.interContainer.style.top = '50px';
	window.location.hash = "#submitted"
	return;
}
