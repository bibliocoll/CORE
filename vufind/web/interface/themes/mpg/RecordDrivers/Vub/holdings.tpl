{*RDG*} 

      <div id="vub" class="othercontent">
        {translate text="Are you interested in this title"}? 
        {translate text="Feel free to send us a purchase suggestion"}! 
        <form id="vubform" action="http://intern.coll.mpg.de/node/3160/" method="get">
          <input type="hidden" name="title" value="{$coreShortTitle}"/>
          <input type="hidden" name="author" value="{$coreMainAuthor}"/>
          <input type="hidden" name="isbn" value="{$isbn}"/>
          <input type="hidden" name="publications" value="{$corePublications.0}"/>
          <input type="hidden" name="from" value="CORE"/>
          <br/>
          <button type="submit">{translate text="order here"} ({translate text="internal only"})</button>
        </form>
      </div>



