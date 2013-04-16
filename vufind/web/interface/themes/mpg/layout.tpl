<!DOCTYPE html>

{* We should hide the top search bar and breadcrumbs in some contexts: *}
{if ($module=="Search" || $module=="Summon" || $module=="WorldCat" || $module=="Authority") && $pageTemplate=="home.tpl"}
    {assign var="showTopSearchBox" value=0}
    {assign var="showBreadcrumbs" value=0}
{else}
    {assign var="showTopSearchBox" value=1}
    {assign var="showBreadcrumbs" value=1}
{/if}

  <head>
    <title>CORE - {$pageTitle|truncate:64:"..."}</title>
    {if $addHeader}{$addHeader}{/if}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <!-- HTML-5-Canvas (Jahrslider in Kurzanzeige) funktioniert nicht in IE9, erzwinge IE=8 -->
    <!--[if lte IE 8]>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <![endif]-->
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    {css media="screen" filename="styles.css"}
    {css media="print" filename="print.css"}
    <link rel="stylesheet" type="text/css" media="screen" href="http://www.coll.mpg.de/bib/vufind/vufind-rdg.css" />
    <link rel="stylesheet" type="text/css" media="print" href="http://www.coll.mpg.de/bib/vufind/vufind-rdg.print.css" />
    {css media="screen" filename="rdg-extra.css"}
    {if $module=='Classification'}
    {css media="screen" filename="rdg-classification.css"}
    {/if}
    {if $module=='Classification' || $module=='Record'}
    {css media="screen" filename="jquery-ui-1.8.12.custom.css"}
    {/if}
    {* RDG: jquery qtips *}
    {css media="screen" filename="jquery.qtip.min.css"}
    <script language="JavaScript" type="text/javascript">
      path = '{$url}';
    </script>

    {js filename="jquery-1.7.2.min.js"}
    {js filename="jquery-ui.min.js"} 
    {js filename="yui/yahoo-dom-event.js"}
    {js filename="yui/connection-min.js"}
    {js filename="yui/datasource-min.js"}
    {js filename="yui/autocomplete-min.js"}
    {js filename="yui/dragdrop-min.js"}
    {js filename="scripts.js"}
    {js filename="rc4.js"}
    {js filename="ajax.yui.js"}
    {* RDG: jquery qtips *}
    {js filename="jquery.qtip.min.js"}
    {js filename="jquery.qtip.funcs.js"}
    <!-- altmetrics.com mashup -->
    <script type='text/javascript' src='https://d1bxh8uas1mnw7.cloudfront.net/assets/embed.js'></script>
  </head>

  <body>
    {* LightBox *}
    <div id="lightboxLoading" style="display: none;">{translate text="Loading"}...</div>
    <div id="lightboxError" style="display: none;">{translate text="lightbox_error"}</div>
    <div id="lightbox" onClick="hideLightbox(); return false;"></div>
    <div id="popupbox" class="popupBox"><b class="btop"><b></b></b></div>
    {* End LightBox *}
    
    <div class="searchheader">
      <div class="searchcontent">
        <div class="alignright" style="text-align:right;">
          <div id="logoutOptions"{if !$user} style="display: none;"{/if}>
            <a href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
            <a href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a> |
            <a href="http://aleph.mpg.de/F?func=self-check-0&local_base=rdgvf{if $userLang == "en"}&con_lng=eng{/if}">{translate text="Selbstverbuchung"}</a>
          </div>
          <div id="loginOptions"{if $user} style="display: none;"{/if}>
            {if $authMethod == 'Shibboleth'}
              <a href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
            {else}
              <a href="https://intern.coll.mpg.de/biblio/vufind/login.php">{translate text="Login"}</a> |
              <a href="http://aleph.mpg.de/F?func=self-check-0&local_base=rdgvf{if $userLang == "en"}&con_lng=eng{/if}">{translate text="Selbstverbuchung"}</a>
            {/if}
          </div>
          <form method="get" name="mobileForm" action="" style="float:left">
            <select id="setMobile" name="ui" onChange="document.mobileForm.submit();">
              <option value="standard" selected>{translate text="Standard"}</option>
              <option value="mobile">{translate text="Mobil"}</option>
            </select>
          </form>
          {if is_array($allLangs) && count($allLangs) > 1}
            <form method="post" name="langForm" action="" style="float:right">
              <div class="hiddenLabel"><label for="mylang">{translate text="Language"}:</label></div>
              <select id="mylang" name="mylang" onChange="document.langForm.submit();">
                {foreach from=$allLangs key=langCode item=langName}
                  <option value="{$langCode}"{if $userLang == $langCode} selected{/if}>{translate text=$langName}</option>
                {/foreach}
              </select>
              <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
            </form>
          {/if}
        </div>

        {if $showTopSearchBox}
{*
          <a href="{$url}"><img src="{$path}/interface/themes/mpg/images/rdg/core_short.png" alt="MPI for Research on Collective Goods - Library" title="CORE - Collective Goods Research Explorer" class="alignleft" width="107"></a>
*}
{* Logo wird extern eingebunden - s. vufind-rdg.css #LogoExternLayout *}
	    <a href="{$url}">
	    <div id="LogoExternLayout" style="float:left;" title="CORE - Collective Goods Research &amp; Explore">
	    </div>
	    </a>
	    <div>
          {if $pageTemplate != 'advanced.tpl'}
            {if $module=="Summon" || $module=="WorldCat" || $module=="Authority"}
              {include file="`$module`/searchbox.tpl"}
            {else}
              {include file="Search/searchbox.tpl"}
            {/if}
          {/if}
	    </div>
        {/if}

        <br clear="all">
      </div>
    </div>
    
    {if $showBreadcrumbs}
    <div class="breadcrumbs">
      <div class="breadcrumbinner">
        <a href="{$url}">{translate text="Home"}</a> <span>&gt;</span>
        {include file="$module/breadcrumbs.tpl"}
        <a href="{$url}/Search/EmailLib" onClick="getLightbox('Search', 'EmailLib', null, null, '{translate text="Email"}'); return false;" style="float:right">{translate text="EmailLib"}</a>
        <a href="{$url}/Search/History" style="float:right">{translate text="Search History"}</a>
      </div>
    </div>
    {/if}
    
    <div id="doc2" class="yui-t4"> {* Change id for page width, class for menu layout. *}

      {if $useSolr || $useWorldcat || $useSummon}
      <div id="toptab">
        <ul>
          {if $useSolr}
          <li{if $module != "WorldCat" && $module != "Summon"} class="active"{/if}><a href="{$url}/Search/Results?lookfor={$lookfor|escape:"url"}">{translate text="University Library"}</a></li>
          {/if}
          {if $useWorldcat}
          <li{if $module == "WorldCat"} class="active"{/if}><a href="{$url}/WorldCat/Search?lookfor={$lookfor|escape:"url"}">{translate text="Other Libraries"}</a></li>
          {/if}
          {if $useSummon}
          <li{if $module == "Summon"} class="active"{/if}><a href="{$url}/Summon/Search?lookfor={$lookfor|escape:"url"}">{translate text="Journal Articles"}</a></li>
          {/if}
        </ul>
      </div>
      <div style="clear: left;"></div>
      {/if}

      {include file="$module/$pageTemplate"}

      <div id="ft">
      {include file="footer.tpl"}
      </div> {* End ft *}

    </div> {* End doc *}

    {* Feedback-Lasche *}
    <!--[if lte IE 8]>
    <div id="clipbox1-ie" class="clipbox-ie">
    <![ENDIF]-->
    <div id="clipbox1" class="clipbox ui-widget">
      <a title="Ask us!" href="mailto:biblio@coll.mpg.de?subject=VuFind Feedback">
	<div class="clipboxinner"><!--[IF lte IE 8]>&nbsp;<![ENDIF]--></div>
      </a>
    </div>
    <!--[IF lte IE 8]>
    </div>
    <![ENDIF]-->
    {* Literaturagenten-Lasche *}
    <!--[if lte IE 8]>
    <div id="clipbox2-ie" class="clipbox-ie">
    <![ENDIF]-->
    <div id="clipbox2" class="clipbox ui-widget">
      <a title="Document Delivery" href="http://intern.coll.mpg.de/library/page/service-ordering-literature-agents">
	<div class="clipboxinner"><!--[IF lte IE 8]>&nbsp;<![ENDIF]--></div>
      </a>
    </div>
    <!--[IF lte IE 8]>
    </div>
    <![ENDIF]-->
    {* EZB-Lasche *}
    <!--[if lte IE 8]>
    <div id="clipbox3-ie" class="clipbox-ie">
    <![ENDIF]-->
    <div id="clipbox3" class="clipbox ui-widget">
      <a title="Electronic Journals" href="http://ezb.uni-regensburg.de/fl.phtml?bibid=MPPRG">
	<div class="clipboxinner"><!--[IF lte IE 8]>&nbsp;<![ENDIF]--></div>
      </a>
    </div>
    <!--[IF lte IE 8]>
    </div>
    <![ENDIF]-->

  </body>
</html>
