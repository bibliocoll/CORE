{if $coreThumbMedium}
<div class="recordcover">
  {if $coreThumbLarge}<a rel="external" href="{$coreThumbLarge|escape}">{/if}
    <img alt="{translate text='Cover Image'}" class="recordcover" src="{$coreThumbMedium|escape}"/>
  {if $coreThumbLarge}</a>{/if}
</div>
{/if}

<h3>
{if $f9843a}{$f9843a|escape}{/if} {if $f9843b}: {$f9843b|escape}{/if}{if $f9843c}<br>{$f9843c|escape}{/if}<br>
{if $f9842n}{$f9842n|escape}. {/if}{if $f9842a}{$f9842a|escape}{/if} {if $f9842b}: {$f9842b|escape}{/if}{if $f9842c}<br>{$f9842c|escape}{/if}<br>
{if $MPGtitle}{$MPGtitle|escape}
{else}
{$coreShortTitle|escape}
{if $coreSubtitle}: {$coreSubtitle.0|escape}{/if}
{if $coreTitleSection}{$coreTitleSection|escape}{/if}
{/if}
</h3>

{if $coreSummary}<p>{$coreSummary|truncate:200:"..."|escape}</p>{/if}

<dl class="biblio" title="{translate text='Bibliographic Details'}">
  {if !empty($MPGHoldings)}
    <dt>{translate text='Library Holdings'}: </dt>
    <dd>
      {foreach from=$MPGHoldings item=field name=loop}
        {$field}<br>
      {/foreach}
    </dd>
  {/if}

  {if !empty($MPGParallel)}
     <dt>{translate text='Previous Title'}: </dt>
     <dd>
        {foreach from=$MPGParallel item=field name=loop}
           {if $field.w}<a rel="external" href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}">{$field.a|escape}</a>{else}{$field.a|escape}{/if}<br>
        {/foreach}
     </dd>
  {/if}
  {if !empty($MPGNewer)}
     <dt>{translate text='Previous Title'}: </dt>
     <dd>
        {foreach from=$MPGNewer item=field name=loop}
           {if $field.w}<a rel="external" href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}">{$field.a|escape}</a>{else}{$field.a|escape}{/if}<br>
        {/foreach}
     </dd>
  {elseif !empty($coreNextTitles)}
    <dt>{translate text='New Title'}: </dt>
    <dd>
      {foreach from=$coreNextTitles item=field name=loop}
        <a rel="external" href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br>
      {/foreach}
    </dd>
  {/if}

  {if !empty($MPGPrevious)}
     <dt>{translate text='Previous Title'}: </dt>
     <dd>
        {foreach from=$MPGPrevious item=field name=loop}
           {if $field.w}<a rel="external" href="{$url}/Search/Results?lookfor=aleph_id:{$field.w}">{$field.a|escape}</a>{else}{$field.a|escape}{/if}<br>
        {/foreach}
     </dd>
  {elseif !empty($corePrevTitles)}
    <dt>{translate text='Previous Title'}: </dt>
    <dd>
      {foreach from=$corePrevTitles item=field name=loop}
        <a rel="external" href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br>
      {/foreach}
    </dd>
  {/if}


{if !empty($coreMainAuthor)}
  <dt>{translate text='Main Author'}:</dt>
  <dd><a rel="external" href="{$path}/Search/Results?lookfor={$coreMainAuthor|regex_replace:"/\[.*\]/"|escape:"url"}&type=Author">{$coreMainAuthor|escape}</a></dd>
{/if}

<dt>{translate text='Format'}:</dt>
<dd>
     {if is_array($recordFormat)}
      {foreach from=$recordFormat item=displayFormat name=loop}
        <span class="iconlabel {$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat}</span>
      {/foreach}
    {else}
      <span class="iconlabel {$recordFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$recordFormat}</span>
    {/if}  
</dd>

{if !empty($recordLanguage)}
<dt>{translate text='Language'}:</dt>
<dd>{foreach from=$recordLanguage item=lang}{$lang|escape} {/foreach}</dd>
{/if}

