{foreach from=$holdings item=holding key=location}
<h4>{translate text=$location}</h4>
<table class="holdings" summary="{translate text='Holdings details from'} {translate text=$location}">
  {if $holding.0.callnumber}
  <tr>
    <th>{translate text="Call Number"}: </th>
    <td>{$holding.0.callnumber|escape}</td>
  </tr>
  {/if}
  {if $holding.0.summary}
  <tr>
    <th>{translate text="Volume Holdings"}: </th>
    <td>
      {foreach from=$holding.0.summary item=summary}
      {$summary|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {if $holding.0.notes}
  <tr>
    <th>{translate text="Notes"}: </th>
    <td>
      {foreach from=$holding.0.notes item=data}
      {$data|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {foreach from=$holding item=row}
    {if $row.barcode != ""}
  <tr>
    <th>{$row.callnumber}</th>
    <td>
      {if $row.reserve == "Y"}
      {translate text="On Reserve - Ask at Circulation Desk"}
      {else}
        {if $row.availability}
      <span class="available">{translate text="Available"}</span> | 
      {if $row.link}<a href="{$row.link|escape}">{translate text="Place a Hold"}</a>{/if}
        {else}
      <span class="checkedout">{$row.status|escape}</span>
          {if $row.duedate}
            {translate text="Due"}: {$row.duedate|escape} |
          {/if}
          {if $row.link}<a href="{$row.link|escape}">{translate text="Recall This"}</a>{/if}

        {/if}
       {if $row.description}
         {$row.description}
       {/if}
      {/if}
    </td>
  </tr>
    {/if}
  {/foreach}
</table>
{/foreach}

{if $history}
<h4>{translate text="Most Recent Received Issues"}</h4>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue|escape}</li>
  {/foreach}
</ul>
{/if}

{if !empty($holdingURLs) || $holdingsOpenURL || !empty($MPGADAM)}
  <h3>{translate text="Internet"}</h3>
  {if !empty($MPGADAM)}
    {foreach from=$MPGADAM item=adam}
      <a href="{$adam.url|escape}" target="new">{translate text=$adam.label}</a><br/>
    {/foreach}
    <!-- ADAM = Inhaltsverzeichnis = true = filtere MAB 655 aus, falls vorhanden + gib Rest aus -->
        {if !empty($holdingURLs)}
          {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
            {if !strstr($desc, "Inhaltsverzeichnis") and !strstr($desc, "Table")}            
              <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
                <!-- Volltext? -->
                {if stristr($desc, "Full") or stristr($desc, "Voll") or stristr($desc, "Lizenz") or stristr($desc, "online")}
                  <div class="showFulltextLinkRecord">
                     <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text="Get full text"}</a><br/>
                  </div>
                  <div class="showFulltextLinkRecordNote">
                     <span>{translate text="FulltextNote"}!</span>
                   </div>
                {/if}
            {/if}
          {/foreach}
        {/if}
  {else}
  <!-- ADAM = Inhaltsverzeichnis = false = gebe alles aus MAB 655 aus -->
    {if !empty($holdingURLs)}
      {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
        <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text=$desc}</a><br/>
                <!-- Volltext? -->
	        {if stristr($desc, "Full") or stristr($desc, "Voll") or stristr($desc, "Lizenz") or stristr($desc, "online")}
                  <div class="showFulltextLinkRecord">
                     <a href="{if $proxy}{$proxy}/login?qurl={if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape:"url"}{else}{if !strstr($currentUrl, "http")}http://{/if}{$currentUrl|escape}{/if}">{translate text="Get full text"}</a><br/>
                  </div> 
                  <div class="showFulltextLinkRecordNote">
                     <span>{translate text="FulltextNote"}!</span>
                  </div>
                {/if}
      {/foreach}
    {/if}
  {/if}
{/if}

