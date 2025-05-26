# Krdano Kontakt Splitter

Der Krdano-Kontakt-Splitter (Karte+Tornado) erkennt und extrahiert sämtliche relevanten Informationen aus dem Namen auf einer gescannten Visitenkarte und generiert daraus automatisch die korrekte Briefanrede.

## Installation

- Umgebung
    - Für die Laravel Applikation werden PHP, Composer (siehe [Laravel Docs](https://laravel.com/docs/12.x/installation#installing-php)) und npm (siehe [Node Docs](https://nodejs.org/en/download)) benötigt.
- Installation & Starten
    - Zur Installation und Einrichtung aller für den Laravel-Service relevanten Abhängigkeiten kann folgender Befehl genutzt werden:
      ```bash
      composer setup
      ```
    - Um die Laravel Anwendung zu starten:
      ```bash
      composer dev
      ```
    - Die Anwendung ist nun unter [localhost:8000](http://localhost:8000) erreichbar.
        - Es wird ein default-User angelegt mit der E-Mail-Adresse **admin@krdano.de** und dem Passwort **kartentornado**.

