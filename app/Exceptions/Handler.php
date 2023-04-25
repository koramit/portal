<?php

namespace App\Exceptions;

use App\Models\PersonalAccessToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // @TODO should notify the user that the token is expired or revoked and admin should be notified
        // @TODO should limit the number of notifications
        // @TODO should queue the notification
        // render for unauthenticated 401
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                if ($token = PersonalAccessToken::findToken($request->bearerToken())) { // token is expired or revoked
                    /** @var \App\Models\User $owner */
                    $owner = $token->tokenable;
                    $key = 'revoked_token_attempts_for_user_'.$owner->name;
                    if (Cache::has($key)) {
                        Cache::increment($key);
                    } else {
                        Cache::put($key, 1, now()->addMinute());
                    }

                    $attempts = Cache::get($key);
                    if ($attempts > config('sanctum.failed_attempts_per_minute')) {
                        Log::alert("Too many attempts [$attempts] with revoked token from $owner->name");
                    }
                } else { // token is invalid
                    $key = 'invalid_token_attempts_for_the_ip_'.$request->ip();
                    if (Cache::has($key)) {
                        Cache::increment($key);
                    } else {
                        Cache::put($key, 1, now()->addMinute());
                    }

                    $attempts = Cache::get($key);
                    if ($attempts > config('sanctum.failed_attempts_per_minute')) {
                        Log::alert("Too many attempts [$attempts] with invalid token from {$request->ip()}");
                    }
                }
            }
        });

        // render for unauthorized 403
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $user = $request->user();
                $key = 'unauthorized_attempts_for_user_'.$user->name;
                if (Cache::has($key)) {
                    Cache::increment($key);
                } else {
                    Cache::put($key, 1, now()->addMinute());
                }

                $attempts = Cache::get($key);
                if ($attempts > config('sanctum.failed_attempts_per_minute')) {
                    Log::alert("Too many attempts [$attempts] with unauthorized token from $user->name");
                }
            }
        });

        // render for too many request 429
        $this->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                $user = $request->user();
                $token = $user->currentAccessToken();
                Log::alert("Too many requests with token [$token->name] from $user->name");
            }
        });
    }
}
