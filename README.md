# JumpLink.Forms – Winter CMS Plugin

Speichert **Formular-Einsendungen** (z. B. das Kontaktformular eines Themes)
zusätzlich zum bestehenden E-Mail-Versand in der Datenbank und macht sie im
Backend einsehbar – gruppiert/filterbar **nach Formular**.

Das Plugin ist bewusst **generisch**: beliebige Formulare lassen sich ohne
Schema-Änderung anbinden, indem im AJAX-Handler des Themes eine Zeile ergänzt
wird. Der E-Mail-Versand des Themes bleibt unverändert.

## Installation

Plugin nach `plugins/jumplink/forms` legen und Migration ausführen:

```bash
php artisan winter:up
```

## Verwendung im Theme

Im bestehenden Formular-Handler (zusätzlich zum Mailversand) speichern:

```php
\JumpLink\Forms\Models\Submission::store('contact', [
    'name'    => post('name'),
    'email'   => post('email'),
    'phone'   => post('phone'),
    'message' => post('message'),
]);
```

- Erster Parameter: technischer Formular-Schlüssel (z. B. `contact`).
- Zweiter Parameter: beliebige Felder (werden als JSON gespeichert).
- Optionaler dritter Parameter: abweichendes Anzeige-Label.

Empfehlung: den `store()`-Aufruf in ein eigenes `try/catch` setzen, damit ein
Speicherfehler den E-Mail-Versand nicht blockiert.

## Telefonnachrichten

Ein Telefonassistent (z. B. [sprechstunde](https://github.com/JumpLink/sprechstunde))
kann Nachrichten fürs Team abgeben:

```
POST /api/jumplink/forms/phone-message
Authorization: Bearer <JUMPLINK_FORMS_PHONE_TOKEN>
Content-Type: application/json

{"name": "Jo Doe", "phone": "+4900000000", "message": "…", "venue": "…", "receivedAt": "…"}
```

Die Nachricht wird als Einsendung `phone` gespeichert und per E-Mail
verschickt. In der `.env` der Winter-Installation:

- `JUMPLINK_FORMS_PHONE_TOKEN` – Pflicht; ohne Token antwortet der Endpunkt
  immer 401.
- `JUMPLINK_FORMS_PHONE_MAILTO` – optional; sonst die `email_address` des
  aktiven Themes, sonst `mail.from.address`.

Antwort `201`, wenn gespeichert oder verschickt; `401` falsches Token, `422`
fehlende Felder, `503` weder gespeichert noch verschickt.

## Backend

Unter **Formulare → Einsendungen**:

- Liste aller Einsendungen mit Spalten Formular / Name / E-Mail / Status / Eingang,
- Filter nach **Formular**, Status und Eingangsdatum,
- Detailansicht mit allen übermittelten Feldern,
- Status-Workflow (Neu / Gelesen / Erledigt) und Zähler für neue Einsendungen.

## Lizenz

MIT. Built by [JumpLink / Art+Code Studio](https://artandcode.studio).
