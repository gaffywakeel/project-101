<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class TwoFactorAuth
{
    /**
     * Generate a new secret key.
     */
    public function generateSecretKey(): string
    {
        return trim(Base32::encodeUpper(random_bytes(10)), '=');
    }

    /**
     * Get the two-factor authentication QR code URL.
     */
    public function qrCodeUrl(string $email, string $secret): string
    {
        $otp = $this->authenticator($secret);

        $otp->setIssuer(config('app.name'));
        $otp->setLabel($email);

        // @codeCoverageIgnoreStart
        if (File::exists(public_path('icon.png'))) {
            $otp->setParameter('image', asset('icon.png'));
        }
        // @codeCoverageIgnoreEnd

        return $otp->getProvisioningUri();
    }

    /**
     * Verify the given code.
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->authenticator($secret)->verify($code);
    }

    /**
     * Generate two-factor code
     */
    public function generateCode(string $secret): string
    {
        return $this->authenticator($secret)->now();
    }

    /**
     * Create instance of authenticator
     */
    protected function authenticator(string $secret): TOTP
    {
        return tap(TOTP::createFromSecret($secret), function (TOTP $totp) {
            $totp->setPeriod(30);
            $totp->setDigest('sha1');
            $totp->setDigits(6);
        });
    }
}
