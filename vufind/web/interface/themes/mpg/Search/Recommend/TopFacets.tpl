{if $topFacetSet}
{* RDG: see d3.get-data.js *}
  {foreach from=$topFacetSet item=cluster key=title}
    {foreach from=$cluster.list item=thisFacet name="narrowLoop"}
      {if $smarty.foreach.narrowLoop.iteration == ($topFacetSettings.rows * $topFacetSettings.cols) + 1}
        <div id="showFacetVisualization" style="float:right;"><img title="{translate text='more'} &amp; {translate text='visualization'}" src="{$path}/interface/themes/mpg/images/rdg/piechart-thumb.png"/><br/><span id="showmore">{translate text='more'} ...</span><span id="showless" style="display:none">{translate text='less'} ...
        </div>
      {/if}
    {/foreach}
  {/foreach}
{* end d3 *}
  {foreach from=$topFacetSet item=cluster key=title}
  <div class="authorbox">
  <table class="facetsTop navmenu narrow_begin">
    <tr><th colspan="{$topFacetSettings.cols}">{translate text=$cluster.label}<span>{translate text="top_facet_suffix"}</span></th></tr>
        {foreach from=$cluster.list item=thisFacet name="narrowLoop"}
        {if $smarty.foreach.narrowLoop.iteration == ($topFacetSettings.rows * $topFacetSettings.cols) + 1}
{* RDG: for 'more' see visualization implementation in d3.get-data.js *}
{*    <tr id="more{$title}"><td><a href="#" onclick="moreFacets('{$title}'); return false;">{translate text='more'} ...</a></td></tr> *}
  </table>
  <table class="facetsTop navmenu narrowGroupHidden" id="narrowGroupHidden_{$title}">
    <tr><th colspan="{$topFacetSettings.cols}"><div class="top_facet_additional_text">{translate text="top_facet_additional_prefix"}{translate text=$cluster.label}<span>{translate text="top_facet_suffix"}</span></div></th></tr>
        {/if}
    {if $smarty.foreach.narrowLoop.iteration % $topFacetSettings.cols == 1}
    <tr>
    {/if}
        {if $thisFacet.isApplied}
{* RDG: we always need a facet count for d3.get-data.js *}
        <td>{$thisFacet.value|escape} (<span class="facetCount">{$thisFacet.count}</span>) <img src="{$path}/images/silk/tick.png" alt="Selected"></td>
        {else}
        <td><a href="{$thisFacet.url|escape}">{$thisFacet.value|escape}</a> (<span class="facetCount">{$thisFacet.count}</span>)</td>
        {/if}
    {if $smarty.foreach.narrowLoop.iteration % $topFacetSettings.cols == 0 || $smarty.foreach.narrowLoop.last}
    </tr>
    {/if}
        {if $smarty.foreach.narrowLoop.total > ($topFacetSettings.rows * $topFacetSettings.cols) && $smarty.foreach.narrowLoop.last}
{* RDG: for 'more' see visualization implementation in d3.get-data.js *}
{*    <tr><td><a href="#" onclick="lessFacets('{$title}'); return false;">{translate text='less'} ...</a></td></tr> *}
        {/if}
        {/foreach}
  </table>
  </div>
  {/foreach}
{/if}
