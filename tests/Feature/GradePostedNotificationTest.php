<?php

namespace Tests\Feature;

use App\Models\ParentStudentLink;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\GradePosted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GradePostedNotificationTest extends TestCase
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

    private function recordGrade(float $value): void
    {
        $this->actingAs($this->teacher)->post(
            route('teacher.subjects.grades.store', $this->subject),
            ['student_id' => $this->student->id, 'grade_value' => $value]
        )->assertRedirect();
    }

    public function test_grade_notification_reaches_student_and_linked_parent(): void
    {
        Notification::fake();

        $this->recordGrade(90);

        Notification::assertSentTo($this->studentUser, GradePosted::class);
        Notification::assertSentTo($this->parentUser, GradePosted::class);
        Notification::assertCount(2);
    }

    public function test_failing_grade_is_flagged_as_an_alert_in_app(): void
    {
        // Default passing threshold is 75, so this counts as failing.
        $this->recordGrade(40);

        $notification = $this->studentUser->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertTrue($notification->data['is_alert']);
        $this->assertSame('Grade alert', $notification->data['title']);
    }

    public function test_passing_grade_is_not_flagged_as_an_alert(): void
    {
        $this->recordGrade(90);

        $notification = $this->studentUser->fresh()->notifications()->first();

        $this->assertNotNull($notification);
        $this->assertFalse($notification->data['is_alert']);
        $this->assertSame('New grade posted', $notification->data['title']);
    }
}
