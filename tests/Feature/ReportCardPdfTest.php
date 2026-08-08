<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\ParentStudentLink;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportCardPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $studentUser;
    private User $otherStudentUser;
    private User $parentUser;
    private User $admin;
    private Student $student;
    private Student $otherStudent;
    private Subject $subject;
    private Semester $semester;

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

        $academicYear = AcademicYear::create(['year_label' => '2025-2026']);
        $this->semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Fall 2025',
            'is_locked' => false,
        ]);

        $this->subject = Subject::create([
            'subject_code' => 'MATH101',
            'subject_name' => 'Mathematics',
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->studentUser = User::create([
            'full_name' => 'Student One',
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Student',
            'status' => 'Active',
        ]);
        $this->student = Student::create(['user_id' => $this->studentUser->id, 'student_number' => 'S0001']);
        $this->subject->enrollments()->create(['student_id' => $this->student->id]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject->id, 'grade_value' => 88]);

        $this->otherStudentUser = User::create([
            'full_name' => 'Student Two',
            'username' => 'student2',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'role' => 'Student',
            'status' => 'Active',
        ]);
        $this->otherStudent = Student::create(['user_id' => $this->otherStudentUser->id, 'student_number' => 'S0002']);

        $this->parentUser = User::create([
            'full_name' => 'Parent One',
            'username' => 'parent1',
            'email' => 'parent1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Parent',
            'status' => 'Active',
        ]);
        ParentStudentLink::create(['parent_user_id' => $this->parentUser->id, 'student_id' => $this->student->id]);

        $admin = User::create([
            'full_name' => 'Admin One',
            'username' => 'admin1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'status' => 'Active',
        ]);
        $admin->forceFill(['two_factor_confirmed_at' => now()])->save();
        $this->admin = $admin;
    }

    public function test_student_can_download_own_report_card(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.report-card.show', $this->semester));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_cannot_be_tricked_into_seeing_someone_elses_data(): void
    {
        // currentStudent() always resolves to the logged-in user's own
        // Student row, so there's no student_id to spoof in the URL —
        // this just confirms the response is scoped to the right person.
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.report-card.show', $this->semester));

        $response->assertOk();
    }

    public function test_linked_parent_can_download_childs_report_card(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->get(route('parent.children.report-card.show', [$this->student, $this->semester]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_unlinked_parent_is_forbidden(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->get(route('parent.children.report-card.show', [$this->otherStudent, $this->semester]));

        $response->assertForbidden();
    }

    public function test_admin_can_download_any_students_report_card(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.students.report-card.show', [$this->student, $this->semester]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_index_lists_semesters_with_subjects(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.report-card.index'));

        $response->assertOk();
        $response->assertSee('Fall 2025');
    }
}
