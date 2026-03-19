# Bühl Landingpage

Eine kleine Symfony-Anwendung, die nach erfolgreicher Keycloak-Anmeldung eine helle, moderne Landingpage mit konfigurierbaren Link-Karten anzeigt.

## Funktionsumfang

- **Pflegbare Landingpage-Links** über `config/packages/app_links.yaml`
- **Konfigurierbarer Header** für Badge, Titel und Beschreibung
- **Keycloak/OpenID-Connect Login** vor der Startseite
- **Keycloak-Logout** inklusive Abmeldung aus der Keycloak-Session
- **Twig + TailwindCSS** für ein modernes, freundliches UI
- **Docker Compose** startet App und Keycloak gemeinsam

## Schnellstart

```bash
docker compose up --build
```

Der PHP-Container installiert beim ersten Start automatisch die Composer-Abhängigkeiten und leert anschließend den Symfony-Cache.

Danach sind die Dienste erreichbar unter:

- Landingpage: <http://localhost:8080>
- Keycloak: <http://localhost:8081>

### Demo-Login

- Benutzername: `demo`
- Passwort: `demo`
- Keycloak Admin: `admin` / `admin`

## Landingpage konfigurieren

Die Texte im oberen Bereich und die Link-Karten werden in `config/packages/app_links.yaml` gepflegt:

```yaml
parameters:
    app.landing_header:
        badge: 'Geschützt via Keycloak SSO'
        title: 'Willkommen auf deinem persönlichen Link-Hub.'
        description: 'Alle Karten werden zentral aus einer Symfony-Konfigurationsdatei geladen.'

    app.landing_links:
        - text: 'Symfony Dokumentation'
          url: 'https://symfony.com/doc'
          color: 'from-sky-500 to-cyan-500'
          icon: 'book-open'
```

## Verfügbare Icons

Du kannst aktuell diese 20 Icons in `icon:` verwenden:

- `book-open`
- `briefcase`
- `calendar`
- `chart-bar`
- `check-circle`
- `cloud`
- `cog`
- `document-text`
- `envelope`
- `folder`
- `globe`
- `heart`
- `home`
- `light-bulb`
- `link`
- `lock-closed`
- `magnifying-glass`
- `rocket-launch`
- `server`
- `shield-check`

Unbekannte Namen fallen automatisch auf ein Plus-Icon zurück.

## Wichtige Umgebungsvariablen

Die Keycloak-Verbindung ist in `.env` vorbelegt und passt zu `docker-compose.yml`:

- `APP_URL`
- `KEYCLOAK_SERVER_URL`
- `KEYCLOAK_PUBLIC_URL`
- `KEYCLOAK_REALM`
- `KEYCLOAK_CLIENT_ID`
- `KEYCLOAK_CLIENT_SECRET`
- `KEYCLOAK_REDIRECT_URI`

## Lokale Entwicklung ohne Docker

Sobald Composer-Abhängigkeiten installiert sind, kann die App auch lokal gestartet werden:

```bash
composer install
php bin/console cache:clear
php -S 127.0.0.1:8000 -t public
```
