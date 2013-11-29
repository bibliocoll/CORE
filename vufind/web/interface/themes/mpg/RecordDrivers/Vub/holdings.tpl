{*RDG*} 

{if strpos($availability, "vergriffen") !== false}
      <div id="orderBoxLiteraturagentVUB" class="othercontent outofprint">
        {translate text="This book is currently out of print"}.<br/><br/>
        {translate text="Are you interested in this title"}? 
        {translate text="We will gladly try to get this book for you from a second-hand bookshop or via inter-library loan"}!
        <div class="magicbuttonbox">	
          <a class="magicbutton" href="http://intern.coll.mpg.de/node/3159/?rft_atitle={$coreShortTitle}&rft_au={$coreMainAuthor}&isbn={$isbn}&rft_jtitle={$corePublications.0}&from=CORE">
           {translate text="order here"} ({translate text="internal only"})
	  </a>
        </div>
{* Formular deaktiviert (einheitliche Links, s. Index/holdings.tpl *}
{*        
	  <form id="vubform" action="http://intern.coll.mpg.de/node/3159/" method="get">
          <input type="hidden" name="rft_atitle" value="{$coreShortTitle}"/>
          <input type="hidden" name="rft_au" value="{$coreMainAuthor}"/>
          <input type="hidden" name="isbn" value="{$isbn}"/>
          <input type="hidden" name="rft_jtitle" value="{$corePublications.0}"/>
          <input type="hidden" name="from" value="CORE"/>
          <br/>
          <button type="submit">{translate text="order here"} ({translate text="internal only"})</button>
        </form>
*}
      </div>
{else}
      <div id="vub" class="othercontent">
        {translate text="Are you interested in this title"}? 
        {translate text="Feel free to send us a purchase suggestion"}! 
        <div class="magicbuttonbox">	
          <a class="magicbutton" href="http://intern.coll.mpg.de/node/3160/?title={$coreShortTitle}&author={$coreMainAuthor}&isbn={$isbn}&publications={$corePublications.0}&from=CORE">
           {translate text="order here"} ({translate text="internal only"})
	  </a>
        </div>
{* Formular deaktiviert (einheitliche Links, s. Index/holdings.tpl *}
{*
        <form id="vubform" action="http://intern.coll.mpg.de/node/3160/" method="get">
          <input type="hidden" name="title" value="{$coreShortTitle}"/>
          <input type="hidden" name="author" value="{$coreMainAuthor}"/>
          <input type="hidden" name="isbn" value="{$isbn}"/>
          <input type="hidden" name="publications" value="{$corePublications.0}"/>
          <input type="hidden" name="from" value="CORE"/>
          <br/>
          <button type="submit">{translate text="order here"} ({translate text="internal only"})</button>
        </form>
*}
      </div>
{/if}


