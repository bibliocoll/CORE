{if $pageTemplate=='history.tpl' && $user}
  {* if we're in /Search/History and logged in, then use MyResearch footer navbar instead *}
  {include file='MyResearch/footer-navbar.tpl'}
{else}
<div data-role="navbar">
  <ul>
    {if $recordCount > 0 && ($pageTemplate == 'list.tpl' || $pageTemplate == 'reserves-list.tpl' || $pageTemplate == 'newitem-list.tpl')}
      {* show Bag button on /Search/Results *}
      {* disabled until fully implemented:
      <li><a href="{$path}/Cart/Home" class="book_bag_btn" data-rel="dialog" data-transition="flip">{translate text="Bag"} (<span class="cart_size">0</span>)</a></li> 
       *}
    {else}
      {* show Language button everywhere else *}
{*      <li><a data-rel="dialog" href="#Language-dialog" data-transition="pop">{translate text="Language"}</a></li>      *}
    {/if}

    <li><a data-rel="dialog" href="#Language-dialog" data-transition="pop">{translate text="Language"}</a></li>
    
    {* always show Account button *}
    {if $user}
    <li><a rel="external" href="{$path}/MyResearch/Home">{translate text="Account"}</a></li>
    {else}
    <li><a rel="external" href="https://intern.coll.mpg.de/biblio/mobile/login-vufind.php">{translate text="Account"}</a></li>
    {/if}    
    {* show Logout if logged in *}
    {if $user}
      <li><a rel="external" href="{$path}/MyResearch/Logout">{translate text="Logout"}</a></li>
    {/if}
  </ul>
</div>
{/if}
