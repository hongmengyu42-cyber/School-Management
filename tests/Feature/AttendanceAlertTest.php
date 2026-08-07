<?php

namespace Tests\Feature;

use App\Models\ParentStudentLink;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\AttendanceAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AttendanceAlertTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $studentUser;
    private User $parentUser;
    private Student $student;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::create([
            'full_name' => 'Ms. Teacher',
            'username' => 'teacher1',
            'email' => 'teacher1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Teacher',
            'status' => 'Active',
        ]);
        // Teachers must have 2FA confirmed or every request bounces to setup.
        $this->teacher->forceFill(['two_factor_confirmed_at' => now()])->save();

        $this->subject = Subject::create([
            'subject_code' => 'MATH101',
            'subject_name' => 'Mathematics',
            'teacher_id' => $this->teacher->id,
        ]);

        $this->studentUser = User::create([
            'full_name' => 'Student One',
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Student',
            'status' => 'Active',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => 'S0001',
        ]);

        $this->subject->enrollments()->create(['student_id' => $this->student->id]);

        $this->parentUser = User::create([
            'full_name' => 'Parent One',
            'username' => 'parent1',
            'email' => 'parent1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Parent',
            'status' => 'Active',
        ]);

        ParentStudentLink::create([
            'parent_user_id' => $this->parentUser->id,
            'student_id' => $this->student->id,
        ]);
    }

    private function markAbsent(string $date): void
    {
        $this->actingAs($this->teacher)->post(
            route('teacher.subjects.attendance.store', $this->subject),
            ['date' => $date, 'statuses' => [$this->student->id => 'Absent']]
        )->assertRedirect();
    }

    public function test_no_alert_before_threshold_is_reached(): void
    {
        Notification::fake();

        $this->markAbsent('2026-01-05'); // 1st consecutive absence
        $this->markAbsent('2026-01-06'); // 2nd consecutive absence (default threshold is 3)

        Notification::assertNothingSent();
    }

    public function test_alert_fires_once_threshold_is_reached(): void
    {
        Notification::fake();

        $this->markAbsent('2026-01-05');
        $this->markAbsent('2026-01-06');
        $this->markAbsent('2026-01-07'); // 3rd consecutive absence -> alert

        Notification::assertSentTo($this->studentUser, AttendanceAlert::class);
        Notification::assertSentTo($this->parentUser, AttendanceAlert::class);
        Notification::assertCount(2); // one per notifiable, not per day
    }

    public function test_alert_does_not_repeat_on_every_subsequent_absence(): void
    {
        Notification::fake();

        $this->markAbsent('2026-01-05');
        $this->markAbsent('2026-01-06');
        $this->markAbsent('2026-01-07'); // fires here
        $this->markAbsent('2026-01-08'); // must NOT fire again

        Notification::assertSentTimes(AttendanceAlert::class, 2); // still just the one alert, x2 recipients
    }

    public function test_a_present_day_resets_the_streak(): void
    {
        Notification::fake();

        $this->markAbsent('2026-01-05');
        $this->markAbsent('2026-01-06');

        $this->actingAs($this->teacher)->post(
            route('teacher.subjects.attendance.store', $this->subject),
            ['date' => '2026-01-07', 'statuses' => [$this->student->id => 'Present']]
        );

        $this->markAbsent('2026-01-08');
        $this->markAbsent('2026-01-09'); // only 2 in a row again after the reset

        Notification::assertNothingSent();
    }
}
