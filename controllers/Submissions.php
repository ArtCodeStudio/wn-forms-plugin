<?php namespace JumpLink\Forms\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

/**
 * Backend-Controller für Formular-Einsendungen (Liste + Detailansicht).
 */
class Submissions extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = ['jumplink.forms.manage_submissions'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('JumpLink.Forms', 'forms', 'submissions');
    }

    /**
     * Beim Öffnen einer "neuen" Einsendung diese als gelesen markieren.
     */
    public function update($recordId = null, $context = null)
    {
        $model = \JumpLink\Forms\Models\Submission::find($recordId);
        if ($model && $model->status === 'new') {
            $model->status = 'read';
            $model->save();
        }
        return $this->asExtension('FormController')->update($recordId, $context);
    }
}
