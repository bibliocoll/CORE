<a rel="external" href="{$path}/Record/{$summId|escape:'url'}">
  <div class="result recordId" id="record{$summId|escape}">
  <h3>
      {if !empty($MPGtitle)}
      {if $f9843a}{$f9843a|escape}{if $f9843b}: {$f9843b|escape}{/if}. {/if}{if $f9842n}{$f9842n}: {/if}{if $f9842a}{$f9842a|escape}{if $f9842b}: {$f9842b|escape}{/if}. {/if}{if $MPGtitle}{$MPGtitle|escape}{/if}
      {else}
      {if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}
      {/if}
  </h3>
  <p>
  {if !empty($summAuthor)}
      {translate text='by'}
        {if is_array($summAuthor)}
          {foreach from=$summAuthor item=auth name=loop}
            {$auth|escape}{if !$smarty.foreach.loop.last}; {/if}
          {/foreach}
        {else}
          {$summAuthor|escape}
        {/if}
  {/if}
      {if $summGBVSource}{$summGBVSource}
      {else if $summDate}{translate text='Published'} {$summDate.0|escape}
      {/if}
   </p>
      {if !empty($summURLs)}
        {foreach from=$summURLs key=recordurl item=urldesc}
          <p><a rel="external" href="{if $proxy}{$proxy}/login?qurl={$recordurl|escape:"url"}{else}{$recordurl|escape}{/if}" class="fulltext" target="new">{if $recordurl == $urldesc}{translate text='Get full text'}{else}{$urldesc|escape}{/if}</a></p>
        {/foreach}
      {/if}
      {if !empty($MPGADAM)}
        {foreach from=$MPGADAM item=adam}
          <p><a rel="external" href="{$adam.url|escape}" target="new" class="{$adam.label|lower}MPG">{translate text=$adam.label}</a></p>
        {/foreach}
      {/if}

  {if $summAjaxStatus}
    <p><strong>{translate text='Call Number'}:</strong> <span class="ajax_availability hide callnumber{$summId|escape}">{translate text='Loading'}...</span></p>
    <p><strong>{translate text='Located'}:</strong> <span class="ajax_availability hide location{$summId|escape}">{translate text='Loading'}...</span></p>
  {elseif !empty($summCallNo)}
    <p><strong>{translate text='Call Number'}:</strong> {$summCallNo|escape}</p>
  {/if}
  {if !empty($summFormats)}
    <p>
    {foreach from=$summFormats item=format}
      <span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span>
    {/foreach}
      <span class="ajax_availability hide status{$summId|escape}">{translate text='Loading'}...</span>
    </p>
  {/if}
  </div>
</a>
<a href="#" data-record-id="{$summId|escape}" title="{translate text='Add to book bag'}" class="add_to_book_bag">{translate text="Add to book bag"}</a>
