<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateJsonApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header("accept") !== "application/vnd.api+json") {
            throw new HttpException(406, __("Not Acceptable"));
        }

        if (
            $request->isMethod("post") ||
            $request->isMethod("patch") ||
            $request->isMethod("put")
        ) {
            if ($request->header("content-type") !== "application/vnd.api+json" && !str_contains($request->header("content-type"), "multipart/form-data; boundary=")) {
                throw new HttpException(415, __("Unsupported Media Type"));
            }
        }

        return $next($request)->withHeaders([
            "content-type" => "application/vnd.api+json",
        ]);
    }
}
