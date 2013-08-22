<div id="record{$summId|escape}" class="yui-ge">
  <div class="yui-u first">
    {if $summThumb}
    <img src="{$summThumb|escape}" class="alignleft" alt="{translate text='Cover Image'}"/>
    {else}
    <img src="{$path}/bookcover.php" class="alignleft" alt="{translate text='No Cover Image'}"/>
    {/if}
    <div class="resultitem">
      <div class="resultItemLine1">
      {if !empty($MPGtitle) || !empty($f9842a)}
      <a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if $f9843a}{$f9843a|escape}{if $f9843b}: {$f9843b|escape}{/if}. {/if}{if $f9842n}{$f9842n}: {/if}{if $f9842a}{$f9842a|escape}{if $f9842b}: {$f9842b|escape}{/if}. {/if}{if $MPGtitle}{$MPGtitle|escape}{/if}</a>
      {else}
      <a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|replace:";":": "|escape}{/if}</a>
      {/if}
      </div>
      
      <div class="resultItemLine2">
      {if !empty($summAuthor)}
      {translate text='by'}
      <a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
      {/if}
      {if $summGBVSource}{$summGBVSource}
      {else if $summDate}{translate text='Published'} {$summDate.0|escape}
      {/if}
      </div>

      <div class="resultItemLine3">
      {if !empty($summSnippetCaption)}<b>{translate text=$summSnippetCaption}:</b>{/if}
      {if !empty($summSnippet)}<span class="quotestart">&#8220;</span>...{$summSnippet|highlight}...<span class="quoteend">&#8221;</span><br>{/if}
      {if $summAjaxStatus}
      <b>{translate text='Call Number'}:</b> <span id="callnumber{$summId|escape}">{translate text='Loading'}</span><br>
      <b>{translate text='Located'}:</b> <span id="location{$summId|escape}">{translate text='Loading'}</span>
      {elseif !empty($summCallNo)}
      <b>{translate text='Call Number'}:</b> {$summCallNo|escape}
      {/if}
      </div>
      
      <div class="resultItemLine4">
      {if $summAjaxStatus}
        <div class="status" id="status{$summId|escape}">
          <span class="unknown" style="font-size: 8pt;">{translate text='Loading'}...</span>
        </div>
      {/if}

      {if !empty($MPGADAM)}
        {foreach from=$MPGADAM item=adam}
          <span class="iconlabel {$adam.label|lower}"><a href="{$adam.url|escape}" target="new" class="{$adam.label|lower}MPG">{translate text=$adam.label}</a></span>
        {/foreach}
        <!-- ADAM = Inhaltsverzeichnis = true = filtere MAB 655 aus, falls vorhanden + gib Rest aus -->
        <!-- nothing more to do, for other links see full view (do not show full text links here) -->
        <!--
        {if !empty($summURLs)}
          {foreach from=$summURLs key=recordurl item=urldesc}
            {if !strstr($urldesc, "Inhaltsverzeichnis") and !strstr($urldesc, "Table")}
            <br><a href="{if $proxy}{$proxy}/login?qurl={if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape:"url"}{else}{if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape}{/if}" class="toc" target="new">{if $recordurl == $urldesc}{translate text='Get full text'}{else}{$urldesc|escape}{/if}</a>
            {/if}
          {/foreach}
        {/if}
        -->
      {else}
      <!-- ADAM = Inhaltsverzeichnis = false = gebe alles aus MAB 655 aus, aber keine Volltextlinks! -->
        {if !empty($summURLs)}
          {foreach from=$summURLs key=recordurl item=urldesc}
            {if stristr($urldesc, "Full") or stristr($urldesc, "Voll") or stristr($urldesc, "Lizenz") or stristr($urldesc, "online")}
             <!-- nothing to do, for full text see full view (do not show full text links here) -->
            {else}
            <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape:"url"}{else}{if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape}{/if}" class="toc" target="new">{if $recordurl == $urldesc}{translate text='Get full text'}{else}{$urldesc|escape}{/if}</a>
            {/if}
          {/foreach}
        {/if}
      {/if}
        <div style="display: none;" id="locationDetails{$summId|escape}">&nbsp;</div>
      </div>
      {foreach from=$summFormats item=format}
        <div class="resultsFormat"><span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span></div>
      {/foreach}
    </div>
  </div>

  <div class="yui-u">
        <div id="saveLink{$summId|escape}">
      {* RDG: check Login! *}
     {if $user} 
      <a href="{$url}/Record/{$summId|escape:"url"}/Save" onClick="getLightbox('Record', 'Save', '{$summId|escape}', '', '{translate text='Add to favorites'}', 'Record', 'Save', '{$summId|escape}'); return false;" class="fav tool">{translate text='Add to favorites'}</a>
        {else}
      <span class="notloggedin tooltip fav" title="{translate text='Please login first to use this function!'}">{translate text='Add to favorites'}</span>
        {/if}
      <ul id="lists{$summId|escape}"></ul>
      <script language="JavaScript" type="text/javascript">
        getSaveStatuses('{$summId|escape:"javascript"}');
      </script>
    </div>
      {if $summOpenUrl || !empty($summURLs)}
        {if $summOpenUrl}
         <div class="previewDiv">
          {include file="Search/openurl.tpl" openUrl=$summOpenUrl}
         </div>
        {/if}
      {/if}

    {if $abrufzeichen}
        {include file="RecordDrivers/Index/abrufzeichen.tpl"}
    {/if}

    {if $showPreviews}
      {if (!empty($summLCCN)|!empty($summISBN)|!empty($summOCLC))}
        {if $showGBSPreviews}      
          <div class="previewDiv"> 
            <a class="{if $summISBN}gbsISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}gbsLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN|$summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}gbsOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" border="0" style="width: 70px; margin: 0; padding-bottom:5px;"/>
            </a>    
          </div>
        {/if}
        {if $showOLPreviews}
          <div class="previewDiv">
            <a class="{if $summISBN}olISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}olLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN|$summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}olOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="{$path}/images/preview_ol.gif" border="0" style="width: 70px; margin: 0"/>
            </a>
          </div> 
        {/if}
        {if $showHTPreviews}
          <div class="previewDiv">
            <a id="HT{$summId|escape}" style="display:none"  target="_blank">
              <img src="{$path}/images/preview_ht.gif" border="0" style="width: 70px; margin: 0" title="{translate text='View online: Full view Book Preview from the Hathi Trust'}"/>
            </a>
          </div> 
        {/if}
      {/if}
     {/if}
     <!-- Volltext? --> 
