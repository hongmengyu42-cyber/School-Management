<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\DisputeThread;
use App\Models\Grade;
use App\Models\Subject;
use App\Policies\AttendancePolicy;
use App\Policies\DisputeThreadPolicy;
use App\Policies\GradePolicy;
use App\Policies\SubjectPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Grade::class, GradePolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(DisputeThread::class, DisputeThreadPolicy::class);
    }
}
