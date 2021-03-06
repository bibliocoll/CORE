<div class="searchform yui-skin-sam">
  {if $searchType == 'advanced'}
    <a href="{$path}/Search/Advanced?edit={$searchId}" class="small">{translate text="Edit this Advanced Search"}</a> |
    <a href="{$path}/Search/Advanced" class="small">{translate text="Start a new Advanced Search"}</a> |
    <a href="{$path}/" class="small">{translate text="Start a new Basic Search"}</a>
    <br>{translate text="Your search terms"} : "<b>{$lookfor|escape:"html"}</b>"
  {else}
    <form method="GET" action="{$path}/Search/Results" name="searchForm" id="searchForm" class="search">
      {* Only activate autocomplete if it is configured to be turned on: *}
      <div class="hiddenLabel"><label for="lookfor">{translate text="Search For"}:</label></div>
      {if $autocomplete}
        <div id="autoCompleteContainer">
          <input id="lookfor" type="text" name="lookfor" size="30" value="{$lookfor|escape:"html"}">
          <div id="suggestions"></div>
        </div>
        <script language="JavaScript" type="text/javascript">
          initAutocomplete("lookfor", "suggestions", "AuthorTitle"); 
          // RDG: neu, SearchType direkt als 3. Parameter übergeben, s. angepasste Funktion in ajax.yui.js
        </script>
      {else}
        <input id="lookfor" type="text" name="lookfor" size="30" value="{$lookfor|escape:"html"}">
      {/if}
      <div class="hiddenLabel"><label for="type">{translate text="in"}:</label></div>
      <select id="type" name="type">
      {foreach from=$basicSearchTypes item=searchDesc key=searchVal}
        <option value="{$searchVal}"{if $searchIndex == $searchVal} selected{/if}>{translate text=$searchDesc}</option>
      {/foreach}
      </select>
      <input id="defaultSearchButton" type="submit" name="submitButton" value="{translate text="Find"}">&nbsp;
 <!-- <a href="{$path}/Search/Advanced" class="small">{translate text="Advanced"}</a> -->

      {* Do we have any checkbox filters? *}
      {assign var="hasCheckboxFilters" value="0"}
      {if isset($checkboxFilters) && count($checkboxFilters) > 0}
        {foreach from=$checkboxFilters item=current}
          {if $current.selected}
            {assign var="hasCheckboxFilters" value="1"}
          {/if}
        {/foreach}
      {/if}
      {if $shards}
        <br />
        {foreach from=$shards key=shard item=isSelected}
          {* id + label für jquery.qtip.funcs.js *}
          <input id="{$shard|escape|replace:' ':''}" type="checkbox" {if $isSelected}checked="checked" {/if}name="shard[]" value='{$shard|escape}' /> <label for="{$shard|escape|replace:' ':''}">{$shard|translate}</label>
        {/foreach}
      {/if}

        <div class="keepFilters">
      {* RDG: pre-filter for local holdings only *}
          <span id="checkboxPrefilter">
          <input type="checkbox" name="filter[]" value='collection:&quot;{"Local Library Catalog"|escape}&quot;' />&nbsp;
            {translate text="show me only what is in the library"}
          </span>
      {if $filterList || $hasCheckboxFilters}
          <input id="retainAll" type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} onclick="filterAll(this);" />
          <label for="retainAll" style="color:red">{translate text="basic_search_keep_filters"}</label>
          <div style="display:none;">
            {foreach from=$filterList item=data key=field}
              {foreach from=$data item=value}
                <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value='{$value.field|escape}:&quot;{$value.value|escape}&quot;' />
              {/foreach}
            {/foreach}
            {foreach from=$checkboxFilters item=current}
              {if $current.selected}
                <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value="{$current.filter|escape}" />
              {/if}
            {/foreach}
          </div>
      {/if}
        </div>
      {* Load hidden limit preference from Session *}
      {if $lastLimit}<input type="hidden" name="limit" value="{$lastLimit|escape}" />{/if}
      {if $lastSort}<input type="hidden" name="sort" value="{$lastSort|escape}" />{/if}
    </form>
  {/if}
</div>
