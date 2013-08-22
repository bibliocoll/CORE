{* Display Title *}
{literal}
  <script language="JavaScript" type="text/javascript">
    // <!-- avoid HTML validation errors by including everything in a comment.
    function subjectHighlightOn(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.addClass(o, "hoverLink");
            }
        }
    }

    function subjectHighlightOff(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.removeClass(o, "hoverLink");
            }
        }
    }
    // -->
  </script>
{/literal}
{* Display Cover Image *}

  {if $coreThumbMedium}
    <div class="alignright">
      {if $coreThumbLarge}<a href="{$coreThumbLarge|escape}">{/if}
        <img alt="{translate text='Cover Image'}" class="recordcover" src="{$coreThumbMedium|escape}">
      {if $coreThumbLarge}</a>{/if}
    </div>
  {else}
{* <img src="{$path}/bookcover.php" alt="{translate text='No Cover Image'}"> *}
  {/if}

{* End Cover Image *}


  {if $abrufzeichen}
      {include file="RecordDrivers/Index/abrufzeichen.tpl"}
  {/if}


{* Display Title *}
<h1>{if $f9843a}{$f9843a|escape}{/if} {if $f9843b}: {$f9843b|escape}{/if}{if $f9843c}<br>{$f9843c|escape}{/if}<br>
{if $f9842n}{$f9842n|escape}. {/if}{if $f9842a}{$f9842a|escape}{/if} {if $f9842b}: {$f9842b|escape}{/if}{if $f9842c}<br>{$f9842c|escape}{/if}<br>
{if $MPGtitle}{$MPGtitle|regex_replace:"/\/$/"|escape}
{else}
{$coreShortTitle|replace:";":": "|escape} {* 'replace' really should happen in the data, not here ... *}
{*{if $coreSubtitle}: {$coreSubtitle|escape}{/if}*}
{if $coreTitleSection}{$coreTitleSection|escape}{/if}
{/if}
{* {if $coreTitleStatement}{$coreTitleStatement|escape}{/if} *}
</h1>
{* End Title *}
{if $coreSummary}
 <p class="summaryBox">{$coreSummary|truncate:300:"":true|escape|replace:"§":"<br/>"}<span id="coreSummaryDots">...</span><span id="coreSummaryMoreText" style="display:none">{$coreSummary|substr:300:1000|replace:"§":"<br/>"}</span>
  <a id="coreSummaryMoreShow" class="coreSummaryToggle" href="#">{translate text='Full description'}</a>
  <a id="coreSummaryMoreHide" class="coreSummaryToggle" href="#" style="display:none">{translate text='Hide description'}</a>
 </p>
{/if}

