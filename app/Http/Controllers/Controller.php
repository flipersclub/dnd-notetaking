<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function with(): array
    {
        // Check if "with" query parameter is present
        if (request()->has('include')) {
            // Get the comma-separated list of relationships from the query parameter
            return explode(',', request()->include);
        }
        return [];
    }
}
