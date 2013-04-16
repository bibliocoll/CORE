      {foreach from=$abrufzeichen item=az}
        {if $az == "LitRatio2013"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=litratio2013&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2013.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	 </a>
        </div>
        {elseif $az == "LitBonnerRunde"}
        <div class="abruf">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-monday-seminar.png"></img>
        </div>
        {elseif $az == "newbook"}
        <div class="abruf">
         <a href="http://intern.coll.mpg.de/library/page/service-alerts">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-new.png" class="tooltip" title="{translate text='Browse all recent acquisitions of the library'} ({translate text='internal only'})"></img>
         </a>
        </div>
        {/if}
      {/foreach}