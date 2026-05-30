<?php namespace JumpLink\Forms;

use Backend;
use System\Classes\PluginBase;
use JumpLink\Forms\Models\Submission;

/**
 * JumpLink Forms Plugin
 *
 * Speichert Formular-Einsendungen (z. B. Kontaktformular) zusätzlich zum
 * bestehenden E-Mail-Versand im CMS und macht sie im Backend einsehbar –
 * gruppiert/filterbar nach Formular. Generisch gehalten: beliebige Formulare
 * lassen sich ohne Schema-Änderung über Submission::store() anbinden.
 */
class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'jumplink.forms::lang.plugin.name',
            'description' => 'jumplink.forms::lang.plugin.description',
            'author'      => 'JumpLink – Art+Code Studio',
            'icon'        => 'icon-inbox',
            'homepage'    => 'https://artandcode.studio',
        ];
    }

    public function registerNavigation()
    {
        return [
            'forms' => [
                'label'       => 'jumplink.forms::lang.plugin.menu_label',
                'url'         => Backend::url('jumplink/forms/submissions'),
                'icon'        => 'icon-inbox',
                'permissions' => ['jumplink.forms.*'],
                'order'       => 510,
                'counter'      => [Submission::class, 'unreadCount'],
                'counterLabel' => 'jumplink.forms::lang.submissions.counter_label',
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'jumplink.forms.manage_submissions' => [
                'tab'   => 'jumplink.forms::lang.plugin.name',
                'label' => 'jumplink.forms::lang.permissions.manage_submissions',
            ],
        ];
    }
}
