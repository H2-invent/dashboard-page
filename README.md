# Bühl Landingpage

Eine kleine Symfony-Anwendung, die nach erfolgreicher Keycloak-Anmeldung eine helle, moderne Landingpage mit konfigurierbaren Link-Karten anzeigt.

## Funktionsumfang

- **Pflegbare Landingpage-Links** über `config/packages/app_links.yaml`
- **Konfigurierbarer Header** für Organisationslogo, Badge, Titel und Beschreibung
- **Kleine Website-Logos pro Kachel** über eine Bild-URL in der Konfiguration
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

Die Texte im oberen Bereich, das Organisationslogo und die Link-Karten werden in `config/packages/app_links.yaml` gepflegt:

```yaml
parameters:
    app.landing_header:
        logo: 'https://www.google.com/s2/favicons?sz=128&domain_url=https://example.org'
        logo_alt: 'Organisationslogo'
        badge: 'Geschützt via Keycloak SSO'
        title: 'Willkommen auf deinem persönlichen Link-Hub.'
        description: 'Alle Karten werden zentral aus einer Symfony-Konfigurationsdatei geladen.'

    app.landing_links:
        - text: 'Symfony Dokumentation'
          url: 'https://symfony.com/doc'
          color: 'from-sky-500 to-cyan-500'
          image: 'https://www.google.com/s2/favicons?sz=64&domain_url=https://symfony.com'
```

### Organisationslogo im Header

Für das Logo deiner Organisation im oberen Bereich setzt du einfach in `app.landing_header.logo` eine Bild-URL oder einen lokalen Asset-Pfad.

Beispiele:

- Externe Bild-URL: `https://example.com/logo.png`
- Lokale Datei: `/images/organisation-logo.svg`
- Favicon-artiges Logo: `https://www.google.com/s2/favicons?sz=128&domain_url=https://example.org`

Über `logo_alt` kannst du den Alternativtext anpassen.

### Bild-Logos pro Kachel

Jede Kachel kann mit einem kleinen Website-Logo versehen werden. Dafür gibst du einfach eine Bild-URL in `image:` an.

Beispiele:

- Favicon über Google S2: `https://www.google.com/s2/favicons?sz=64&domain_url=https://example.com`
- Eigenes Logo: `https://example.com/logo.png`
- Lokale Datei im Projekt: `/images/logo.svg`

Falls du aus Kompatibilitätsgründen noch `icon:` statt `image:` verwendest, wird der bisherige Icon-Fallback weiterhin unterstützt.

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
