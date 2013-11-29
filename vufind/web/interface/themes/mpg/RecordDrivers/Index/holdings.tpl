{* begin SFX response injection: show order button if no fulltext links are found (only if $isGBVRecord) *}
{* switch on in record-rdg-extra.js *} 
{if $isGBVRecord}
      <div id="orderBoxLiteraturagent" class="othercontent" style="display:none">
        {translate text="It seems that there is no fulltext available at the moment"}.<br/><br/>
        {translate text="Are you interested in this title"}? 
        {translate text="Our literature agents will get this article for you"}!
        <div class="magicbuttonbox">	
          <a class="magicbutton" href="http://intern.coll.mpg.de/node/3159/?{$coreOpenURL}&from=CORE">
           {translate text="order here"} ({translate text="internal only"})
	  </a>
        </div>
      </div>
{/if}
{* end SFX response injection *}

{if $driverMode && !empty($holdings)}
  {if $showLoginMsg}
    <div class="userMsg">
      <a href="{$path}/MyResearch/Home?followup=true&followupModule=Record&followupAction={$id}">{translate text="Login"}</a> {translate text="hold_login"}
    </div>
  {/if}
  {if $user && !$user->cat_username}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
{/if}

{if !empty($holdingURLs) || $holdingsOpenURL || !empty($MPGADAM)}
  <h3>{translate text="Internet"}</h3>
  {if !empty($MPGADAM)}
    {foreach from=$MPGADAM item=adam}
       <div class="showTocLinkRecord">
         <a href="{$adam.url|escape}" target="new">{translate text=$adam.label}</a><br/>
       </div>
    {/foreach}
    <!-- ADAM = Inhaltsverzeichnis = true = filtere MAB 655 aus, falls vorhanden + gib Rest aus -->
        {if !empty($holdingURLs)}
          {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
            {if !strstr($desc, "Inhaltsverzeichnis") and !strstr($desc, "Table")}            
              <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
                <!-- Volltext? -->
                {if stristr($desc, "Full") or stristr($desc, "Voll") or stristr($desc, "Lizenz") or stristr($desc, "online")}
                  <div class="showFulltextLinkRecord">
                     <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text="Get full text"}</a><br/>
                  </div>
                  <div class="showFulltextLinkRecordNote">
                     <span>{translate text="FulltextNote"}!</span>
                  </div> 
                {/if}
            {/if}
          {/foreach}
        {/if}
  {else}
  <!-- ADAM = Inhaltsverzeichnis = false = gebe alles aus MAB 655 aus -->
    {if !empty($holdingURLs)}
      {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
        <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
                <!-- Volltext? -->
	        {if stristr($desc, "Full") or stristr($desc, "Voll") or stristr($desc, "Lizenz") or stristr($desc, "online")}
                  <div class="showFulltextLinkRecord">
                     <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text="Get full text"}</a><br/>
                  </div> 
                  <div class="showFulltextLinkRecordNote">
                     <span>{translate text="FulltextNote"}!</span>
                  </div>
                {* Extrabutton, falls ToC: *}
                {elseif strstr($desc, "Inhaltsverzeichnis") or strstr($desc, "Table")}
		  <div class="showTocLinkRecord">
                     <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text="toc"}</a><br/>
                  </div>
                {/if}
      {/foreach}
    {/if}
  {/if}
  {if $holdingsOpenURL}
    {include file="Search/openurl.tpl" openUrl=$holdingsOpenURL}
  {/if}
{/if}

{* Hinweis bei downLinks wenn Zeitschrift *}
{if !empty($MPGDownLink)}
  {if !empty($recordFormat)}
    {if ($recordFormat.0 == 'Journal')}
      <span style="color:red;font-weight:bold">
      {translate text='Please note: this overview lists only current issues'}. 
      <a href="{$url}/Record/{$id|escape:'url'}/DownLinkRecords#tabnav">
      {translate text='Please check here for full holdings'}!
      </a>
      </span>
    {/if}
  {/if}
{/if}

