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

## Backend

Unter **Formulare → Einsendungen**:

- Liste aller Einsendungen mit Spalten Formular / Name / E-Mail / Status / Eingang,
- Filter nach **Formular**, Status und Eingangsdatum,
- Detailansicht mit allen übermittelten Feldern,
- Status-Workflow (Neu / Gelesen / Erledigt) und Zähler für neue Einsendungen.

## Lizenz

MIT. Built by [JumpLink / Art+Code Studio](https://artandcode.studio).
