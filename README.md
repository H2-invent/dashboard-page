# Bühl Landingpage

Eine kleine Symfony-Anwendung, die nach erfolgreicher Keycloak-Anmeldung eine helle, moderne Landingpage mit konfigurierbaren Link-Karten anzeigt.

## Funktionsumfang

- **Pflegbare Landingpage-Links** über `config/packages/app_links.yaml`
- **Keycloak/OpenID-Connect Login**, bevor die Startseite erreichbar ist
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

## Links konfigurieren

Die Link-Karten werden in `config/packages/app_links.yaml` gepflegt:

```yaml
parameters:
    app.landing_links:
        - text: 'Symfony Dokumentation'
          url: 'https://symfony.com/doc'
          color: 'from-sky-500 to-cyan-500'
          icon: 'book-open'
```

### Unterstützte Icons

Aktuell sind diese Icon-Namen hinterlegt:

- `book-open`
- `lock-closed`
- `rocket-launch`

Unbekannte Namen fallen automatisch auf ein Plus-Icon zurück.

## Wichtige Umgebungsvariablen

Die Keycloak-Verbindung ist in `.env` vorbelegt und passt zu `docker-compose.yml`:

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
