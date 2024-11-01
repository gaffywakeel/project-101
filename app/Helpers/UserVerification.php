<?php

namespace App\Helpers;

use App\Models\RequiredDocument;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UserVerification
{
    /**
     * Context user
     */
    protected User $user;

    /**
     * Cache store
     */
    protected array $cache = [];

    /**
     * Basic verifications
     *
     * @var array|string[]
     */
    protected array $basic = [
        'verified_phone',
        'verified_email',
        'complete_profile',
    ];

    /**
     * Advanced verifications
     *
     * @var array|string[]
     */
    protected array $advanced = [
        'verified_address',
        'verified_documents',
    ];

    /**
     * Construct verification
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Initialize
     */
    public static function make(User $user): UserVerification
    {
        return new static($user);
    }

    /**
     * Get basic data
     */
    public function getBasic(): Collection
    {
        if (!array_key_exists('basic', $this->cache)) {
            $this->cache['basic'] = $this->parse($this->basic);
        }

        return $this->cache['basic'];
    }

    /**
     * Get advanced data
     */
    public function getAdvanced(): Collection
    {
        if (!array_key_exists('advanced', $this->cache)) {
            $this->cache['advanced'] = $this->parse($this->advanced);
        }

        return $this->cache['advanced'];
    }

    /**
     * Check if verification is complete
     */
    public function isComplete(): bool
    {
        return $this->isCompleteAdvanced();
    }

    /**
     * Check 'advanced' verification
     */
    protected function isCompleteAdvanced(): bool
    {
        return $this->isCompleteBasic() && $this->getStatus($this->getAdvanced());
    }

    /**
     * Check 'basic' verification
     */
    protected function isCompleteBasic(): bool
    {
        return $this->getStatus($this->getBasic());
    }

    /**
     * Verification status
     */
    public function getLevel(): string
    {
        return $this->isCompleteAdvanced() ? 'advanced' : ($this->isCompleteBasic() ? 'basic' : 'unverified');
    }

    /**
     * Parse verification
     */
    protected function parse(array $names): Collection
    {
        return collect($names)
            ->filter(function ($name) {
                return settings()->verification->get($name);
            })
            ->map(function ($name) {
                $status = $this->{'check' . Str::studly($name)}();

                return collect(['name' => $name])
                    ->put('title', trans("verification.$name"))
                    ->put('status', $status);
            })
            ->values();
    }

    /**
     * Parse status
     */
    protected function getStatus(Collection $data): bool
    {
        return $data->reduce(function ($status, $record) {
            return data_get($record, 'status') && $status;
        }, true);
    }

    /**
     * Verified Phone
     */
    protected function checkVerifiedPhone(): bool
    {
        return $this->user->isPhoneVerified();
    }

    /**
     * Verified Email
     */
    protected function checkVerifiedEmail(): bool
    {
        return $this->user->isEmailVerified();
    }

    /**
     * Complete Profile
     */
    protected function checkCompleteProfile(): bool
    {
        return (bool) $this->user->profile?->is_complete;
    }

    /**
     * Verified Documents
     */
    protected function checkVerifiedDocuments(): bool
    {
        return RequiredDocument::enabled()->get()->reduce(function ($verified, $requirement) {
            return $requirement->getDocument($this->user)?->status === 'approved' && $verified;
        }, true);
    }

    /**
     * Verified Address
     */
    protected function checkVerifiedAddress(): bool
    {
        return $this->user->address?->status === 'approved';
    }
}
