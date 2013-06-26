      {foreach from=$abrufzeichen item=az}
        {if $az == "LitRatio2013"}
        <div class="abruf hide-for-results-list">
         <a href="{$url}/Search/Results?lookfor=litratio2013&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2013.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {elseif $az == "LitBonnerRunde"}
        <div class="abruf">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-monday-seminar.png"></img>
        </div>
        {/if}
        {if $az == "newbook"}
        <div class="abruf">
         <a href="http://intern.coll.mpg.de/library/page/service-alerts">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-new.png" class="tooltip" title="{translate text='Browse all recent acquisitions of the library'} ({translate text='internal only'})"></img>
         </a>
        </div>
        {/if}
        {if $az == "LitRatio-SocialPreferences"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=litratio-socialpreferences&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-social.png" class="tooltip" title="Ratio Literature: Social Preferences"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Trust"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=litratio-trust&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-trust.png" class="tooltip" title="Ratio Literature: Trust"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Voting"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=litratio-voting&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-voting.png" class="tooltip" title="Ratio Literature: Voting"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Norms"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=litratio-norms&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-norms.png" class="tooltip" title="Ratio Literature: Norms"></img>
	     </a>
        </div>
        {/if}
        {if $az == "AwrdPbl"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=AwrdPbl&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-awarded.png" class="tooltip" title="Awarded Publication"></img>
	     </a>
        </div>
        {/if}
      {/foreach}
