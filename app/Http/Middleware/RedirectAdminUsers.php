<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class RedirectAdminUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a usuarios autenticados
        if (Auth::check()) {
            $user = Auth::user();

            // Si el usuario no tiene company_id
            if (is_null($user->company_id)) {
                // Si es super-admin o admin, redirigir al panel de Filament
                if ($user->hasRole(['super_admin', 'admin'])) {
                    return redirect()->route('filament.admin.pages.dashboard');
                }

                // Si no es admin y no tiene company_id, es un caso raro - log y logout
                Log::warning('Usuario sin company_id y sin roles de admin intentó acceder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()->toArray(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => __('auth.failed')]);
            }
        }

        return $next($request);
    }
}
