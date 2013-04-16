<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Description'}">

  {if !empty($downLinks)}
  {*$downLinks|@print_r*}

   {foreach from=$downLinks item=entries key=key name=loop}
   <tr><th>{translate text='Title'}:</th>
   <td>
   {if !empty($entries.author)}
     {if is_array($entries.author)}{$entries.author[0]}{else}{$entries.author}{/if}:&nbsp;
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
   {/foreach}

      <td>&nbsp;</td>
    </tr>
  {/if}


</table>
