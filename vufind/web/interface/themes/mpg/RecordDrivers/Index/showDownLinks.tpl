<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Description'}">

  {if !empty($downLinks)}
  {*$downLinks|@print_r*}

   {foreach from=$downLinks item=entries key=key name=loop}
   <tr class="downLinkRow" {if ($smarty.foreach.loop.iteration > 50)} style="display:none"{else}style="display:table-row"{/if}>
   <th>{translate text='Title'}:</th>
   <td>
   {if !empty($entries.author)}
     {if is_array($entries.author)}{$entries.author[0]|escape}{else}{$entries.author|escape}{/if}:&nbsp;
   {/if}
   {if !empty($entries.publishDate)}
   <a href="{$path}/Record/{$entries.id}">{$entries.publishDate}</a>. 
   {/if}
   {if !empty($entries.title)}	
     <a href="{$path}/Record/{$entries.id}">
       {if is_array($entries.title)}{$entries.title[0]}{else}{$entries.title}{/if}
     </a> 
   {/if}
   </td></tr>

   {if $smarty.foreach.loop.iteration == 50}
   <tr class="downLinkRow" style="display:table-row" id="showMoreDownLinks"><th></th>
     <td><span><a href="#">{translate text="There are more entries"}&nbsp;&gt; </span>
       <a style="display:none" href="#">{translate text="more"}</a>
     </td>
   </tr>
   {/if}
       
   {/foreach}
  {/if}


</table>
