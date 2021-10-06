<?php

namespace Itsjeffro\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ResourceActionController extends Controller
{
    /**
     * List actions for the given resource.
     */
    public function index(Request $request, string $resource)
    {
        return response()->json([
            'test',
        ]);
    }

    /**
     * Handle action for the given resource.
     */
    public function handle(Request $request, string $resource, string $action)
    {
        return response()->json([
            'test2',
        ]);
    }
}