{if (!empty($holdingLCCN)||!empty($isbn)||!empty($holdingArrOCLC))}
  <span style="">
    <a class="{if $isbn}gbsISBN{$isbn}{/if}{if $holdingLCCN}{if $isbn} {/if}gbsLCCN{$holdingLCCN}{/if}{if $holdingArrOCLC}{if $isbn|$holdingLCCN} {/if}{foreach from=$holdingArrOCLC item=holdingOCLC name=oclcLoop}gbsOCLC{$holdingOCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank"><img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" border="0" style="width: 70px; margin: 0;"/></a>    
    <a class="{if $isbn}olISBN{$isbn}{/if}{if $holdingLCCN}{if $isbn} {/if}olLCCN{$holdingLCCN}{/if}{if $holdingArrOCLC}{if $isbn|$holdingLCCN} {/if}{foreach from=$holdingArrOCLC item=holdingOCLC name=oclcLoop}olOCLC{$holdingOCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank"><img src="{$path}/images/preview_ol.gif" border="0" style="width: 70px; margin: 0"/></a> 
    <a id="HT{$id|escape}" style="display:none"  target="_blank"><img src="{$path}/images/preview_ht.gif" border="0" style="width: 70px; margin: 0" title="{translate text='View online: Full view Book Preview from the Hathi Trust'}"/></a>
  </span>
{/if}
{foreach from=$holdings item=holding key=location}
<h3>{translate text=$location}</h3>
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Holdings details from'} {translate text=$location}">
  {if $holding.0.summary}
  <tr>
    <th>{translate text="Volume Holdings"}: </th>
    <td>
      {foreach from=$holding.0.summary item=summary}
      {$summary|escape}<br>
      {/foreach}
    </td>
    <td>&nbsp;</td>
  </tr>
  {/if}
  {if $holding.0.notes}
  <tr>
    <th>{translate text="Notes"}: </th>
    <td>
      {foreach from=$holding.0.notes item=data}
      {$data|escape}<br>
      {/foreach}
    </td>
    <td>&nbsp;</td>
  </tr>
  {/if}
  {foreach from=$holding item=row}
    {if $row.barcode != ""}
      {* RDG: maphilight *}
      {if $row.callnumber != "" and $row.callnumber != "E-Book" and $row.availability and $location == "Library"}
        {js filename="jquery.maphilight.js"}
        {assign var="alephBarcode" value="1"}
      {/if}
  <tr>
    <th id="getLocation">{$row.callnumber}</th>
    <td>
      {if $row.reserve == "Y"}
      {translate text="On Reserve - Ask at Circulation Desk"}
      {else}
        {if $row.availability}
        {* Begin Available Items (Holds) *}
          <div>
          <span class="available">{translate text="Available"}</span>
          {if $row.link}
            <a class="holdPlace" href="{$row.link|escape}"><span>{translate text="Place a Hold"}</span></a>
          {/if}
          </div>
        {else}
        {* Begin Unavailable Items (Recalls) *}
          <div>
          <span class="checkedout">{translate text=$row.status}</span>
	  {* checkAJAX: called by JS function getALEPHBorrower() -- see record-rdg-extra.js *}
          {if $row.status == "Not available" && !strstr($row.duedate, "Expected")}
	    &nbsp;<a class="checkAJAX" title="{$id}" style="display:none">{translate text="Who borrowed this item"}?</a>
            {if $row.returnDate} <span class="statusExtra">{$row.returnDate|escape}</span>{/if}
            {if $row.requests_placed > 0}
              <span>{translate text="Requests"}: {$row.requests_placed|escape}</span>
            {/if}
          {/if}
          {if $row.link}
            <a class="holdPlace" href="{$row.link|escape}"><span>{translate text="Recall This"}</span></a>
          {/if}
          </div>
        {/if}
      {/if}
    </td>
    <td>
       {if $row.description}
         {$row.description}
       {else}
         &nbsp;
       {/if}
    </td>
  </tr>
    {/if}
  {/foreach}
</table>
{/foreach}
{if $alephBarcode} 
{include file="RecordDrivers/Index/maphilight.tpl"}
{/if}
{if $history}
<h3>{translate text="Most Recent Received Issues"}</h3>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue|escape}</li>
  {/foreach}
</ul>
{/if}
