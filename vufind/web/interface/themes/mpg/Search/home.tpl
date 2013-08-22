<div class="searchHome">


    {* Beta-Streifen *}
    <div id="dbeta">
      <img src="{$path}/interface/themes/mpg/images/rdg/beta.png"/>
      {* Hinweis-Text direkt formatieren, sonst buggy mit jQuery/CSS-Ladereihenfolge *}
      <div id="dbetaText" style="text-align:right;display:none;position:absolute;top:-25px;right:5px;color:#fff"><a style="color:#fff" href="mailto:biblio@coll.mpg.de">{translate text="This is a beta version - Please give us feedback on things you miss or (do not) like!"}</a></div>
    </div>

    
    <b class="btop"><b></b></b>
    <div class="searchHomeContent">
      {*    <img src="{$path}/interface/themes/mpg/images/rdg/core_long.png" alt="MPI for Research on Collective Goods - Library" title="CORE - Collective Goods Research Explorer" width="470">
      *}
      {* Logo wird extern eingebunden - s. vufind-rdg.css #LogoExternHome *}
      <div id="LogoExternHome" title="CORE - Collective Goods Research &amp; Explore">
      </div>
      
      <div class="searchHomeForm">
	{include file="Search/searchbox.tpl"}
      </div>
    </div>
</div>

{* anstelle der Facetten selbst-definierte Links anzeigen *}

<div class="searchHomeBrowse">
  <div class="searchHomeBrowseInner homeFloatParent">
    <div class="homeFloatChild"><p><strong>{translate text='Search Options'}</strong></p>
      <ul>
	<li><a href="{$path}/Search/History">{translate text='Search History'}</a></li>
        <li><a href="{$path}/Search/Advanced">{translate text='Advanced Search'}</a></li>
	<li><a href="{$url}/Help/Home?topic=search" onClick="window.open('{$url}/Help/Home?topic=search', 'Help', 'width=625, height=510'); return false;">{translate text='Search Tips'}</a></li>
        <li><a href="#ytvideos">{translate text='Video Tutorials'}</a>&nbsp;&nbsp; </li>
      </ul>
    </div>
    <div class="homeFloatChild"><p><strong>{translate text='Browse'}</strong></p>
      <ul>
        <li><a href="{$path}/Classification/Classification">{translate text='Classification'}</a></li>
        {* <li><a href="{$path}/Browse/Home">{translate text='Collection'}</a></li> *}
        <li><a href="{$path}/AlphaBrowse/Home">{translate text='Alphabetically'}</a></li>
      </ul>
    </div>
    <div class="homeFloatChild"><p><strong>{translate text='Choose a List'}</strong></p>
      <a href="{$url}/Search/Results?lookfor=AwrdPbl&type=AllFields&submit=Find">Awarded Publications</br></a>
      <a href="{$url}/Search/Results?lookfor=ThReGrp&type=AllFields&submit=Find">Theory Reading Group</br></a>
      <a href="{$url}/Search/Results?lookfor=IntuitExp&type=AllFields&submit=Find">Intuitive Experts</br></a>
      <a href="#" id="toggle_ratio_lists">Ratio <span class="textarrow">&#x25BE;</span></a>
      <div id="ratio_lists" style="display:none">
      <a href="{$url}/Search/Results?lookfor=LitRatio2013&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2013.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatio-Trust&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-trust.png" class="tooltip" title="Ratio Literature: Trust"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatio-SocialPreferences&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-social.png" class="tooltip" title="Ratio Literature: Social Preferences"></img>
      </a>
{*      
      <a href="{$url}/Search/Results?lookfor=LitRatio-CooperationPunishment&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-coop.png" class="tooltip" title="Ratio Literature: Cooperation & Punishment"></img>
      </a>
*}
      <a href="{$url}/Search/Results?lookfor=LitRatio-Voting&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-voting.png" class="tooltip" title="Ratio Literature: Voting"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatio-Norms&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-norms.png" class="tooltip" title="Ratio Literature: Norms"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatioAllgemein&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-misc.png" class="tooltip" title="Ratio Literature: Miscellaneous"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatio2007&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2007.png" class="tooltip" title="Ratio Literature Seminar 2007"></img>
      </a>
      <a href="{$url}/Search/Results?lookfor=LitRatio2006&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2006.png" class="tooltip" title="Ratio Literature Seminar 2006"></img>
      </a>
      </div>
    </div>
  
  {*
    <div id="betafeedback">
      <a href="mailto:biblio@coll.mpg.de">{translate text="This is a beta version - Please give us feedback on things you miss or (do not) like!"}</a>
    </div>
    *}
    <br clear="all">
  </div>
</div>

{* Facetten vorerst nicht anzeigen = setze $facetList auf 0 *}
{assign var="facetList" value="0"}
{if $facetList}
  <div class="searchHomeBrowseHeader">
    {foreach from=$facetList item=details key=field}
      {* Special case: extra-wide header for call number facets: *}
      <div{if $field == "callnumber-first" || $field == "dewey-hundreds"} class="searchHomeBrowseExtraWide"{/if}>
        <h2>{translate text="home_browse"} {translate text=$details.label}</h2>
      </div>
    {/foreach}
    <br clear="all">
  </div>
  
  <div class="searchHomeBrowse">
    <div class="searchHomeBrowseInner">
      {foreach from=$facetList item=details key=field}
        {assign var=list value=$details.sortedList}
        {* Special case: single, extra-wide column for Dewey call numbers... *}
        <div{if $field == "dewey-hundreds"} class="searchHomeBrowseExtraWide"{/if}>
          <ul>
            {* Special case: two columns for LC call numbers... *}
            {if $field == "callnumber-first"}
              {foreach from=$list item=currentUrl key=value name="callLoop"}
                <li><a href="{$currentUrl|escape}">{$value|escape}</a></li>
                {if $smarty.foreach.callLoop.iteration == 17}
                  </ul>
                  </div>
                  <div>
                  <ul>
                {/if}
              {/foreach}
            {else}
              {assign var=break value=false}
              {foreach from=$list item=currentUrl key=value name="listLoop"}
                {if $smarty.foreach.listLoop.iteration > 12}
                  {if !$break}
                    <li><a href="{$path}/Search/Advanced"><strong>{translate text="More options"}...</strong></a></li>
                    {assign var=break value=true}
                  {/if}
                {else}
                  <li><a href="{$currentUrl|escape}">{$value|escape}</a></li>
                {/if}
              {/foreach}
            {/if}
          </ul>
        </div>
      {/foreach}
        <!-- allgemeine wichtige Links (nur mögl., wenn max. 3 Facetten in facets.ini definiert sind!) -->
        <div><a href="{$path}/Classification/Classification"><b>{translate text="Classification Search"}</b></a></div>
      <br clear="all">
    </div>
    <b class="gbot"><b></b></b>
  </div>
{/if}



<script language="JavaScript" type="text/javascript">
document.searchForm.lookfor.focus();
</script>

{if ($userLang == "de")}	
  <div id="ytvideos">
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/qrMG9GcA7s4?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/CPGZQtBjB6E?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/yBr_wn73MLY?rel=0" frameborder="0" allowfullscreen></iframe> 
    </div>
  </div>
{else}
  <div id="ytvideos">
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/8DYnMyK1-Mc?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/YuE_KY7gieo?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="ytvideo">
      <iframe width="640" height="360" src="//www.youtube-nocookie.com/embed/effmGhvnybk?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
  </div>
{/if}
