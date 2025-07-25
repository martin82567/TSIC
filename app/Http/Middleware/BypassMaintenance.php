<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BypassMaintenance extends CheckForMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // Allow access to paths that start with 'admin/' or from allowed IPs
        $allowedPaths = ['admin', 'cron', 'dataimport', 'api/middleware'];
        if (Str::startsWith($request->path(), $allowedPaths)) {
            return $next($request);
        }

        if ($this->app->isDownForMaintenance()) {
            $data = json_decode(file_get_contents($this->app->storagePath() . '/framework/down'), true);

            throw new MaintenanceModeException($data['time'], $data['retry'], $data['message']);
        }

        return $next($request);
    }
}
