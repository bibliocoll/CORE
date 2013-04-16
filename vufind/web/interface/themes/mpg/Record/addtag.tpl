<form onSubmit='SaveTag(&quot;{$id|escape}&quot;, this,
    {literal}{{/literal}success: &quot;{translate text='add_tag_success'}&quot;, load_error: &quot;{translate text='load_tag_error'}&quot;, save_error: &quot;{translate text='add_tag_error'}&quot;{literal}}{/literal}
    ); return false;' method="POST">
  <input type="hidden" name="submit" value="1" />
 {* RDG: JEL ZBW-API Suggestions *}
  <div id="tagnote"><img src="{$path}/interface/themes/mpg/images/rdg/rdg_logo_pur.png" alt="Be a tagging hero!" class="alignleft" width="80"></a>
       {translate text="add_tag_note_jel"}. {translate text="add_tag_note_jel_2"}!<br/><br/>
       <label for="tag">{translate text="Tags"}:</label>
        <select id="tagselect" name="tagselect" size="1">
          <option title="default" value="default">any text</option>
          <option title="jel" value="jel">JEL code</option>
          <option title="stw" value="stw">STW subject headings</option>
        </select>
        <input type="hidden" name="notify" value="collective.core@gmail.com" />
        <input type="text" name="tag" id="tag" value="" size="25" maxlength="25" />&nbsp;<input type="submit" value="{translate text='Save'}" /><br/>
        ({translate text="publicly viewable"})
        <br/><br/>
        {translate text="add_tag_note"}
  </div>
</form>
{* RDG: jquery addtags jel suggestions *}
{js filename="jquery-ui.addtags-jel.js"}
