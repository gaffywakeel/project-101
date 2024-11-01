<?php

namespace App\Exceptions;

use App\Models\CommerceCustomer;
use App\Models\CommercePayment;
use App\Models\PeerOffer;
use App\Models\PeerTrade;
use App\Models\SystemLog;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exceptions with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        TransferException::class,
        LockException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Get the default context variables for logging.
     */
    protected function context(): array
    {
        return rescue(fn () => array_filter([
            'user' => Auth::user()?->only('id', 'name'),
        ]), [], false);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  Request  $request
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        return !$request->expectsJson()
            ? redirect()->guest($exception->redirectTo() ?? route('index'))
                ->notify(trans('auth.unauthenticated'), 'error')
            : response()->json(['message' => $exception->getMessage()], 401);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Exception $exception) {
            rescue(fn () => SystemLog::error($exception->getMessage()), null, false);
        });

        $this->renderable(function (NotFoundHttpException $exception, Request $request) {
            $previous = $exception->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $message = match ($previous->getModel()) {
                    PeerOffer::class => trans('peer.offer_not_found'),
                    PeerTrade::class => trans('peer.trade_not_found'),
                    User::class => trans('user.not_found'),
                    CommerceCustomer::class => trans('commerce.customer_not_found'),
                    CommercePayment::class => trans('commerce.payment_not_found'),
                    default => $exception->getMessage()
                };

                $e = new NotFoundHttpException($message, $previous);

                return $this->renderExceptionResponse($request, $e);
            }
        });
    }
}
