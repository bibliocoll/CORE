      {foreach from=$abrufzeichen item=az}
        {if $az == "newbook"}
        <div class="abruf">
         <a href="http://intern.coll.mpg.de/library/page/service-alerts">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-new.png" class="tooltip" title="{translate text='Browse all recent acquisitions of the library'} ({translate text='internal only'})"></img>
         </a>
        </div>
        {/if}
        {if $az == "LitRatioAllgemein"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatioAllgemein&type=AllFields&submit=Find">
           <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-misc.png" class="tooltip" title="Ratio Literature: Miscellaneous"></img>
         </a>
        </div>
        {/if}
        {if $az == "LitRatio-SocialPreferences"}
        {assign var="LitRatioSubject" value="1"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio-SocialPreferences&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-social.png" class="tooltip" title="Ratio Literature: Social Preferences"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Trust"}
        {assign var="LitRatioSubject" value="1"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio-Trust&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-trust.png" class="tooltip" title="Ratio Literature: Trust"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Voting"}
        {assign var="LitRatioSubject" value="1"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio-Voting&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-voting.png" class="tooltip" title="Ratio Literature: Voting"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio-Norms"}
        {assign var="LitRatioSubject" value="1"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=Litratio-Norms&type=AllFields&submit=Find">
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
        {if $az == "ThReGrp"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=ThReGrp&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-theory.png" class="tooltip" title="Theory Reading Group"></img>
	     </a>
        </div>
        {/if}
        {if $az == "IntuitExp"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=IntuitExp&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-intuit.png" class="tooltip" title="Intuitive Experts"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio2013"} {* only for full view (see also below) *}
        <div class="abruf hide-for-results-list">
         <a href="{$url}/Search/Results?lookfor=LitRatio2013&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2013.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio2007"} {* only for full view (see also below) *}
        <div class="abruf hide-for-results-list">
         <a href="{$url}/Search/Results?lookfor=LitRatio2007&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2007.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio2006"} {* only for full view (see also below) *}
        <div class="abruf hide-for-results-list">
         <a href="{$url}/Search/Results?lookfor=LitRatio2006&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2006.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitBonnerRunde"}
        <div class="abruf">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-monday-seminar.png"></img>
        </div>
        {/if}
      {/foreach}

 {* some awkward display logic: only for results list ($summAuthor): show, if data is a single "LitRatio[YYYY]" *}
      {if !empty($summAuthor) && !$LitRatioSubject}
        {if $az == "LitRatio2013"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio2013&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2013.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio2007"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio2007&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2007.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
        {if $az == "LitRatio2006"}
        <div class="abruf">
         <a href="{$url}/Search/Results?lookfor=LitRatio2006&type=AllFields&submit=Find">
	   <img src="{$path}/interface/themes/mpg/images/rdg/abruf-ratio-2006.png" class="tooltip" title="Ratio Literature Seminar 2013"></img>
	     </a>
        </div>
        {/if}
      {/if}
