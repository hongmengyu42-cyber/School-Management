<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = ['setting_key', 'setting_value'];

    private const PASSING_THRESHOLD_KEY = 'passing_grade_threshold';
    private const SCHOOL_NAME_KEY = 'school_name';
    private const CONSECUTIVE_ABSENCE_ALERT_KEY = 'consecutive_absence_alert_threshold';
    private const DEFAULT_PASSING_THRESHOLD = 75.0;
    private const DEFAULT_CONSECUTIVE_ABSENCE_ALERT = 3;

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            return static::where('setting_key', $key)->value('setting_value') ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        Cache::forget("setting:{$key}");
    }

    /** Replaces includes/settings_helpers.php::getPassingThreshold($pdo). */
    public static function passingThreshold(): float
    {
        return (float) static::get(self::PASSING_THRESHOLD_KEY, self::DEFAULT_PASSING_THRESHOLD);
    }

    public static function schoolName(): string
    {
        return (string) static::get(self::SCHOOL_NAME_KEY, 'School');
    }

    /**
     * Number of consecutive "Absent" marks (within a single subject) that
     * triggers an AttendanceAlert notification to the student + linked
     * parents. Configurable so schools can tune sensitivity without a code
     * change.
     */
    public static function consecutiveAbsenceAlertThreshold(): int
    {
        return (int) static::get(self::CONSECUTIVE_ABSENCE_ALERT_KEY, self::DEFAULT_CONSECUTIVE_ABSENCE_ALERT);
    }
}
