<!-- Suchformular fuer Begriffe/Notationen aus Systematik (Autocomplete) -->
<!-- action wird in js gefüllt -->
<form id="autocompleteForm" method=get
      name=form3
      action="{$path}/Search/Results" style="padding-bottom:20px">
<span id="textClassificationSearch">{translate text="search for a classification term"}:</span> <input class="autocomplete" size=40 name="lookfor" value="">
<img id="ClassificationSearchButton" src="{$path}/interface/themes/mpg/images/rdg/go-inactive.png" width="25px" height="25px" style="vertical-align:middle"/>
 <!-- Submit-Button erst nach Auswahl aktivieren (s. js) -->        
<!-- 
<input id="ClassificationSearchButton"
               type="image"
               alt="OK"
               title="Suche starten"
               src="{$path}/interface/themes/mpg/images/rdg/go.png" width="25px" height="25px"
               border="0">
-->
<input type="hidden" name="type" value="classification_local_short_txt_mv">
<input type="hidden" name="view" value="list">
</form>
{* set current language for js *}
<span id="userLang" style="display:none">{$userLang}</span> 
{js filename="classification-autocomplete.js"}
<!-- Ende Suchformular -->


<!-- Systematik als Funktion aufrufen und aktuelle Sprache uebergeben -->
{classification language=$userLang}
