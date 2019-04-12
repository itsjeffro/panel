<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Routing\Controller;
use Itsjeffro\Panel\Panel;

class ResourcesController extends Controller
{
    /**
     * List resources.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Panel::getResources());
    }
}
