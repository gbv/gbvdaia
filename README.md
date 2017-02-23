# gbvdaia

Dieses git-Repository enthält den PHP-Quellcode des zentraler DAIA-Server für
den GBV. Der zentrale DAIA-Server bietet gegenüber dem [PAIA/DAIA-Service] nur
*eingeschränkte Funktionalität*, da er nicht direkt auf das Ausleihsystem
zugreift.

## Installation

gbvdaia benötigt PHP 7 und [composer]. Zusätzliche PHP-Bibliotheken sind in
`composer.json` aufgeführt und werden folgendermaßen installiert:

    $ composer install --no-dev

Anschließend muss ein Apache-Webserver so eingerichtet werden, dass unter der
gewünschten Basis-URL des DAIA-Servers das Verzeichnis `src/` ausgeliefert
wird.

## Demo

Eine (langsame) Demo-Instanz ist möglicherweise unter
<https://gbvdaia.herokuapp.com/> verfügbar.

## Einbindung in eigene Programme

~~~php
use DAIA\Request;
use GBV\DAIAService;

$service  = new DAIAService();
$request  = new Request(['id'=>$id]);
$response = $service->query($request);   # DAIA\Response oder DAIA\Error
~~~

## Entwicklung

[![License](https://poser.pugx.org/gbv/gbvdaia/license)](https://packagist.org/packages/gbv/gbvdaia)
[![Build Status](https://travis-ci.org/gbv/gbvdaia.svg?branch=master)](https://travis-ci.org/gbv/gbvdaia)
[![Coverage Status](https://coveralls.io/repos/gbv/gbvdaia/badge.svg?branch=master)](https://coveralls.io/r/gbv/gbvdaia)

## Lizenz

gbvdaia kann unter den Bedingungen der AGPL weiterverwendet werden.

[PAIA/DAIA-Service]: https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service
[composer]: https://getcomposer.org/
