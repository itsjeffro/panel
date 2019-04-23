<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Panel;

class AppController extends Controller
{
    /**
     * Application entry point.
     *
     * @return Illuminate\Http\Response
     */
    public function show()
    {
        return view('panel::layout', [
            'panelVariables' => json_encode(Panel::scriptVariables()),
        ]);
    }
}