{* Display Main Details *}
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}">

  {* Display holdings section if at least one series exists. *}
  {if !empty($MPGHoldings)}
  <tr valign="top">
    <th>{translate text='Library Holdings'}: </th>
    <td>
      {foreach from=$MPGHoldings item=field name=loop}
        {$field}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($MPGParallel)}
  <tr align="top">
     <th>{translate text='Parallel edition'}: </th>
     <td>
        {foreach from=$MPGParallel item=field name=loop}
           {if $field.w}<a href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}+OR+id:{$field.w}+OR+ctrlnum:\(DE-599\)ZDB{$field.w}">{$field.i|escape} {$field.a|escape}</a>{else}{$field.i|escape} {$field.a|escape}{/if}<br>
        {/foreach}
     </td>
  </tr>
  {/if}

  {if !empty($MPGNewer)}
  <tr align="top">
     <th>{translate text='New Title'}: </th>
     <td>
        {foreach from=$MPGNewer item=field name=loop}
           {if $field.w}<a href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}+OR+id:{$field.w}+OR+ctrlnum:\(DE-599\)ZDB{$field.w}">{$field.a|escape}</a>{else}{$field.a|escape}{/if}<br>
        {/foreach}
     </td>
  </tr>
  {elseif !empty($coreNextTitles)}
  <tr valign="top">
    <th>{translate text='New Title'}: </th>
    <td>
      {foreach from=$coreNextTitles item=field name=loop}
        <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($MPGPrevious)}
  <tr align="top">
     <th>{translate text='Previous Title'}: </th>
     <td>
        {foreach from=$MPGPrevious item=field name=loop}
           {if $field.w}<a href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}+OR+id:{$field.w}+OR+ctrlnum:\(DE-599\)ZDB{$field.w}">{$field.a|escape}</a>{else}{$field.a|escape}{/if}<br>
        {/foreach}
     </td>
  </tr>
  {elseif !empty($corePrevTitles)}
  <tr valign="top">
    <th>{translate text='Previous Title'}: </th>
    <td>
      {foreach from=$corePrevTitles item=field name=loop}
        <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreMainAuthor)}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a href="{$url}/Author/Home?author={$coreMainAuthor|regex_replace:"/\[.*\]/"|escape:"url"}">{$coreMainAuthor|escape}</a></td>
  </tr>
  {elseif !empty($coreTopMainAuthor)}
  {assign var='firstTopShown' value='true'}
  <tr valign="top">
    <th>{translate text='Main Author (top)'}: </th>
    <td><a href="{$url}/Author/Home?author={$coreTopMainAuthor|regex_replace:"/\[.*\]/"|escape:"url"}">{$coreTopMainAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($coreCorporateAuthor)}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a href="{$url}/Author/Home?author={$coreCorporateAuthor|escape:"url"}">{$coreCorporateAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($MPGSecondaryCorps)}
  <tr valign="top">
    <th>{translate text='Additional Corporate Bodies'}: </th>
    <td>
      {foreach from=$MPGSecondaryCorps item=field name=loop}
        <a href="{$url}/Author/Home?author={$field|regex_replace:"/\[.*\]/"|escape:"url"}">{$field|escape}</a>{if !$smarty.foreach.loop.last}; {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreContributors)}
  <tr valign="top">
    <th>{translate text='Other Authors'}: </th>
    <td>
      {foreach from=$coreContributors item=field name=loop}
        <a href="{$url}/Author/Home?author={$field|regex_replace:"/\[.*\]/"|escape:"url"}">{$field|escape}</a>{if !$smarty.foreach.loop.last}; {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($MPGTopAuthorsAll)}
  {if $firstTopShown=='true' && !empty($MPGsecondaryTopAuthors)}
  <tr valign="top">
    <th>{translate text='Other Authors (top)'}: </th>
    <td>
      {if $firstTopShown=='true'}
      {foreach from=$MPGsecondaryTopAuthors item=field name=loop}
        <a href="{$url}/Author/Home?author={$field|regex_replace:"/\[.*\]/"|escape:"url"}">{$field|escape}</a>{if !$smarty.foreach.loop.last}; {/if}
      {/foreach}
    </td>
  </tr>
  {else}
  <tr valign="top">
    <th>{translate text='Other Authors (top)'}: </th>
    <td>
      {foreach from=$MPGTopAuthorsAll item=field name=loop}
        <a href="{$url}/Author/Home?author={$field|regex_replace:"/\[.*\]/"|escape:"url"}">{$field|escape}</a>{if !$smarty.foreach.loop.last}; {/if}
      {/foreach}
      {/if}
    </td>
  </tr>
  {/if}
  {/if}

  {if !empty($coreGBVSource)}
  <tr valign="top">
    <th>{translate text='Source'}: </th>
    <td>
      {if !empty($coreGBVJournalLink)}<a href="{$url}/Search/Results?lookfor=aleph_id:{$coreGBVJournalLink.0}+OR+id:{$coreGBVJournalLink.0}">{/if}{translate text=$coreGBVSource}{if !empty($coreGBVJournalLink)}</a>{/if}
      {if !empty($coreGBVJournalLink)}<br/><a id="MoreArticles" href="{$url}/Search/Results?lookfor=ppnlink:{$coreGBVJournalLink.0}">{translate text="MoreArticles"}</a>{/if}
    </td>
  </tr>
  {/if}

  <tr valign="top">
    <th>{translate text='Format'}: </th>
    <td>
     {if is_array($recordFormat)}
      {foreach from=$recordFormat item=displayFormat name=loop}
        <span class="iconlabel {$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat}</span>
      {/foreach}
    {else}
      <span class="iconlabel {$recordFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$recordFormat}</span>
    {/if}  
    </td>
  </tr>

  {if !empty($recordLanguage)}
  <tr valign="top">
    <th>{translate text='Language'}: </th>
    <td>{foreach from=$recordLanguage item=lang}{$lang|escape}<br>{/foreach}</td>
  </tr>
  {/if}

  {if !empty($corePublications)}
  <tr valign="top">
    <th>{translate text='Published'}: </th>
    <td>
      {foreach from=$corePublications item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($corePhysical)}
  <tr valign="top">
    <th>{translate text='Physical Description'}: </th>
    <td>
      {foreach from=$corePhysical item=field name=loop}
        {$field|escape}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreEdition)}
  <tr valign="top">
    <th>{translate text='Edition'}: </th>
    <td>
      {$coreEdition|escape}
    </td>
  </tr>
  {/if}

  {* Display series section if at least one series exists. *}
  {if !empty($coreSeries)}
  <tr valign="top">
    <th>{translate text='Series'}: </th>
    <td>
      {foreach from=$coreSeries item=field name=loop}
        {* Depending on the record driver, $field may either be an array with
           "name" and "number" keys or a flat string containing only the series
           name.  We should account for both cases to maximize compatibility. *}
        {if is_array($field)}
          {if !empty($field.name)}
            <a href="{$url}/Search/Results?lookfor=%22{$field.name|escape:"url"}%22&amp;type=Series">{$field.name|escape}</a>
            {if !empty($field.number)}
              ; {$field.number|escape}
            {/if}
            <br>
          {/if}
        {else}
          <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Series">{$field|escape}</a><br>
        {/if}
      {/foreach}
    </td>
  </tr>
  {/if}


