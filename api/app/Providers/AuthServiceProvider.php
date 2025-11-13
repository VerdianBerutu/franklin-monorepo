<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Upload;
use App\Policies\UploadPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Upload::class => UploadPolicy::class,
        // Tambahkan policies lainnya nanti
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate Bypass untuk Admin
        Gate::before(function ($user, $ability) {
            // Jika user punya role 'admin', bypass semua permission check
            if ($user->hasRole('admin')) {
                return true;
            }
            // Jika bukan admin, lanjutkan ke permission check normal
        });
    }
}