<?php

namespace App\Http\ViewComposers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingComposer
{

    protected $settings;

    public function __construct()
    {
        $settings = DB::table('settings')->find(1);
        $this->settings = $settings;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('faq_url', Auth::guard('mentee')->check() ? $this->settings->mentee_faq : $this->settings->mentor_faq);
    }

}