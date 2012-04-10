<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {foreach from=$mHtmlMetaTags item="tag"}
    <meta
    {foreach from=$tag item="value" key="property"}
        {$property}="{$value}"
    {/foreach}
    />
    {/foreach}

    <title>{$mSiteTitle} - Login</title>

    {foreach from=$mHtmlStylesheets item="href"}
    <link href="{$href}" rel="stylesheet" type="text/css" />
    {/foreach}

</head>

<body class="{$mShortSiteCode|lower}">

<div id="Shell">

    <!---  HEADER * HEADER * HEADER * HEADER * HEADER * HEADER * HEADER * --->

    <div id="Header">
        <div class="column1"><img src="/common/images/clear.gif" alt="{$mSiteTitle}" width="532" height="78" border="0"/></div>
        <div class="column2"> &nbsp; </div>
    </div>


    <!---  TOP NAV * TOP NAV * TOP NAV * TOP NAV * TOP NAV * TOP NAV * TOP NAV * --->

    <div id="topnav">
        <div class="navspace">&nbsp;</div>
    </div>

    <!---  BODY * BODY * BODY * BODY * BODY * BODY * BODY * BODY * BODY --->

    <div id="Main">
        <div class="maincontainer_2col">
            <div class="topshadow"> &nbsp;</div>

            <!--- LEFT COLUMN * LEFT COLUMN * LEFT COLUMN * LEFT COLUMN * LEFT COLUMN  --->

            <div id="Leftcolumn">
            {if $mShortSiteCode == 'DGN'} 
                <p>
                    Digital Government Navigator is much more than a collection of RFPs or news articles.
                    A membership to Navigator gives you access to a personalized, constantly updated industry specific tool.
                    It is easy to search, customize and comes with personalized customer service to answer any of your questions.
                    A few of your membership benefits include:
                </p>

                <ul>
                    <li> Custom delivered pre-RFPs, bids and awards to each user</li>
                    <li> Current contact data for decision makers</li>
                    <li> DealWatch to keep a close eye on your most important opportunities</li>
                    <li> Detailed budget data</li>
                    <li> Market Alerts on the latest developments</li>
                    <li> Grant database</li>
                    <li> CIO Interviews</li>
                </ul>

             {elseif $mShortSiteCode == 'DEN'} 

                <p>
                   The Digital Education Navigator is much more than a collection of RFPs or news articles.
                   A membership to Navigator gives you access to a personalized, constantly updated industry
                   specific tool focused on the K-12 and Higher Education market. It is easy to search, customize
                   and comes with personalized customer service to answer any of your questions. A few of
                   your membership benefits include:
                </p>

                <ul>
                    <li>Custom delivered pre-RFPs, bids and awards to each user</li>
                    <li>Education IT budgets/spending</li>
                    <li>Current contact data for decision makers</li>
                    <li>Market Alerts on the latest developments</li>
                    <li>Grant Information</li>
                    <li>Access to Market Briefings and Executive Interviews</li>
                </ul>

            {elseif $mShortSiteCode == 'EMN'} 
                <p>
                    The Emergency Management Navigator (EM Nav) provides online business development and sales information on the
	                emergency and public safety markets.  Included are bids, contacts, funding, news, all in one place.  A membership
	                gives personalized access to the information needed to expand sales in these markets.  It is easy to search,
	                customized and comes with customer service training and support. Included with access are:
	            </p>

	            <ul>
			      <li>Custom delivered pre-RFPs, bids and awards to each user</li>
			      <li>Current contact data for key organizations and decision makers</li>
			      <li>Budget and funding information</li>
			      <li>Grant information and grant database</li>
			      <li>Market overview and procurement information</li>
			      <li>Emergency Management events and news</li>
                </ul>

            {/if}
            </div><!-- left column -->

            <!--- CENTER COLUMN * CENTER COLUMN * CENTER COLUMN * CENTER COLUMN * CENTER COLUMN  --->

            <div id="Centercolumn">
            {if $CONCURRENT_USER}

                <div class="contentblock">
                    <h2 style="height:55px;">{$mSiteTitle} Login</h2>

                    <div id='msg' class="textindent" style="display: block; height: 60px; width:500px;">
                        <span style="color:red">
                            You are attempting to sign into Navigator from a 2nd location; <br/>
                            you must first terminate your previous session by clicking the button below
                        </span>
                    </div>

                    <form method="post">
                     {*   <input type='hidden' name='terminate_other_session' value="1"/> *}
                     {*   <input type='hidden' name='current_page' value="{$smarty.get.current_page}"/> *}
                        <input type='hidden' name="email"        value="{$email}"/>
                     {*   <input type='hidden' name="password"     value="{$password}"/> *}
                        <input type="hidden" name="remember_me"  value="{$remember_me}"/>
                        <input type="hidden" name="ep"           value="{$ep}"/>

                        <label class="loginlabel">&nbsp;</label>
                        <input type="submit" name="submit" value="Terminate other Session" class="loginbutton" />
                        <input type="submit" name="submit" value="Cancel" class="loginbutton" />
                    </form>

                    <ul class="login" style="display:block; width:500px;">
                        <li><a href="http://www.centerdigitalgov.com/industry/products/94">Learn more or apply for a trial account</a></li>
                        <li><a href="http://{$mNavHost}/support/request_logon/">Request a Log-on</a></li>
                    </ul>

                    <p>&nbsp;</p><p>&nbsp;</p>

                </div><!--contentblock  -->

            {else}

                <div class="contentblock">
                    <h2 style="height:55px;">{$mSiteTitle} Login</h2>

                    <div style="float:left;wdith:290px;">
                        <p style="color:red;">{$error_msg}</p>

                        <form method="post">
                            <input type='hidden' name='current_page' value="{$smarty.get.current_page} "/>
                            <label class="loginlabel">Email Address:</label>
                            <input name="email"  class="loginfield"  style="width:200px;" />
                            <br clear="all">

                            <label class="loginlabel">Password:</label>
                            <input name="password" type="password"  class="loginfield"  style="width:200px;"  />
                            <br clear="all">

                            <label class="loginlabel">Remember me:</label>
                            <input type="checkbox" name="remember_me" class="loginfield" />
                            <br clear="all">

                            <label class="loginlabel">&nbsp;</label>
                            <input type="submit" name="submit" value="Log In" class="loginbutton" />
                        </form>

                        <ul class="login" style="display:block; width:290px;">
                            {if strpos($smarty.server.HTTP_HOST, "navigatored") !== false}
                                <li><a href="http://forms.erepublic.com/forms/CDENavigatorTrial">Learn more or apply for a trial account</a></li>
                            {elseif strpos($smarty.server.HTTP_HOST, "navigatorgov") !== false}
                                <li><a href="http://www.centerdigitalgov.com/industry/products/94">Learn more or apply for a trial account</a></li>
                            {elseif strpos($smarty.server.HTTP_HOST, "navigatorem") !== false}
                                <li><a href="http://forms.erepublic.com/forms/EMNavigatorTrial">Learn more or apply for a trial account</a></li>
                            {/if}
                            <li><a href="http://{$mNavHost}/support/request_logon/">Request a Log-on</a></li>
                            <li><a href="http://{$mNavHost}/support/password_reset/">Forgot Password</a></li>
                        </ul>
                    </div>

                    {if stripos($smarty.server.HTTP_USER_AGENT, 'MSIE 6.0') !== false}
                    <div style="float:right; width:290px;">
                        <p class="details">The new sites currently support the following Internet browsers:</p>

                        <ul class="details" style="margin: 0 10px; list-style: disc;">
                            <li>Internet Explorer 7.0+</li>
                            <li>Internet Explorer 8.0+</li>
                            <li>Firefox 3.0+</li>
                        </ul>

                        <p class="details">
                            Using other browsers will have unexpected results.
                            You can download the latest Internet Explorer or
                            the latest Firefox by using the links here:
                        </p>

                        <a href="http://www.microsoft.com/windows/Internet-explorer/default.aspx">
                            <img src="{$mThemeDir}/common/images/downloadie_small.jpg" alt="Download Internet Explorer"/>
                        </a>
                        <a href="http://www.mozilla.com/en-US/firefox/upgrade.html">
                            <img  src="{$mThemeDir}/common/images/downloadfirefox_small.jpg" alt="Download Firefox"/>
                        </a>
                    </div>
                    {/if}

                    <br clear="all"></br>

                </div> <!--contentblock  -->
            {/if}
            </div> <!-- Centercolumn -->
        </div> <!-- MainContainer -->
    </div> <!-- Main -->

    <!--- FOOTER * FOOTER * FOOTER * FOOTER * FOOTER * FOOTER *  --->

    <div id="Footercontainer">
        <div class="footerlinks">
            <a href="http://www.centerdigitalgov.com/navigator/?pg=aboutdgn" class="supportlinks" target="_blank">About Us</a> |
            <a href="http://www.centerdigitalgov.com/privacy.php" target="_blank">Privacy</a>
        </div>

        <div class="footertext">
            <span class="welcomeuser">
                Copyright ©  1995-{$smarty.now|date_format:"%Y"} &nbsp; <a href="http://www.erepublic.com/" target="_blank">e.Republic, Inc.</a>  All rights reserved.
            </span>
        </div>
    </div>
</div>

{foreach from=$mHtmlJavaScripts item="src"}
<script type="text/javascript" src="{$src}"></script>
{/foreach}
</body>
</html>