<h1>Hilfe zu den Suchoperatoren</h1>

<ul class="HelpMenu">
  <li><a href="#Wildcard Searches">Suche mit Platzhaltern</a></li>
  <li><a href="#Fuzzy Searches">Unscharfe Suche</a></li>
  <li><a href="#Proximity Searches">Suche nach ähnlichen Wörtern</a></li>
  <li><a href="#Range Searches">Bereichssuche</a></li>
  <li><a href="#Boosting a Term">Wort gewichten</a></li>
  <li><a href="#Boolean operators">Boolsche Operatoren</a>
    <ul>
      <li><a href="#AND">AND</a></li>
      <li><a href="#+">+</a></li>
      <li><a href="#OR">OR</a></li>
      <li><a href="#NOT">NOT</a></li>
      <li><a href="#-">-</a></li>
    </ul>
  </li>
</ul>

<dl class="Content">
  <dt><a name="Wildcard Searches"></a>Suche mit Platzhaltern</dt>
  <dd>
    <p>Als Platzhalter für beliebig viele Zeichen verwenden Sie <strong>?</strong>.</p>
    <p>Wenn sie nach "Text" oder "Test" suchen wollen, schreiben Sie </p>
    <pre class="code">Te?t</pre>
    <p>Als Platzhalter für 0 oder mehrere beliebige Zeichen verwenden Sie
       <strong>*</strong>.</p>
    <p>Wenn Sie nach "Test", "Tests" oder "Tester" suchen wollen, schreiben Sie:</p>
    <pre class="code">Test*</pre>
    <p>Platzhalter können an beliebiger Stelle platziert werden:</p>
    <pre class="code">Te*t</pre>
  </dd>
  
  <dt><a name="Fuzzy Searches"></a>Unscharfe Suche</dt>
  <dd>
    <p>Verwenden Sie für die unscharfe Suche die Tilde (<strong>~</strong>) am
       Ende eines Wortes.
       Wenn Sie nach Wörtern suchen wollen, die ähnlich wie "Raum" geschrieben werden, schreiben Sie:</p>
    <pre class="code">Raum~</pre>
    <p>Sie erhalten dann auch Treffer mit ähnlicher Zeichenfolge wie "Baum" oder "Rahm".</p>
    <p>Sie können den Ähnlichkeitsgrad varriieren, indem Sie einen Wert zwischen 0 und 1 hinter die Tilde setzen. Je kleiner der Wert, desto unschärfer wird dabei die Suche. 
			Beispiel:</p>
    <pre class="code">roam~0.8</pre>
		<p>Wenn Sie nichts angeben, wird der Wert automatische auf 0.5 gesetzt.</p>
  </dd>
  
  <dt><a name="Proximity Searches"></a>Bereichssuche 1</dt>
  <dd>
    <p>
			Für die Bereichssuche setzen Sie die Tilde (<strong>~</strong>) hinter eine
      Wortgruppe. Wenn Sie nach den Begriffen "Ökonomie" und "Keynes"
      suchen wollen, dabei aber lediglich Treffer erhalten wollen, bei
      denen diese Begriffe höchstens zehn Wörter von einander entfernt
      sind, schreiben Sie:  
		</p>
    <pre class="code">"Ökonomie Keynes"~10</pre>
  </dd>
  
  {literal}
  <dt><a name="Range Searches"></a>Bereichssuche 2</dt>
  <dd>
    <p>
      Für die Bereichssuche verwenden Sie geschweifte Klammern
      (<strong>{ }</strong>).
			Wenn Sie nur nach Publikationen aus den Jahren 2002 bis 2003 suchen wollen, schreiben Sie 
    </p>
    <pre class="code">{2002 TO 2003}</pre>
  </dd>
  {/literal}
  
  <dt><a name="Boosting a Term"></a>Wörter gewichten</dt>
  <dd>
    <p>
      Sie können einzelenen Suchbegriffen eine höhere Relevanz
      zuweisen. Verwenden Sie hierzu das Caret-Zeichen (<strong>^</strong>). Beispiel:
    </p>
    <pre class="code">economics Keynes^5</pre>
    <p>Dadurch wird das Wort "Keynes" in der Ergebnisliste stärker gewichtet als das Wort "economics".
    </p>
  </dd>

  <dt><a name="Boolean operators"></a>Boolesche Operatoren</dt>
  <dd>
    <p>
			Mithilfe von Boolschen Operatoren können Sie Suchbegriffe
      logisch miteinander verknüpfen. Folgende Operatoren können Sie
      benutzen: <strong>AND</strong>, <strong>+</strong>, <strong>OR</strong>,
      <strong>NOT</strong> und <strong>-</strong>. Hinweis: Schreiben Sie
      Boolsche Operatoren stets groß.  
    </p>
    <dl>
      <dt><a name="AND"></a>AND</dt>
      <dd>
        <p>Die Und-Verknüpfung (<strong>AND</strong>) ist der
            Standardoperator. Wird also zwischen zwei Suchbegriffen
            kein Operator gesetzt, wird automatisch die
            Und-Verknüpfung verwendet. Wenn Sie zwei Suchbegriffe mit
            AND verbinden, erhalten Sie nur Treffer, in denen beide
            Begriffe vorhanden sind. Wenn Sie nach nach Titeln suchen,
            in denen die Suchbegriffe "economics" und "Keynes"
            enthalten sind, schreiben Sie:
        </p>
        <pre class="code">economics Keynes</pre>
        <p>oder</p>
        <pre class="code">economics AND Keynes</pre>
      </dd>      
      <dt><a name="+"></a>+</dt>
      <dd>
        <p>Wenn Sie den "<strong>+</strong>"-Operator vor ein Wort setzen, erhalten Sie Treffer, in denen dieses Wort vorhanden ist.
        </p>
        <p>Beispiel:</p>
        <pre class="code">+economics +Keynes</pre>
      </dd>
      <dt><a name="-"></a>-</dt>
      <dd>
        <p>Wenn Sie den "<strong>-</strong>"-Operator vor ein Wort
           setzten, erhalten Sie Treffer, in denen dieses Wort nicht
           vorhanden ist. Beispiel:</p>
        <pre class="code">economics -Keynes</pre>
      </dd>
      <dt><a name="OR"></a>ODER</dt>
      <dd>
        <p>Steht eine Oder-Verknüpfung (<strong>OR</strong>) zwischen
        zwei Suchbegriffen, erhalten Sie Treffer, in denen mindestens
        eines der beiden Wörter gefunden wurde. Wenn Sie nach Titeln
        suchen, in denen die Begriffe "economics Keynes" oder "Keynes"
        enthalten sind, schreiben Sie: 
        </p>
        <pre class="code">"economics Keynes" OR Keynes</pre>
      </dd>
      <dt><a name="NOT"></a>NOT</dt>
      <dd>
        <p>Indem Sie <strong>NOT</strong> hinter ein Wort setzen, schließen Sie Treffer aus,
           welche dieses Wort enthalten.</p>
        <p>Wenn Sie nach Titeln suchen wollen, die das Wort
           "economics" enthalten aber nicht das Wort "Keynes",
           schreiben Sie:</p>
        <pre class="code">economics NOT Keynes</pre>
        <p>Hinweis: <strong>NOT</strong> kann nur in Verbindung mit
           mindestens zwei Suchbegriffen verwendet werden. Eine Suche
           nach <pre class="code">NOT economics</pre> liefert keine Treffer. 
      </dd>

    </dl>
  </dd>
</dl>
