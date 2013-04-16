<ul class="commentList" id="commentList">
{* Pull in comments from a separate file -- this separation allows the same template
   to be used for refreshing this list via AJAX. *}
{include file="Record/view-comments-list.tpl"}
</ul>

{* RDG: check Login! *}
{if $user} 
<form name="commentForm" id="commentForm" action="{$url}/Record/{$id|escape:"url"}/UserComments" method="POST">
  <p><textarea name="comment" rows="4" cols="50"></textarea></p>
  <script language="JavaScript" type="text/javascript">
    document.write('<a href="{$url}/Record/{$id|escape:"url"}/UserComments" class="tool add"' +
      'onClick=\'SaveComment("{$id|escape}", {literal}{{/literal}save_error: "{translate text='comment_error_save'}", load_error: "{translate text='comment_error_load'}", save_title: "{translate text='Save Comment'}"{literal}}{/literal}); return false;\'>{translate text="Add your comment"}</a>');
  </script>
  <noscript>
    <input type="submit" value="{translate text="Add your comment"}"/>
  </noscript>
</form>
{else}
<span class="notloggedin tooltip add" title="{translate text='Please login first to use this function!'}">{translate text="Add your comment"}</span>
{/if}