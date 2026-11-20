<?php namespace JumpLink\Forms\Classes;

use Cms\Classes\Theme;
use Config;
use Illuminate\Http\Request;
use JumpLink\Forms\Models\Submission;
use Log;
use Mail;

/**
 * Nimmt eine Telefonnachricht als JSON entgegen, speichert sie als Einsendung
 * (Formular "phone") und schickt sie per E-Mail ans Team.
 *
 * Erwartet: {"name", "phone", "message", optional "venue", "receivedAt", "source"}.
 * Antwortet 201 nur, wenn die Nachricht gespeichert ODER verschickt wurde –
 * der Assistent sagt dem Anrufer nur dann, dass sie angekommen ist.
 */
class PhoneMessage
{
    public function receive(Request $request)
    {
        $token = env('JUMPLINK_FORMS_PHONE_TOKEN');
        $given = (string) $request->bearerToken();
        // Ohne konfiguriertes Token ist der Endpunkt aus, nicht offen.
        if (!$token || !hash_equals($token, $given)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $name    = trim((string) $request->input('name'));
        $phone   = preg_replace('/[^0-9+]/', '', (string) $request->input('phone'));
        $message = trim((string) $request->input('message'));
        if ($name === '' || $message === '' || !preg_match('/^\+?[0-9]{5,20}$/', $phone)) {
            return response()->json(['error' => 'name, phone and message are required'], 422);
        }
        $data = [
            'name'       => mb_substr($name, 0, 200),
            'phone'      => $phone,
            'message'    => mb_substr($message, 0, 2000),
            'source'     => mb_substr((string) $request->input('source', ''), 0, 50),
            'venue'      => mb_substr((string) $request->input('venue', ''), 0, 100),
            'receivedAt' => mb_substr((string) $request->input('receivedAt', ''), 0, 40),
        ];

        $stored = false;
        try {
            Submission::store('phone', $data);
            $stored = true;
        } catch (\Throwable $e) {
            Log::error('Telefonnachricht: Speichern fehlgeschlagen: '.$e->getMessage());
        }

        $mailed = false;
        $to = static::recipient();
        if ($to) {
            try {
                Mail::raw(static::mailText($data), function ($mail) use ($to, $data) {
                    $mail->to($to)->subject('Telefonnachricht von '.$data['name']);
                });
                $mailed = true;
            } catch (\Throwable $e) {
                Log::error('Telefonnachricht: E-Mail fehlgeschlagen: '.$e->getMessage());
            }
        }

        if (!$stored && !$mailed) {
            return response()->json(['error' => 'not delivered'], 503);
        }
        return response()->json(['stored' => $stored, 'mailed' => $mailed], 201);
    }

    /** Empfänger: JUMPLINK_FORMS_PHONE_MAILTO, sonst die E-Mail-Adresse des aktiven Themes, sonst der Absender. */
    protected static function recipient()
    {
        $to = env('JUMPLINK_FORMS_PHONE_MAILTO');
        if ($to) {
            return $to;
        }
        try {
            $theme = Theme::getActiveTheme();
            if ($theme && $theme->email_address) {
                return $theme->email_address;
            }
        } catch (\Throwable $e) {
            // kein Theme: weiter zum Absender
        }
        return Config::get('mail.from.address');
    }

    protected static function mailText(array $data)
    {
        return implode("\n", [
            'Am Telefon wurde eine Nachricht für das Team hinterlassen.',
            '',
            'Name: '.$data['name'],
            'Rückrufnummer: '.$data['phone'],
            '',
            $data['message'],
            '',
            'Aufgenommen vom Telefonassistenten'.($data['receivedAt'] ? ' am '.$data['receivedAt'] : '').'.',
            'Die Nachricht steht auch im Backend unter Formulare.',
        ]);
    }
}
