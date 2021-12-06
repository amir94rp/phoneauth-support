<?php

namespace PhoneAuth\Support\Middleware;

use Closure;
use PhoneAuth\Support\Contracts\MustVerifyNumber;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureNumberIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param string|null $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyNumber &&
                ! $request->user()->hasVerifiedNumber())) {
            return $request->expectsJson()
                ? abort(403, 'Your phone number is not verified.')
                : Redirect::guest( $redirectToRoute
                    ? URL::route($redirectToRoute)
                    : URL::temporarySignedRoute(
                        'verify.create',
                        now()->addMinutes(15),
                        $request->user()
                            ? ['number' => $request->user()->getNumberForVerification()]
                            : null
                    ));
        }

        return $next($request);
    }
}
