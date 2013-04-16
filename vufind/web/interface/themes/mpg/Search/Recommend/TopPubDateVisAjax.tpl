{if $visFacets}

    {* flot *}
    <!--[if lte IE 8]>{js filename="flot/excanvas.min.js"}<![endif]--> 
    {js filename="flot/yui.flot.js"}
    {js filename="pubdate_vis.js"}

    {foreach from=$visFacets item=facetRange key=facetField}
      <div class="authorbox">
      <strong>{translate text=$facetRange.label}</strong>
      {* space the flot visualisation *}
      <div id="datevis{$facetField}x" style="margin:0 10px;width:700px;height:80px;cursor:crosshair;"></div>
      </div>
    {/foreach}

    <script type="text/javascript">
      loadVis('{$searchParams}', '{$url}');
    </script>

{/if}
