<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Send email
     */
    public function sendEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }

    /**
     * Verify email
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('index')
            ->notify(trans('verification.email_verified'), 'success');
    }
}
