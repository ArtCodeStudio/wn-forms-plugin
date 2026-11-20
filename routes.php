<?php

use JumpLink\Forms\Classes\PhoneMessage;

/*
 * Nachrichten, die ein Telefonassistent (z. B. sprechstunde) für das Team
 * aufnimmt. Ohne Web-Middleware: kein Session-Cookie, kein CSRF – die
 * Anfrage weist sich per Bearer-Token aus (JUMPLINK_FORMS_PHONE_TOKEN).
 */
Route::post('api/jumplink/forms/phone-message', [PhoneMessage::class, 'receive']);
