<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ConvertInputKeysToSnakeCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->replace(
            $this->convertKeysToCase($request->post())
        );
        return $next($request);
    }

    private function convertKeysToCase(mixed $data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $array = [];

        foreach ($data as $key => $value) {
            $array[Str::snake($key)] = is_array($value)
                ? $this->convertKeysToCase($value)
                : $value;
        }

        return $array;
    }
}
