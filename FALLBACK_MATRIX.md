# Fallback-Matrix

Status: 2026-07-01

Legende:

- DONE: umgesetzt
- PARTIAL: teilweise umgesetzt
- TODO: offen

| Funktion | Option A | Option B | Option C | Status | Nächster Schritt |
| --- | --- | --- | --- | --- | --- |
| Kontaktformular | Formular senden | Mailto-Button | Telefon/WhatsApp | DONE | Beobachtung in Produktion |
| E-Mail-Versand | PHPMailer / SMTP | mail() Fallback | Nutzerhinweis + Mailto | PARTIAL | Mailto auf weitere Formulare ausrollen |
| Login | Benutzername + Passwort | Passwort vergessen | Einmalcode | PARTIAL | optional Einmalcode (OTP) spaeter |
| Registrierung | Registrierung | Verifizierung erneut senden | Admin kontaktieren | TODO | nur falls Registrierung benötigt |
| Passwort vergessen | Link per E-Mail | Erneut senden | Support | DONE | Monitoring und Rate-Limit nachziehen |
| Dateiupload | Direkt hochladen | Per E-Mail senden | Cloud-Link akzeptieren | PARTIAL | Upload-UI um Alternativen erweitern |
| Bilder | Originalbild | Platzhalter | Textbeschreibung | PARTIAL | globales onerror-Bild + Alt-Fallback |
| API-Ausfall | API-Daten | lokale Daten | Hinweistext | PARTIAL | auf alle Listing-Seiten erweitern |
| Datenbankfehler | DB | Cache | Wartungsseite | PARTIAL | maintenance.php + Schalter |
| Admin Speichern | Normal speichern | Autosave | Lokaler Entwurf | PARTIAL | auf weitere Admin-Formulare ausrollen |
| Formulare allgemein | Senden | Entwurf speichern | Daten behalten | PARTIAL | Draft-Helper JS zentralisieren |
| Zahlung | Karte | PayPal | Ueberweisung | TODO | nur falls Zahlungsflow aktiv |
| Terminbuchung | Online | Telefon | E-Mail | PARTIAL | dedizierte Terminseite mit 3 CTA |
| Suche | Suchfeld | Kategorien | Beliebte Seiten | PARTIAL | Such-Empty-State verbessern |
| Navigation | Menue | Suche | Sitemap | DONE | - |
| 404 | Fehlermeldung | Startseite/Suche | Kontakt/Beliebte Seiten | DONE | Monitoring |
| 500 | Fehlermeldung | Neu laden / Startseite | Kontakt / Statushinweis | DONE | Monitoring |
| Session abgelaufen | Neu anmelden | Hinweis | Rueckkehr zur angefragten Seite | PARTIAL | optional Formulardaten-Transfer erweitern |
| JS deaktiviert | JS-Version | einfache HTML-Alternative | Hinweis | TODO | noscript Banner + Fallback Links |
| Cookies abgelehnt | notwendige Cookies | klare Einschraenkungen | alternative Aktionen | PARTIAL | Hinweistext je betroffene Funktion |
| Mobile | responsive UI | klickbare Telefon/E-Mail | Navigation ohne Gesten | DONE | Cross-device QA |
| Downloads | PDF | HTML | per E-Mail anfordern | TODO | pro Download-Endpunkt ergänzen |
| Newsletter | Formular | E-Mail Anmeldung | - | TODO | nur falls Newsletter genutzt |
| Captcha | Mathe-Captcha | erneute Frage | Mail-Verifizierung | PARTIAL | erneute Frage bei Fehler automatisieren |
| Browser-Kompatibilitaet | volle Version | eingeschraenkte Version | Hinweis | TODO | einfacher Browser-Hinweis |

## Admin-Panel Zusatzsicherheit

- TODO: Automatische Backups
- TODO: Papierkorb fuer geloeschte Daten
- TODO: Versionsverlauf
- PARTIAL: Loeschbestaetigung vorhanden
- TODO: Autosave
- TODO: Undo-Funktion
- TODO: Audit-Log
- PARTIAL: Rollen vorhanden (admin_users.role)
- DONE: CSV-Export fuer Anfragen
- TODO: Import mit Validierung

## Diese Runde umgesetzt

- 404-Fallbackseite erstellt: 404.php
- 500-Fallbackseite erstellt: 500.php
- Apache ErrorDocument angepasst: .htaccess
- Cookie-Privacy-Link korrigiert: js/cookie-consent.js
