<form name="addForm" action="">
  {foreach from=$recordSet item=record name="recordLoop"}
    <div class="result {if ($smarty.foreach.recordLoop.iteration % 2) == 0}alt {/if}record{$smarty.foreach.recordLoop.iteration}{if !empty($summCollections)}{foreach from=$summCollections item=collection name=collectionLoop}{if $collection == "Local Library Catalog"} local{/if}{/foreach}{/if}">
      {* This is raw HTML -- do not escape it: *}
      {$record}
    </div>
  {/foreach}
</form>

<script type="text/javascript">
  doGetStatuses({literal}{{/literal}
    unknown: '<span class="unknown">{translate text='Unknown'}<\/span>'
  {literal}}{/literal});
  {if $user}
  doGetSaveStatuses();
  {/if}
</script>
