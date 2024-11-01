<?php

namespace Tests\Fakes;

use Carbon\Carbon;
use NeoScrypts\Installer\Contracts\InstallerInterface;
use Str;

class InstallerFake implements InstallerInterface
{
    /**
     * {@inheritDoc}
     */
    public function license(): ?array
    {
        return [
            'code' => $this->getLicenseCode(),
            'item' => '34496505',
            'type' => 'Extended License',
            'supported_until' => Carbon::now()->addMonths(6),
            'date' => Carbon::now()->subMonths(6),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function hasValidLicense(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setLicenseCode(string $code): array
    {
        return $this->license();
    }

    /**
     * {@inheritDoc}
     */
    public function getLicenseCode(): ?string
    {
        return Str::uuid()->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function hasLicenseCode(): bool
    {
        return true;
    }
}