<!--     
     {if !empty($summURLs)}
       {foreach from=$summURLs key=recordurl item=urldesc}
          {if stristr($urldesc, "Full") or stristr($urldesc, "Voll") or stristr($urldesc, "Lizenz") or stristr($urldesc, "online")}
             <div class="showFulltextLinkResult">
                <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape:"url"}{else}{if !strstr($recordurl, "http")}http://{/if}{$recordurl|escape}{/if}">{translate text="Get full text"}</a>
             </div> 
          {/if}
       {/foreach}
     {/if}
-->
  </div>
</div>

{if $summCOinS}<span class="Z3988" title="{$summCOinS|escape}"></span>{/if}

{if $summAjaxStatus}
<script type="text/javascript">
  getStatuses('{$summId|escape:"javascript"}');
</script>
{/if}
{if $showPreviews}
<script type="text/javascript">
  {if $summISBN}getExtIds('ISBN{$summISBN|escape:"javascript"}');{/if}
  {if $summLCCN}getExtIds('LCCN{$summLCCN|escape:"javascript"}');{/if}
  {if $summOCLC}{foreach from=$summOCLC item=OCLC}getExtIds('OCLC{$OCLC|escape:"javascript"}');{/foreach}{/if}
  {if (!empty($summLCCN)|!empty($summISBN)|!empty($summOCLC))}
    getHTIds('id:HT{$summId|escape:"javascript"};{if $summISBN}isbn:{$summISBN|escape:"javascript"}{/if}{if $summLCCN}{if $summISBN};{/if}lccn:{$summLCCN|escape:"javascript"}{/if}{if $summOCLC}{if $summISBN || $summLCCN};{/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}oclc:{$OCLC|escape:"javascript"}{if !$smarty.foreach.oclcLoop.last};{/if}{/foreach}{/if}')
  {/if}
</script>
{/if}
