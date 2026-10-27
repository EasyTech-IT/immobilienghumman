# E-Mail Deliverability Checklist (PHPMailer/SMTP)

## 1) Produktionsvariablen setzen

Setze auf dem Server diese Environment-Variablen:

- IMMOBILIEN_SMTP_HOST
- IMMOBILIEN_SMTP_PORT
- IMMOBILIEN_SMTP_USER
- IMMOBILIEN_SMTP_PASS
- IMMOBILIEN_SMTP_SECURE
- IMMOBILIEN_SMTP_AUTH
- IMMOBILIEN_TURNSTILE_SITE_KEY
- IMMOBILIEN_TURNSTILE_SECRET_KEY
- IMMOBILIEN_AUTH_SECRET
- IMMOBILIEN_DB_HOST
- IMMOBILIEN_DB_PORT
- IMMOBILIEN_DB_USER
- IMMOBILIEN_DB_PASS
- IMMOBILIEN_DB_NAME

## 2) DNS korrekt konfigurieren

Im DNS der Absenderdomain (z. B. immobilienghumman.de):

1. SPF

- TXT @
- Wert (Beispiel, je nach SMTP-Anbieter): v=spf1 include:dein-smtp-anbieter.tld ~all

1. DKIM

- DKIM im E-Mail-Provider aktivieren.
- Die vom Provider bereitgestellten DNS-Eintraege exakt uebernehmen.

1. DMARC

- TXT _dmarc
- Startwert (Beispiel): v=DMARC1; p=none; rua=mailto:dmarc@immobilienghumman.de; fo=1
- Nach Monitoring schrittweise auf p=quarantine oder p=reject anheben.

## 3) SMTP Login und Versandgrenzen pruefen

- SMTP-Login mit den produktiven Zugangsdaten testen.
- Absenderadresse muss zum SMTP-Postfach passen.
- Versandlimits deines Anbieters beachten (pro Stunde/Tag).

## 4) Funktionstest

1. Testanfrage ueber Kontaktformular absenden.
2. Pruefen, ob Anfrage-Mail an `MAIL_TO` ankommt.
3. Pruefen, ob Bestaetigungs-Mail beim Absender ankommt.

## 5) Monitoring

Woechentliche KPIs:

- Bounce-Rate < 2%
- Spam-Rate < 0.1%
- Deferred-Rate beobachten

Wenn Bounce-Rate zu hoch:

- Alte/unzustellbare Empfaenger bereinigen.
- Content/Subject auf Spam-Trigger pruefen.
- SPF, DKIM, DMARC erneut validieren.

## IONOS Vorlage (direkt nutzbar)

Setze diese ENV-Werte:

- `IMMOBILIEN_SMTP_HOST=smtp.ionos.de`
- `IMMOBILIEN_SMTP_PORT=587`
- `IMMOBILIEN_SMTP_SECURE=tls`
- `IMMOBILIEN_SMTP_AUTH=true`
- `IMMOBILIEN_SMTP_USER=dein-postfach@immobilienghumman.de`
- `IMMOBILIEN_SMTP_PASS=DEIN_IONOS_MAILPASSWORT`

Fallback:

- `IMMOBILIEN_SMTP_PORT=465`
- `IMMOBILIEN_SMTP_SECURE=ssl`

IONOS Mailserver-Referenz (laut deinen Angaben):

- IMAP (empfohlen): `imap.ionos.de`
- Posteingang-Port (SSL): `993`
- SMTP (Postausgang): `smtp.ionos.de`
- Postausgang-Port (TLS): `587`
- SMTP-Authentifizierung: `Ja`

Hinweis:

- Die Website nutzt nur SMTP fuer den Versand (PHPMailer). IMAP ist nur relevant fuer dein E-Mail-Programm/Postfachabruf.
