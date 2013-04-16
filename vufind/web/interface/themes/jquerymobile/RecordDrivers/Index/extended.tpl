<dl class="biblio" title="{translate text='Description'}">
  {if !empty($extendedDescription)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Description'}:</dt>
    <dd>
      {$extendedDescription|escape}
    </dd>
  {/if}

  {if !empty($extendedSummary)}
  {assign var=extendedContentDisplayed value=1}

    <dt>{translate text='Summary'}:</dt>
    <dd>
      {foreach from=$extendedSummary item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  
  {/if}

  {if !empty($extendedDateSpan)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Published'}:</dt>
    <dd>
      {foreach from=$extendedDateSpan item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedNotes)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Item Description'}:</dt>
    <dd>
      {foreach from=$extendedNotes item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedPhysical)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Physical Description'}:</dt>
    <dd>
      {foreach from=$extendedPhysical item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedFrequency)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Publication Frequency'}:</dt>
    <dd>
      {foreach from=$extendedFrequency item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedPlayTime)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Playing Time'}:</dt>
    <dd>
      {foreach from=$extendedPlayTime item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedSystem)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Format'}:</dt>
    <dd>
      {foreach from=$extendedSystem item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedAudience)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Audience'}:</dt>
    <dd>
      {foreach from=$extendedAudience item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedAwards)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Awards'}:</dt>
    <dd>
      {foreach from=$extendedAwards item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedCredits)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Production Credits'}:</dt>
    <dd>
      {foreach from=$extendedCredits item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedBibliography)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Bibliography'}:</dt>
    <dd>
      {foreach from=$extendedBibliography item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedISBNs)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='ISBN'}:</dt>
    <dd>
      {foreach from=$extendedISBNs item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedISSNs)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='ISSN'}:</dt>
    <dd>
      {foreach from=$extendedISSNs item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedRelated)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Related Items'}:</dt>
    <dd>
      {foreach from=$extendedRelated item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedAccess)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Access'}:</dt>
    <dd>
      {foreach from=$extendedAccess item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedFindingAids)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Finding Aid'}:</dt>
    <dd>
      {foreach from=$extendedFindingAids item=field name=loop}
        <p>{$field|escape}</p>
      {/foreach}
    </dd>
  {/if}

{* RDG neu Systematik *}
  {if !empty($extendedClassificationShort)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Notation'}:</dt>
    <dd>
      {foreach from=$extendedClassificationShort item=field name=loop}
          <span class="classificationShort">
            <a title="{$field|escape}" href="{$path}/Search/Results?lookfor=%22{$field|escape}%22&type=classification_local_short_txt_mv&view=list">{$field|escape}</a>
          </span>
          {if $smarty.foreach.loop.last}{else}<br/>{/if}
      {/foreach}
    </dd>
  {/if}

  {if !empty($extendedClassificationLong)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Notation'}: </dt>
    <dd>
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
    </dd>
  {/if}

  {if !empty($extendedClassificationJEL)}
  {assign var=extendedContentDisplayed value=1}
    <dt>{translate text='Notation'} (JEL): </dt>
    <dd>
      {foreach from=$extendedClassificationJEL item=field name=loop}
          <span class="classificationJEL">
            <a title="{$field|escape}" href="{$path}/Search/Results?lookfor=%22{$field|escape}%22&type=classification_jel&view=list">{$field|escape}</a>
         </span>
          {if $smarty.foreach.loop.last}{else}<br/>{/if}
      {/foreach}
    </dd>
  {/if}


</dl>