<!-- RDG-specific: Aufteilung der Schlagworte -->
  {if !empty($coreMPGSubjectsRSWK)}
  <tr valign="top">
    <th>{translate text='Subjects (RSWK)'}: 
    <img class="tooltip" title="{translate text='Source'}: Schlagwortnormdatei" src="{$path}/interface/themes/mpg/images/rdg/question-button.png"/>
    </th>
    <td>
      {foreach from=$coreMPGSubjectsRSWK item=field name=loop}
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {assign var=subject value="$subject $subfield"}
          <a id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22" {* %22&amp;type=Subject rausgenommen *}
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreMPGSubjectsSTW)}
  <tr valign="top">
    <th>{translate text='Subjects (STW)'}: 
    <img class="tooltip" title="{translate text='Source'}: Subject-Thesaurus Wirtschaft / STW Thesaurus for Economics (ZBW Kiel)" src="{$path}/interface/themes/mpg/images/rdg/question-button.png"/>
    </th>
    <td>
      {foreach from=$coreMPGSubjectsSTW item=field name=loop}
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if $smarty.foreach.subloop.first}
          {assign var=subject value="$subfield"}
          <a id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22" {* %22&amp;type=Subject rausgenommen *}
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
<!-- Verlinkung zur STW via ID kann hier nach Bedarf aktiviert werden
          {else} 
            {if $subfield != 'STW'}
            [
            <a target="top" id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
              href="http://zbw.eu/stw/descriptor/{$subfield|escape:"url"}"
            onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
            onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">STW-Link</a>
            ]
-->
            {/if}
          {/if}
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreMPGSubjectsSH)}
  <tr valign="top">
    <th>{translate text='Subjects (SH)'}:
    <img class="tooltip" title="{translate text='Source'}: Subject Headings (Library of Congress + British Library)" src="{$path}/interface/themes/mpg/images/rdg/question-button.png"/>
    </th>		     
    <td>
      {foreach from=$coreMPGSubjectsSH item=field name=loop}
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {assign var=subject value="$subject $subfield"}
          <a id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22" {* %22&amp;type=Subject rausgenommen *}
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

