# gbvdaia

Dieses git-Repository enthält den PHP-Quellcode des zentralen DAIA-Servers für
den GBV (<https://daia.gbv.de/>). Der zentrale DAIA-Server bietet gegenüber dem
[PAIA/DAIA-Service] nur *eingeschränkte Funktionalität*, da nicht direkt auf
das Ausleihsystem zuggegriffen wird.

## Funktionsumfang

* Rudimentärer DAIA-Server für ausgewählte PICA-Kataloge (LBS-Bibliotheken)
* Entspricht der DAIA 1.0.0, d.h. keine `message` Felder (DAIA 0.5)
* Elektronischen Publikationen werden nicht unterstützt
* ...

## Installation

Das Repository kann direkt von GitHub geklont und aktualisiert werden:

    $ git clone https://github.com/gbv/gbvdaia.git && cd gbvdaia

gbvdaia benötigt PHP 7 mit der [DOM Extension] und [Multibyte extension] sowie
[composer]. Zusätzliche PHP-Bibliotheken sind in `composer.json` aufgeführt und
werden folgendermaßen installiert:

    $ composer install --no-dev

Anschließend muss ein Apache-Webserver so eingerichtet werden, dass unter der
gewünschten Basis-URL des DAIA-Servers das Verzeichnis `src/` ausgeliefert
wird. Zusätzlich wird das Apache-Modul mod_rewrite benötigt.

## Demo

Eine (langsame) Demo-Instanz ist unter <https://gbvdaia.herokuapp.com/>
verfügbar.

## Einbindung in eigene Programme

Statt den DAIA-Server per HTTP abzufragen kann der Dienst auch lokal
installiert und direkt in PHP aufgerufen werden:

~~~php
$config   = new GBV\DAIA\FileConfig($config_directory);
$service  = new GBV\DAIA\Service($config);
$request  = new DAIA\Request([ 'id' => $id ]);
$response = $service->query($request);  # liefert DAIA\Response oder DAIA\Error
~~~

Das konkrete Verhalten des DAIA-Service hängt von der Konfiguration ab. Bei
Bedarf kann diese statt aus Konfigurationsdateien auch z.B. aus einer Datenbank
bereitgestellt werden (Interface `GBV\DAIA\Config`).


## Entwicklung

[![Build Status](https://travis-ci.org/gbv/gbvdaia.svg?branch=master)](https://travis-ci.org/gbv/gbvdaia)
[![Coverage Status](https://coveralls.io/repos/gbv/gbvdaia/badge.svg?branch=master)](https://coveralls.io/r/gbv/gbvdaia)

## Lizenz

gbvdaia kann unter den Bedingungen der [AGPL] weiterverwendet werden.


[PAIA/DAIA-Service]: https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service
[DOM Extension]: https://secure.php.net/manual/en/book.dom.php
[Multibyte Extension]: https://secure.php.net/manual/en/book.mbstring.php
[composer]: https://getcomposer.org/
[AGPL]: https://de.wikipedia.org/wiki/GNU_Affero_General_Public_License
