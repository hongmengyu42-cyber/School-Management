<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\GradeCategory;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\ReportCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportCardServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportCardService $service;
    private User $teacher;
    private Student $student;
    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ReportCardService();

        $this->teacher = User::create([
            'full_name' => 'Ms. Teacher',
            'username' => 'teacher1',
            'email' => 'teacher1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Teacher',
            'status' => 'Active',
        ]);

        $studentUser = User::create([
            'full_name' => 'Student One',
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Student',
            'status' => 'Active',
        ]);

        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => 'S0001',
        ]);

        $academicYear = AcademicYear::create(['year_label' => '2025-2026']);
        $this->semester = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Fall 2025',
            'is_locked' => false,
        ]);
    }

    private function makeSubject(): Subject
    {
        return Subject::create([
            'subject_code' => 'SUB' . random_int(1000, 9999),
            'subject_name' => 'Test Subject',
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_weighted_average_across_categories(): void
    {
        $subject = $this->makeSubject();
        $subject->enrollments()->create(['student_id' => $this->student->id]);

        $homework = GradeCategory::create(['subject_id' => $subject->id, 'name' => 'Homework', 'weight_percent' => 40]);
        $exams = GradeCategory::create(['subject_id' => $subject->id, 'name' => 'Exams', 'weight_percent' => 60]);

        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'category_id' => $homework->id, 'grade_value' => 100]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'category_id' => $exams->id, 'grade_value' => 80]);

        $report = $this->service->buildSubjectReport($subject, $this->student);

        // 100*0.4 + 80*0.6 = 88
        $this->assertSame(88.0, $report['average']);
    }

    public function test_categories_with_no_grades_are_excluded_not_zeroed(): void
    {
        $subject = $this->makeSubject();
        $subject->enrollments()->create(['student_id' => $this->student->id]);

        $homework = GradeCategory::create(['subject_id' => $subject->id, 'name' => 'Homework', 'weight_percent' => 40]);
        GradeCategory::create(['subject_id' => $subject->id, 'name' => 'Exams', 'weight_percent' => 60]); // no grades yet

        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'category_id' => $homework->id, 'grade_value' => 90]);

        $report = $this->service->buildSubjectReport($subject, $this->student);

        // Only Homework has grades, so it's normalized to 100% weight on its own.
        $this->assertSame(90.0, $report['average']);
    }

    public function test_falls_back_to_flat_average_when_no_categories_exist(): void
    {
        $subject = $this->makeSubject();
        $subject->enrollments()->create(['student_id' => $this->student->id]);

        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'grade_value' => 70]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'grade_value' => 90]);

        $report = $this->service->buildSubjectReport($subject, $this->student);

        $this->assertSame(80.0, $report['average']);
    }

    public function test_subject_with_no_grades_has_null_average_and_status(): void
    {
        $subject = $this->makeSubject();
        $subject->enrollments()->create(['student_id' => $this->student->id]);

        $report = $this->service->buildSubjectReport($subject, $this->student);

        $this->assertNull($report['average']);
        $this->assertNull($report['status']);
    }

    public function test_grades_are_not_leaked_across_students_on_a_shared_subject(): void
    {
        $subject = $this->makeSubject();
        $subject->enrollments()->create(['student_id' => $this->student->id]);

        $otherStudentUser = User::create([
            'full_name' => 'Student Two',
            'username' => 'student2',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'role' => 'Student',
            'status' => 'Active',
        ]);
        $otherStudent = Student::create(['user_id' => $otherStudentUser->id, 'student_number' => 'S0002']);
        $subject->enrollments()->create(['student_id' => $otherStudent->id]);

        Grade::create(['student_id' => $this->student->id, 'subject_id' => $subject->id, 'grade_value' => 90]);
        Grade::create(['student_id' => $otherStudent->id, 'subject_id' => $subject->id, 'grade_value' => 10]);

        $report = $this->service->buildSubjectReport($subject, $this->student);

        $this->assertSame(90.0, $report['average']);
        $this->assertCount(1, $report['grades']);
    }

    public function test_percentage_to_gpa_scale(): void
    {
        $this->assertSame(4.0, $this->service->percentageToGpa(95));
        $this->assertSame(3.0, $this->service->percentageToGpa(85));
        $this->assertSame(2.0, $this->service->percentageToGpa(73));
        $this->assertSame(0.0, $this->service->percentageToGpa(40));
    }

    public function test_build_for_semester_only_includes_subjects_in_that_semester(): void
    {
        $inSemester = $this->makeSubject();
        $inSemester->enrollments()->create(['student_id' => $this->student->id]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $inSemester->id, 'grade_value' => 100]);

        $otherYear = AcademicYear::create(['year_label' => '2024-2025']);
        $otherSemester = Semester::create(['academic_year_id' => $otherYear->id, 'name' => 'Spring 2025', 'is_locked' => true]);
        $outOfSemester = Subject::create([
            'subject_code' => 'OLD1',
            'subject_name' => 'Old Subject',
            'teacher_id' => $this->teacher->id,
            'semester_id' => $otherSemester->id,
        ]);
        $outOfSemester->enrollments()->create(['student_id' => $this->student->id]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $outOfSemester->id, 'grade_value' => 10]);

        $report = $this->service->buildForSemester($this->student, $this->semester);

        $this->assertCount(1, $report['subjects']);
        $this->assertSame(100.0, $report['overall_average']);
    }
}
