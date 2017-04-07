# gbvdaia

Dieses git-Repository enthält den PHP-Quellcode des zentralen DAIA-Servers für
den GBV (<https://daia.gbv.de/>). Der zentrale DAIA-Server bietet gegenüber dem
[PAIA/DAIA-Service] nur *eingeschränkte Funktionalität*, da nicht direkt auf
das Ausleihsystem zugegriffen wird.

Unterstützt werden lediglich rudimentäre Funktionen für ausgewählte
PICA-Kataloge (LBS-Bibliotheken).  Der genaue Funktionsumfang mit Unterschieden
zum PAIA/DAIA-Service und zum alten DAIA-Server ist auf der Startseite
beschrieben.

[PAIA/DAIA-Service]: https://www.gbv.de/Verbundzentrale/serviceangebote/paia-service


## Demo

Eine (ggf. etwas langsame) Demo-Instanz ist in der Regel unter
<https://gbvdaia.herokuapp.com/> verfügbar.


## Installation

Das Repository kann direkt von GitHub geklont und aktualisiert werden:

    $ git clone https://github.com/gbv/gbvdaia.git && cd gbvdaia

gbvdaia benötigt PHP 7 mit der [DOM Extension] und [Multibyte extension] sowie
[composer]. Unter Ubuntu 16.04 können die entsprechenden Pakete folgendermaßen
installiert werden:

    $ sudo apt-get install composer php7.0 php-xml php-mbstring

Zusätzlich benötigte PHP-Bibliotheken sind in `composer.json` aufgeführt und
werden folgendermaßen installiert:

    $ composer install --no-dev

Zum Testen kann der DAIA-Server auch auf der Kommandozeile gestartet werden:

    $ php -S localhost:8080 public/index.php

[DOM Extension]: https://secure.php.net/manual/en/book.dom.php
[Multibyte Extension]: https://secure.php.net/manual/en/book.mbstring.php
[composer]: https://getcomposer.org/


### Apache unter Ubuntu

Hier die Schritte zur Installation unter Apache2 unter Ubuntu:

    $ sudo apt-get install libapache2-mod-php7.0

Unter `/etc/apache2/sites-available/gbvdaia.conf` ist eine Konfigurationsdatei
einzurichten, die das Verzeichnis `public/` ausliefert, z.B:

    <VirtualHost *:80>
        ServerName daia.gbv.de
        ServerSignature Off
        DocumentRoot /var/www/gbvdaia/public
        <Directory /var/www/gbvdaia/public>
            AllowOverride All 
            Require all granted
        </Directory>
    </VirtualHost>

Anschließend:

    $ sudo a2ensite gbvdaia
    $ sudo a2enmod rewrite
    $ sudo service apache2 restart


## Konfiguration

Die Datei `public/config.php` wird, falls vorhanden, eingelesen um grundlegende
Konfigurationswerte zu setzen. Siehe `public/config.example.php` für ein Beispiel.

Die eigentliche Konfiguration befindet sich in Konfigurationsverzeichnis, das
standardmäßig in einem Git-Repository verwaltet wird.

## Einbindung in eigene Programme

Statt den DAIA-Server per HTTP abzufragen kann der Dienst auch lokal
installiert und direkt in PHP aufgerufen werden:

~~~php
require 'vendor/autoload.php';

$config   = new GBV\DAIA\FileConfig($config_directory);
$server   = new GBV\DAIA\Server($config, $logger);
$request  = new DAIA\Request([ 'id' => $id ]);
$response = $server->query($request);  # liefert DAIA\Response oder DAIA\Error
~~~

Das konkrete Verhalten des DAIA-Service hängt von der Konfiguration ab. Bei
Bedarf kann diese statt aus Konfigurationsdateien auch z.B. aus einer Datenbank
bereitgestellt werden (Interface `GBV\DAIA\Config`).


## Entwicklung

[![Build Status](https://travis-ci.org/gbv/gbvdaia.svg?branch=master)](https://travis-ci.org/gbv/gbvdaia)
[![Coverage Status](https://coveralls.io/repos/gbv/gbvdaia/badge.svg?branch=master)](https://coveralls.io/r/gbv/gbvdaia)


## Lizenz

gbvdaia kann unter den Bedingungen der [AGPL] weiterverwendet werden.

[AGPL]: https://de.wikipedia.org/wiki/GNU_Affero_General_Public_License
