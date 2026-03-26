<?php

namespace App\Http\Controllers\Admin\Concerns;

trait EnsuresPlatformAdminOnly
{
    /**
     * Training partner admins (HQ or Standard) must not change global config, export all data, etc.
     */
    protected function ensurePlatformAdminOnly(): void
    {
        if (auth()->user()->training_partner_id !== null) {
            abort(403, 'This action is only available for platform administrators.');
        }
    }
}
