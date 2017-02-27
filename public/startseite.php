<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>daia.gbv.de</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-vzg.css">
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
			<span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">daia.gbv.de</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <!--li class="active"><a href="#">Introduction</a></li-->
            <li><a href="http://purl.org/NET/DAIA">Spezifikation</a></li>
            <li><a href="https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service">PAIA-Service</a></li>
            <!--li><a href="method2/">method2</a></li>
			<li><a href="contact/">Contact</a></li-->
          </ul>
        </div>
      </div>
    </nav>

    <div class="jumbotron">
      <div class="container">
        <h1>GBV DAIA</h1>
        <p>Zentraler DAIA-Server für den Gemeinsamen Bibliotheksverbund (GBV)</p>
      </div>
    </div>

    <div class="container">
      <h2>Hintergrund</h2>
	  <p>
        Der zentrale DAIA-Servers für den GBV ermöglicht unter
        <a href="https://daia.gbv.de/">daia.gbv.de</a> 
        Verfügbarkeitsabfragen per
        <a href="http://purl.org/NET/DAIA">Document Availability Information API (DAIA)</a>.
        Der Dienst bietet lediglich <a href="#limitations">rudimenträre Abfragemöglichkeit</a>, 
        da nicht direkt auf die Ausleihsysteme der unterstützen Bibliotheken zugegriffen wird.
        Für vollständige Funktionalität wird der
        <a href="https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service">PAIA-Service</a>
        benötigt.
      </p>
      <h2>API</h2>
      <p>
        Die Abfrageparameter <code>id</code> setzt sich zusammen aus:
		<table class="table">
        <tr>
          <td><code>prefix</code></td>
          <td>Dem optionalen URI-Präfix <code>http://uri.gbv.de/document/</code></td>
        </tr><tr>
          <td><code>dbkey</code></td>
          <td>Dem Datenbankkürzel 
              (siehe <a href="https://uri.gbv.de/database/">uri.gbv.de/database</a>)</td>
        </tr><tr>
          <td><code>PPN</code></td>
          <td>Der PICA-Produktionsnummer (PPN) des abzufragenden Titeldatensatz</td>
        </tr>
        </table>
      </p>
      <h3>Basis-URL</h3>
      <p>
        <code>https://daia.gbv.de/isil/<b>{ISIL}</b>?id=<b>{prefix}{dbkey}:ppn:{PPN}</b>&amp;format=json</code>
      </p>
	  <form method="get" class="well form-inline">
		<input type="text" name="id" class="form-control">
		<input type="hidden" name="format" value="json">
		<button type="submit" class="btn btn-success">Abfrage</button>
	  </form>
      <h3>Basis-URLs einzelner Bibliotheken</h3>
      <p>
        Für ausgewählte Bibliotheken:
      </p>
      <p>
        <code>https://daia.gbv.de/isil/<b>{ISIL}</b>?id=<b>{prefix}{dbkey}:ppn:{PPN}</b>&amp;format=json</code>
        <br> oder
        <code>https://daia.gbv.de/isil/<b>{ISIL}</b>?id=<b>ppn:{PPN}</b>&amp;format=json</code>
      </p>
      <p>
        Dabei ist <code>ISIL</code> der International Standard Identifier for Libraries
        and Related Organizations und Informationen zur Bibliothek müssen unter
        <code>http://uri.gbv.de/organization/isil/<b>{ISIL}</b></code> abrufbar sein.
      </p>

      <h2>Beispiele</h2>
      <ul>
        <li>
          <a href="?id=opac-de-hil2:ppn:16523315X">opac-de-hil2:ppn:16523315X</a>
        </li>
        <li>
          <a href="isil/DE-Hil2?id=ppn:16523315X">ppn:16523315X</a>
          mit Basis-URL <code>/isil/DE-Hil2</code>
        </li>
        <li>
          <a href="?id=http://uri.gbv.de/document/opac-de-830:ppn:540319058">http://uri.gbv.de/document/opac-de-830:ppn:540319058</a>
       </li>
      </ul>

      <h2>Konfiguration</h2>
      <p>
        Informationen zur Konfiguration folgen noch.
        <!--
          All configuration files are available at GitHub.
          See /config for general configuration and /isil/{ISIL}/config 
          for full configuration of a specific library.
        -->
      </p>

      <!--h2>DAIA-Konverter und Validator</h2>
      <p>
        kommt noch (XML, JSON Schema, RDF).
      </p-->

      <h2>Einschränkungen und Unterschiede</h2>
      <p>
        Gegenüber dem
        <a href="https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service">PAIA-Service</a>:
        <ul>
          <li>Keine vollständige Übereinstimmung mit dem Ausleihsystem, vor allem bei
              Spezialfällen wie Bandlisten, Zeitschriften etc.</li>
          <li>Keine Unterstützung von elektronischen Publikationen</li>
          <li>Keine Abfrage per EPN oder Barcode</li>
          <li>...</li>
        </ul>
        Gegenüber dem alten DAIA-Server (2009-2017):
        <ul>
          <li>DAIA 1.0.0 statt DAIA 0.5, d.h. keine <code>message</code> Felder</li>
          <li>Keine Abfrage per EPN, Barcode, ISBN, DOI oder EKI</li>
		  <li>...</li>
        </ul>
      </p>
    </div>

    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-10 text-muted">
  	        Ein Dienst der 
            <a href="https://www.gbv.de/impressum">Verbundzentrale des GBV (VZG)</a>
          </div>
          <div class="col-md-2 text-right text-muted">
            <a href="https://github.com/gbv/gbvdaia">sources</a>
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>
