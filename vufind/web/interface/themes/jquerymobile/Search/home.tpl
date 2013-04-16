<div data-role="page" id="Search-home">
  {include file="header.tpl"}
  <div data-role="content">
    {include file="Search/searchbox.tpl"}
    <ul data-role="listview" data-inset="true" data-dividertheme="b">
     <li data-role="list-divider">{translate text='Quellen'}</li> 
     {foreach from=$facetList item=details key=field}
        {assign var=list value=$details.sortedList}
        {* Special case: single, extra-wide column for Dewey call numbers... *}
        {if $field == "collection"}
         {foreach from=$list item=currentUrl key=value name="callLoop"}
           <li><a rel="external" href="{$currentUrl|escape}">{$value|escape}</a></li>
         {/foreach}
        {/if}
     {/foreach}
    </ul>
    {*
    <ul data-role="listview" data-inset="true" data-dividertheme="b">
      <li data-role="list-divider">{translate text='Need Help?'}</li>
      <li><a href="{$path}/Help/Home?topic=search" data-rel="dialog">{translate text='Search Tips'}</a></li>
      <li><a href="#">{translate text='Ask a Librarian'}</a></li>
      <li><a href="#">{translate text='FAQs'}</a></li>
    </ul>
    *} 
  </div>
  {include file="footer.tpl"}
</div>
