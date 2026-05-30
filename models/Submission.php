<?php namespace JumpLink\Forms\Models;

use Model;

/**
 * Submission – eine Formular-Einsendung (generisch).
 *
 * `form` ist der technische Schlüssel des Formulars (z. B. "contact"),
 * `form_label` ein menschenlesbarer Name fürs Backend, `data` enthält alle
 * übermittelten Felder als JSON (damit beliebige Formulare ohne Schema-Änderung
 * gespeichert werden können). name/email werden – falls vorhanden – zusätzlich
 * als eigene Spalten gehalten, damit Liste/Filter/Suche bequem sind.
 */
class Submission extends Model
{
    public $table = 'jumplink_forms_submissions';

    public $timestamps = true;

    protected $fillable = [
        'form', 'form_label', 'name', 'email', 'data', 'status',
    ];

    protected $jsonable = ['data'];

    /**
     * Menschlesbare Standard-Labels je Formular-Schlüssel. Unbekannte Formulare
     * fallen auf den Schlüssel selbst zurück.
     */
    public static $formLabels = [
        'contact'     => 'Kontaktformular',
        'reservation' => 'Reservierung',
    ];

    /**
     * Speichert eine Einsendung. Bewusst tolerant: Fehler beim Speichern dürfen
     * den (separaten) Mailversand des Formulars nicht beeinträchtigen – der
     * Aufrufer kapselt dies, hier wird nur defensiv normalisiert.
     *
     * @param  string  $form   technischer Formular-Schlüssel, z. B. "contact"
     * @param  array   $data   übermittelte Felder (beliebig)
     * @param  string|null $label  optionales, abweichendes Anzeige-Label
     */
    public static function store($form, array $data, $label = null)
    {
        $form = $form ?: 'unknown';

        // name/email aus gängigen Feldnamen herausziehen (best effort).
        $name  = $data['name']
            ?? trim(($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? '')) ?: null;
        $email = $data['email'] ?? null;

        $submission = new static;
        $submission->form       = $form;
        $submission->form_label = $label ?: (static::$formLabels[$form] ?? $form);
        $submission->name       = $name ?: null;
        $submission->email      = $email;
        $submission->data       = $data;
        $submission->status     = 'new';
        $submission->save();

        return $submission;
    }

    /**
     * Anzahl ungelesener Einsendungen – für den Backend-Menü-Zähler.
     */
    public static function unreadCount()
    {
        return (int) static::where('status', 'new')->count();
    }

    public function getStatusOptions()
    {
        return [
            'new'  => 'Neu',
            'read' => 'Gelesen',
            'done' => 'Erledigt',
        ];
    }

    /**
     * Dropdown-Optionen aller tatsächlich vorkommenden Formulare (für den Filter).
     */
    public function getFormOptions()
    {
        $used = static::distinct()->lists('form', 'form');
        $out = [];
        foreach ($used as $key) {
            $out[$key] = static::$formLabels[$key] ?? $key;
        }
        return $out;
    }
}
