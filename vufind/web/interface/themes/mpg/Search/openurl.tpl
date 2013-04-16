{if $openUrlEmbed}{assign var="openUrlId" value=$openUrlCounter->increment()}{/if}
<a href="{$openUrlBase|escape}?{$openUrl|escape}" {if $openUrlEmbed} onclick="getResolverLinks('{$openUrl|escape:"javascript"|escape}',{$openUrlId}, {literal}{{/literal} error: &quot;{translate text="An error has occurred"}&quot; {literal}}{/literal}); return false" id="openUrlLink{$openUrlId}"{elseif $openUrlWindow} onClick="window.open('{$openUrlBase|escape}?{$openUrl|escape}', 'openurl', '{$openUrlWindow|escape}'); return false;"{/if}>
  {if $openUrlGraphic}
    <img src="{$openUrlGraphic|escape}" alt="{translate text='Get full text'}" style="{if $openUrlGraphicWidth}width:{$openUrlGraphicWidth|escape}px;{/if}{if $openUrlGraphicHeight}height:{$openUrlGraphicHeight|escape}px;{/if} " />
  {else}
    {translate text='Get full text'}
  {/if}
</a>
{if $openUrlEmbed}
  <div id="openUrlEmbed{$openUrlId}" class="resolver" style="display: none"></div>
  {* for record-rdg-extra.js: *}
  <span id="mySFXURL" title="{$openUrl|escape:'javascript'|escape}" style="display:none"/>
{/if}
