<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Description'}">
  {if !empty($extendedDescription)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Description'}: </th>
    <td>
      {$extendedDescription|escape}
    </td>
  </tr>
  {/if}

  {if !empty($extendedSummary)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td>
      {foreach from=$extendedSummary item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedDateSpan)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='PublishedDate'}: </th>
    <td>
      {foreach from=$extendedDateSpan item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedNotes)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Item Description'}: </th>
    <td>
      {foreach from=$extendedNotes item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}



<!-- ausgelagert in core.tpl
  {if !empty($extendedPhysical)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Physical Description'}: </th>
    <td>
      {foreach from=$extendedPhysical item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
-->

  {if !empty($extendedFrequency)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Publication Frequency'}: </th>
    <td>
      {foreach from=$extendedFrequency item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedPlayTime)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Playing Time'}: </th>
    <td>
      {foreach from=$extendedPlayTime item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedSystem)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Format'}: </th>
    <td>
      {foreach from=$extendedSystem item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAudience)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Audience'}: </th>
    <td>
      {foreach from=$extendedAudience item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAwards)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Awards'}: </th>
    <td>
      {foreach from=$extendedAwards item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedCredits)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Production Credits'}: </th>
    <td>
      {foreach from=$extendedCredits item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedBibliography)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Bibliography'}: </th>
    <td>
      {foreach from=$extendedBibliography item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedISBNs)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='ISBN'}: </th>
    <td>
      {foreach from=$extendedISBNs item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedISSNs)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='ISSN'}: </th>
    <td>
      {foreach from=$extendedISSNs item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedRelated)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Related Items'}: </th>
    <td>
      {foreach from=$extendedRelated item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAccess)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Access'}: </th>
    <td>
      {foreach from=$extendedAccess item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedFindingAids)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Finding Aid'}: </th>
    <td>
      {foreach from=$extendedFindingAids item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

<!-- RDG neu Systematik -->
  {if !empty($extendedClassificationShort)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Notation'}: </th>
    <td>
      {foreach from=$extendedClassificationShort item=field name=loop}
          <span class="classificationShort">
            <a title="{$field|escape}" href="{$path}/Search/Results?lookfor=%22{$field|escape}%22&type=classification_local_short_txt_mv&view=list">{$field|escape}</a>
          </span>
          &nbsp;<a href="{$path}/Classification/Classification?group={$field|truncate:3:"":true}#{$field|replace:' ':''}">[{translate text='go to classification'}...]</a>
          {if $smarty.foreach.loop.last}{else}<br/>{/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedClassificationLong)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Notation'}: </th>
    <td>
      <!-- zur besseren Unterscheidung in odd/even einteilen f. CSS -->
      <h4>deutsch:</h4>
      {foreach from=$extendedClassificationLong item=field name=loop}
        {if $smarty.foreach.loop.index is even}	 
          <span class="classificationGer">{$field|escape}</span><br/>
        {/if}
      {/foreach}
      <hr noshade />
      <h4>english:</h4>
      {foreach from=$extendedClassificationLong item=field name=loop}
        {if $smarty.foreach.loop.index is odd}	 
          <span class="classificationEng">{$field|escape}</span><br/>
        {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedClassificationJEL)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Notation'} (JEL): </th>
    <td>
      {foreach from=$extendedClassificationJEL item=field name=loop}
          <span class="classificationJEL">
            <a title="{$field|escape}" href="{$path}/Search/Results?lookfor=%22{$field|escape}%22&type=classification_jel_txt_mv&view=list">{$field|escape}</a>
	  &nbsp;<img src="{$path}/interface/themes/mpg/images/rdg/question-button.png"/> 
         </span>
          {if $smarty.foreach.loop.last}{else}<br/>{/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {* Avoid errors if there were no rows above *}
  {if !$extendedContentDisplayed}
  <tr><td>&nbsp;</td></tr>
  {/if}
</table>
