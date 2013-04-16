{* Your footer *}

{* auskommentiert, wird nun in Search/home.tpl angezeigt *}
{*
<div><p><strong>{translate text='Search Options'}</strong></p>
  <ul>
    <li><a href="{$path}/Search/History">{translate text='Search History'}</a></li>
    <li><a href="{$path}/Search/Advanced">{translate text='Advanced Search'}</a></li>
    <li><a href="{$path}/Classification/Classification">{translate text='Classification Search'}</a></li>
  </ul>
</div>
<div><p><strong>{translate text='Find More'}</strong></p>
  <ul>
    <li><a href="{$path}/Browse/Home">{translate text='Browse the Catalog'}</a></li>
    <li><a href="{$path}/AlphaBrowse/Home">{translate text='Browse Alphabetically'}</a></li>
  </ul>
</div>
<div><p><strong>{translate text='Need Help?'}</strong></p>
  <ul>
    <li><a href="mailto:biblio@coll.mpg.de">{translate text='Ask a Librarian'}</a></li>
    <li><a href="{$url}/Help/Home?topic=search" onClick="window.open('{$url}/Help/Home?topic=search', 'Help', 'width=625, height=510'); return false;">{translate text='Search Tips'}</a></li>
  </ul>
</div>
<div><p><strong>{translate text='Library'}</strong></p>
  <ul>
        <li><a href="http://intern.coll.mpg.de/library/page/library">{translate text='Homepage'}</a></li>
        <li><a href="http://www.coll.mpg.de/bib/aleph-rss/rss.php?myquery=wab=new-acq&mybase=rdg01">{translate text='New Aquisitions'} <img src="{$path}/interface/themes/default/images/silk/feed.png"></a></li>
  </ul>
</div>
*}

<a href="http://intern.coll.mpg.de/library/page/library">{translate text='Library'}</a>
<a href="http://intern.coll.mpg.de/biblio/ask-a-librarian">{translate text='Ask a Librarian'}</a>
<a href="http://intern.coll.mpg.de/library/page/service-alerts">{translate text='New Aquisitions'}</a>
<a id="footerRSS" href="http://www.coll.mpg.de/bib/aleph-rss/rss.php?myquery=wab=new-acq&mybase=rdg01"><img src="{$path}/interface/themes/default/images/silk/feed.png"></a>

<br clear="all">
{* Comply with Serials Solutions terms of service -- this is intentionally left untranslated. *}
{if $module == "Summon"}Powered by Summon™ from Serials Solutions, a division of ProQuest.{/if}
