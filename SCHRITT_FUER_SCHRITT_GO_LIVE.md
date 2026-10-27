# Schritt-fuer-Schritt: Domain-Umzug + Turnstile + PHPMailer SMTP

Diese Seite ist in der richtigen Reihenfolge aufgebaut:

1. Erst das Wichtige (Pflicht)
2. Danach das Weitere (Optional)

---

## Teil A: Pflicht (zuerst machen)

## Schritt 1: DNS bei IONOS richtig setzen

Was du machst:

- A-Record fuer `@` auf die Ziel-Server-IP setzen.
- `www` als CNAME auf `@` setzen (oder eigener A-Record auf gleiche IP).

Warum das wichtig ist:

- Ohne DNS zeigt die Domain nicht auf deinen Server.

Pruefung:

- `www.immobilienghumman.de` muss deinen Server erreichen.

Weiter wenn:

- Domain auf den richtigen Server zeigt.

---

## Schritt 2: SSL/TLS aktivieren

Was du machst:

- Zertifikat fuer beide Hosts aktivieren:
  - `immobilienghumman.de`
  - `www.immobilienghumman.de`

Warum das wichtig ist:

- HTTPS ist Pflicht fuer sichere Formulare und SEO.

Pruefung:

- Beide URLs laden ohne Browser-Warnung.

Weiter wenn:

- HTTPS sauber funktioniert.

---

## Schritt 3: Projekt in den Live-Webroot deployen

Was du machst:

- Aktuellen Projektstand hochladen.
- Sicherstellen, dass `.htaccess` mit deployed ist.

Warum das wichtig ist:

- Dort stehen Redirects und Sicherheitsregeln.

Pruefung:

- Startseite laedt.
- Keine 500-Fehler.

Weiter wenn:

- Seite stabil erreichbar ist.

---

## Schritt 4: Turnstile (CAPTCHA) anlegen

Was du machst:

- In Cloudflare Turnstile Widget erstellen (Managed Challenge).
- Domains freigeben:
  - `www.immobilienghumman.de`
  - `immobilienghumman.de`
- Site Key und Secret Key kopieren.

Warum das wichtig ist:

- Sonst bleibt Spam-Schutz inaktiv.

Pruefung:

- Du hast beide Keys vorliegen.

Weiter wenn:

- Site + Secret Key vorhanden sind.

---

## Schritt 5: Keys in Konfiguration eintragen

Datei:

- [includes/config.local.php](includes/config.local.php)

Was du eintraegst:

- `IMMOBILIEN_TURNSTILE_SITE_KEY`
- `IMMOBILIEN_TURNSTILE_SECRET_KEY`
- `IMMOBILIEN_SMTP_HOST`
- `IMMOBILIEN_SMTP_PORT`
- `IMMOBILIEN_SMTP_USER`
- `IMMOBILIEN_SMTP_PASS`
- `IMMOBILIEN_SMTP_SECURE`
- `IMMOBILIEN_SMTP_AUTH`

Warum das wichtig ist:

- Erst damit sind Turnstile und zuverlaessige Mailzustellung live aktiv.

Pruefung:

- Platzhalter sind ersetzt.

Weiter wenn:

- Konfigurationswerte gesetzt sind.

---

## Schritt 6: Redirects testen

Test-URLs:

- `http://immobilien-ghumman.de/`
- `https://immobilien-ghumman.de/`
- `http://immobilienghumman.de/`
- `https://immobilienghumman.de/`

Soll:

- Alle Varianten leiten auf `https://www.immobilienghumman.de/`.

Warum das wichtig ist:

- Verhindert SEO-Verluste und doppelte Inhalte.

Weiter wenn:

- Weiterleitungen korrekt sind.

---

## Schritt 7: Formulare + Turnstile testen

Was du testest:

- Homepage-Formular.
- Mindestens 2 weitere Formulare (z. B. Hausverwaltung, Finanzierung).

Soll:

- CAPTCHA ist sichtbar.
- Ohne geloestes CAPTCHA darf kein Versand durchgehen.
- Mit geloestem CAPTCHA klappt Versand.
- Test-Mail kommt an (`info@immobilienghumman.de`).

## Schritt 8: SMTP Deliverability absichern

Was du machst:

- SPF im DNS passend zu deinem SMTP-Provider setzen.
- DKIM im SMTP-Provider aktivieren.
- DMARC als TXT setzen (`p=none` zum Start, spaeter anheben).

Warum das wichtig ist:

- Ohne SPF/DKIM/DMARC landen Mails eher im Spam.
- Ohne SPF/DKIM/DMARC sinkt die Zustellrate deutlich.

Warum das wichtig ist:

- Kernfunktion (Leads) muss stabil funktionieren.

Pflichtteil fertig wenn:

- Alle Formularchecks erfolgreich sind.

---

## IONOS Schnellkonfiguration (SMTP)

Empfohlene IONOS SMTP-Werte:

- `IMMOBILIEN_SMTP_HOST=smtp.ionos.de`
- `IMMOBILIEN_SMTP_PORT=587`
- `IMMOBILIEN_SMTP_SECURE=tls`
- `IMMOBILIEN_SMTP_AUTH=true`
- `IMMOBILIEN_SMTP_USER=dein-postfach@immobilienghumman.de`
- `IMMOBILIEN_SMTP_PASS=DEIN_IONOS_MAILPASSWORT`

Serverdaten (IONOS):

- IMAP: `imap.ionos.de` mit SSL-Port `993`
- SMTP: `smtp.ionos.de` mit TLS-Port `587`
- SMTP-Authentifizierung: `Ja`

Alternative (falls 587 geblockt ist):

- Port `465`
- `IMMOBILIEN_SMTP_SECURE=ssl`

Wichtige Hinweise:

- `MAIL_FROM` sollte identisch mit dem IONOS-Postfach (`SMTP_USER`) sein.
- Im IONOS-Postfach muss SMTP/IMAP aktiv sein.
- SPF, DKIM und DMARC fuer die Versanddomain im DNS setzen.

---

## Teil B: Danach (optional, aber sinnvoll)

## Schritt 9: Search Console + Sitemap

Was du machst:

- Property fuer `https://www.immobilienghumman.de` in Google Search Console bestaetigen.
- Sitemap einreichen:
  - `https://www.immobilienghumman.de/sitemap.xml`

Nutzen:

- Schnellere und sauberere Indexierung.

---

## Schritt 10: 1-2 Wochen Monitoring

Was du beobachtest:

- Crawl-Fehler
- Redirect-Fehler
- Formular-Fehler
- Mailzustellung
- Bounce-/Drop-Events aus `mail_events`

Nutzen:

- Fruehes Erkennen von Restproblemen.

---

## Mini-Checkliste zum Abhaken

- [ ] DNS korrekt
- [ ] SSL aktiv
- [ ] Deployment inkl. `.htaccess`
- [ ] Turnstile Keys gesetzt
- [ ] SMTP-Zugangsdaten gesetzt
- [ ] SPF/DKIM/DMARC aktiv
- [ ] Redirects korrekt
- [ ] Formulare + CAPTCHA + Mail erfolgreich
- [ ] (Optional) Search Console + Sitemap
- [ ] (Optional) Monitoring gestartet
