<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }

        $user = $request->user();
        $role = $user->roles->first()->name ?? '';

        if ($role === 'admin') {
            $route = 'admin.dashboard';
        } elseif ($role === 'worker') {
            $route = 'worker.dashboard';
        } else {
            $route = 'customer.dashboard';
        }

        return redirect()->intended(
            route($route, absolute: false) . '?verified=1'
        );
    }
}