<!-- alle anderen Schlagworte, z.B. aus anderen Quellen als RDG-Katalog -->
  {if empty($coreMPGSubjectsSH) and empty($coreMPGSubjectsRSWK) and empty($coreMPGSubjectsSTW) and !empty($coreSubjects)}
  <tr valign="top">
    <th>{translate text='Subjects'}: </th>
    <td>
      {foreach from=$coreSubjects item=field name=loop}
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {assign var=subject value="$subject $subfield"}
          <a id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22" {* %22&amp;type=Subject rausgenommen *}
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}


  {if !empty($coreProduct)}
  <tr valign="top">
    <th>{translate text='Product'}: </th>
    <td>
       {$coreProduct|escape}
    </td>
  </tr>
  {/if}

  {if !empty($coreURLs) || $coreOpenURL}
  <tr valign="top">
    <th>{translate text='Online Access'}: </th>
    <td>
      {if $MPGADAM}
        {foreach from=$MPGADAM item=adam}
          <a href="{$adam.url|escape}" target="new">{translate text=$adam.label}</a><br/>
        {/foreach}
        {foreach from=$coreURLs item=desc key=currentUrl name=loop}
        <!-- ADAM = Inhaltsverzeichnis = true = filtere MAB 655 aus, falls vorhanden + gib Rest aus -->
          {if !strstr($desc, "Inhaltsverzeichnis") and !strstr($desc, "Table")}
        <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
          {/if}
        {/foreach}
      {else}
      <!-- ADAM = Inhaltsverzeichnis = false = gebe alles aus MAB 655 aus -->
        {foreach from=$coreURLs item=desc key=currentUrl name=loop}
          <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
        {/foreach}
      {/if}
      {if $coreOpenURL}
        {include file="Search/openurl.tpl" openUrl=$coreOpenURL}<br/>
      {/if}
    </td>
  </tr>
  {/if}

  {if !empty($coreRecordLinks)}
  {foreach from=$coreRecordLinks item=coreRecordLink}
  <tr valign="top">
    <th>{translate text=$coreRecordLink.title}: </th>
    <td><a href="{$coreRecordLink.link|escape}">{$coreRecordLink.value|escape}</a></td>
  </tr>
  {/foreach}
  {/if}

  <tr valign="top">
    <th>{translate text='Tags'}: </th>
    <td>
      <span style="float:right;">
      {* RDG: check Login! *}
     {if $user} 
    {* RDG: AddTags: keine Lightbox, damit JS einfach eingebunden werden kann (ZBW JEL Suggestions) *}
     {* <a href="{$url}/Record/{$id|escape:"url"}/AddTag" class="tool add" *}
     {*      onClick="getLightbox('Record', 'AddTag', '{$id|escape}', null, '{translate text="Add Tag"}'); return false;">{translate text="Add"}</a>*}
        <a href="{$url}/Record/{$id|escape:"url"}/AddTag" class="tool add">{translate text="Add"}</a>
     {else}
        <span class="notloggedin tooltip add" title="{translate text='Please login first to use this function!'}">{translate text="Add"}</span>
     {/if}
      </span>
      <div id="tagList">
        {if $tagList}
          {foreach from=$tagList item=tag name=tagLoop}
        <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape:"html"}</a> ({$tag->cnt}){if !$smarty.foreach.tagLoop.last}, {/if}
          {/foreach}
        {else}
          {translate text='No Tags'}. {translate text='Be the first to tag this record'}!
        {/if}
      </div>
    </td>
  </tr>

  {if !empty($MPGUpLink) && $recordFormat.0 != "Article"}
{*
{if $f984a}
  <tr valign="top">
    <th>{translate text='UpTitle'}: </th>
    <td>
       <a href="{$url}/Search/Results?lookfor=aleph_id:{$MPGUpLink}+OR+id:{$MPGUpLink}">{$f9842a|escape}</a>
    </td>
  </tr>
{else}
  <tr align="top">
    <th><a href="{$url}/Search/Results?lookfor=aleph_id:{$MPGUpLink}+OR+id:{$MPGUpLink}">{translate text='UpTitle'}</a></th>
    <td>&nbsp;</td>
  </tr>
{/if}
*}
  <tr id="MPGUpLink" align="top">
    <th><a href="{$url}/Search/Results?lookfor=ppnlink:{$MPGUpLink}">{translate text='otherTitles'}</a></th>
    <td>&nbsp;</td>
  </tr>
  {/if}
{*
  {if !empty($MPGDownLink)}
    <tr align="top">
      <th><a href="{$url}/Search/Results?lookfor=ppnlink:{$MPGDownLink}">{translate text='DownTitles'}</a></th>
      <td>&nbsp;</td>
    </tr>
  {/if}
*}
{*
  {if !empty($MPGSeriesUpLink) && !empty($coreSeries)}
    <tr align="top">
      <th>{translate text='fullWork'}: </th>
      <td><a href="{$url}/Search/Results?lookfor=aleph_id:{$MPGSeriesUpLink}+OR+id:{$MPGSeriesUpLink}">{$coreSeries[0].name}</a></td>
    </tr>
  {/if}
*}
  {if !empty($coreCollections)}
    {foreach from=$coreCollections item=collection name=collectionLoop}
      {if $collection == "DOAJ"}
    <tr align="top">
      <th>&nbsp;</th>
      <td><a rel="license" href="http://creativecommons.org/licenses/by-sa/1.0/"><img alt="Creative Commons License" style="border-width:0" 
src="http://i.creativecommons.org/l/by-sa/1.0/80x15.png" /></a> Source: <a 
href="http://www.doaj.org/">Directory of Open Access Journals</a> (DOAJ). </td>
    </tr>
      {/if}
    {/foreach}
  {/if}
</table>
{* End Main Details *}
{* altmetrics.com *}
  {if $coreDOI}
    <div class="alignright">
       <div class='altmetric-embed' data-badge-popover='top' data-hide-no-mentions='true' data-doi="{$coreDOI}"></div>
    </div>
  {/if}