{if !empty($corePublications) and !empty($corePublications.0)}
  <dt>{translate text='Published'}:</dt>
  <dd>
      {foreach from=$corePublications item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
  </dd>
{/if}

{if !empty($coreEdition)}
  <dt>{translate text='Edition'}:</dt>
  <dd>{$coreEdition|escape}</dd>
{/if}

{if !empty($coreSubjects)}
  <dt>{translate text='Subjects'}:</dt>
  <dd>
  {foreach from=$coreSubjects item=field name=loop}
  <p>
    {assign var=subject value=""}
    {foreach from=$field item=subfield name=subloop}
      {if !$smarty.foreach.subloop.first}--{/if}
      {assign var=subject value="$subject $subfield"}
      <a rel="external" href="{$path}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject">{$subfield|escape}</a>
    {/foreach}
  </p>
  {/foreach}
  </dd>
{/if}

{if !empty($coreCorporateAuthor)}
  <dt>{translate text='Corporate Author'}:</dt> 
  <dd>
    <p><a rel="external" href="{$path}/Search/Results?lookfor={$coreCorporateAuthor|regex_replace:"/\[.*\]/"|escape:"url"}&type=Author">{$coreCorporateAuthor|escape}</a></p>
  </dd>
{/if}

{if !empty($coreContributors)}
  <dt>{translate text='Other Authors'}:</dt>
  <dd>
  {foreach from=$coreContributors item=field name=loop}
    <p><a rel="external" href="{$path}/Search/Results?lookfor={$field|regex_replace:"/\[.*\]/"|escape:"url"}&type=Author">{$field|escape}</a></p>
  {/foreach}
  </dd>
{/if}

  {if !empty($coreGBVSource)}
    <dt>{translate text='Source'}: </dt>
    <dd>
      {if !empty($coreGBVJournalLink)}<a href="{$url}/Search/Results?lookfor=aleph_id:{$coreGBVJournalLink.0}+OR+id:{$coreGBVJournalLink.0}">{/if}{$coreGBVSource}{if !empty($coreGBVJournalLink)}</a>{/if}
      {if !empty($coreGBVJournalLink)}<br/><a href="{$url}/Search/Results?lookfor=ppnlink:{$coreGBVJournalLink.0}">{translate text="MoreArticles"}</a>{/if}
    </dd>
  {/if}

{* Display series section if at least one series exists. *}
{if !empty($coreSeries)}
  <dt>{translate text='Series'}:</dt>
  <dd>
  {foreach from=$coreSeries item=field name=loop}
    {* Depending on the record driver, $field may either be an array with
       "name" and "number" keys or a flat string containing only the series
       name.  We should account for both cases to maximize compatibility. *}
    {if is_array($field)}
      {if !empty($field.name)}
        <p>
        <a rel="external" href="{$path}/Search/Results?lookfor=%22{$field.name|escape:"url"}%22&amp;type=Series">{$field.name|escape}</a>
        {if !empty($field.number)}
          {$field.number|escape}
        {/if}
        </p>
      {/if}
    {else}
      <p><a rel="external" href="{$path}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Series">{$field|escape}</a></p>
    {/if}
  {/foreach}
  </dd>
{/if}

{if !empty($coreURLs) || $coreOpenURL}
  <dt>{translate text='Online Access'}:</dt>
  <dd>
      {if $MPGADAM}
        {foreach from=$MPGADAM item=adam}
          <a rel="external" href="{$adam.url|escape}" target="new">{translate text=$adam.label}</a><br/>
        {/foreach}
      {/if}
      {foreach from=$coreURLs item=desc key=currentUrl name=loop}
        <a rel="external" href="{if $proxy}{$proxy}/login?qurl={$currentUrl|escape:"url"}{else}{$currentUrl|escape}{/if}">{$desc|escape}</a><br/>
      {/foreach}
      {if $coreOpenURL}
        {include file="Search/openurl.tpl" openUrl=$coreOpenURL}<br/>
      {/if}
  </dd>
{/if}

{if $tagList}
  <dt>{translate text='Tags'}:</dt>
  <dd>
    {foreach from=$tagList item=tag name=tagLoop}
      <a rel="external" href="{$path}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape:"html"}</a> ({$tag->cnt}){if !$smarty.foreach.tagLoop.last}, {/if}
    {/foreach}
  </dd>
{/if}

  {if !empty($MPGUpLink) && $recordFormat.0 != "Article"}
{*
    <dt>{translate text='UpTitle'}: </dt>
    <dd>
       <a rel="external" href="{$url}/Search/Results?lookfor=aleph_id:{$MPGUpLink}">{$f9842a|escape}</a>
    </dd>
*}
    <dt><a rel="external" href="{$url}/Search/Results?lookfor=ppnlink:{$MPGUpLink}">{translate text='otherTitles'}</a></dt>
    <dd>&nbsp;</dd>
  {/if}

  {if !empty($MPGDownLink)}
      <dt><a rel="external" href="{$url}/Search/Results?lookfor=ppnlink:{$MPGDownLink}">{translate text='DownTitles'}</a></dt>
      <dd>&nbsp;</dd>
  {/if}
{*
  {if !empty($MPGSeriesUpLink) && !empty($coreSeries)}
      <dt>{translate text='fullWork'}: </dt>
      <dd><a rel="external" href="{$url}/Search/Results?lookfor=aleph_id:{$MPGSeriesUpLink}">{$coreSeries[0].name}</a></dd>
  {/if}
*}
</dl>
