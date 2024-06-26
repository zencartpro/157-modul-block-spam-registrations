# 157-modul-block-spam-registrations
Spam Registrierungen blockieren für Zen Cart 1.5.7 deutsch 

## Sinn und Zweck ##

Falls Sie in Ihrem Zen Cart Shop immer wieder Spamregistrierungen bekommen, wo die Nachnamen und Vornamen der Kunden aus wirren Buchstabenfolgen in Groß- und Kleinschreibung bestehen, können Sie solchen Spam mit diesem Modul wirksam blocken.
Typisches Beispiel einer solchen Spam Registrierung:

* Vorname: ygjcCWbBKQOVl
* Nachname: PtODZTvjnkqxor
* Strasse: hyHWjMmrwNFvO
* PLZ: ogicIaZeRKhn
* Ort: oGuVRETjNq

## Funktionsweise ##
* Dieses Modul nimmt anhand der bekannten Spam Merkmale eine Bewertung vor und legt bei solchen Registrierungen kein Kundenkonto an.
* Die Anzahl der für eine Spam Bewertung erforderlichen Großbuchstaben und die zu prüfenden Felder können in der Shopadministration unter Konfiguration > Antispam für Kundenkonto angepasst werden.
* Mit den voreingestellten Standarwerten für Vorname, Nachname, Ort und Adresszeile 2 sollte man aber solchen Spam wirksam blocken können ohne dass zuviele echte Kunden betroffen sind.
* Als Spam erkannte Kundenkonten sind in der Shopadministration unter Kunden > Spam Registrierungen ersichtlich.
* Sollte sich ein echter Kunde mal hierher verirren, kann er dort einfach freigeschaltet werden.
* Wie lange Spamkunden dort gespeichert bleiben kann via Shopadministration unter Konfiguration > Antispam für Kundenkonto eingestellt werden.

## Credits ##
based on ZX Antispam and Email Verification by ZenExpert - https://zenexpert.com