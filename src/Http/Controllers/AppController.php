<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Panel;

class AppController extends Controller
{
    /**
     * Application entry point.
     *
     * @return Response|View
     */
    public function show()
    {
        return view('panel::layout', [
            'panelVariables' => json_encode(Panel::scriptVariables()),
        ]);
    }
}
