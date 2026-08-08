<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 36px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1C2620; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #2F5233; padding-bottom: 10px; }
        .school-name { font-family: 'DejaVu Serif', serif; font-size: 19px; font-weight: bold; color: #2F5233; }
        .doc-title { font-size: 12.5px; color: #4B5A50; margin-top: 3px; }
        .watermark { display: inline-block; margin-top: 8px; padding: 3px 12px; border: 1px solid #A23B32; color: #A23B32; font-size: 10.5px; font-weight: bold; }
        .meta-table { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .meta-table td { padding: 2px 0; font-size: 11px; }
        .meta-label { color: #7C8A80; width: 110px; }
        table.subjects { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.subjects th, table.subjects td { border: 1px solid #DDE2D8; padding: 6px 8px; text-align: left; font-size: 10.5px; vertical-align: top; }
        table.subjects th { background: #E8EEE7; color: #223D26; }
        .status-passed { color: #2F5233; font-weight: bold; }
        .status-failed { color: #A23B32; font-weight: bold; }
        .category-list { font-size: 9.5px; color: #4B5A50; }
        .summary-table { width: 100%; margin-top: 6px; }
        .summary-box { border: 1px solid #DDE2D8; background: #F4F5F1; text-align: center; padding: 10px; }
        .summary-label { font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #7C8A80; }
        .summary-value { font-family: 'DejaVu Serif', serif; font-size: 22px; font-weight: bold; color: #2F5233; margin-top: 2px; }
        .footer { margin-top: 26px; font-size: 9px; color: #7C8A80; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ \App\Models\Setting::schoolName() }}</div>
        <div class="doc-title">Report Card &middot; {{ $semester->name }}@if ($semester->academicYear) ({{ $semester->academicYear->year_label }})@endif</div>
        @unless ($is_official)
            <div class="watermark">UNOFFICIAL &mdash; TERM IN PROGRESS</div>
        @endunless
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Student</td>
            <td>{{ $student->user->full_name }}</td>
            <td class="meta-label">Student #</td>
            <td>{{ $student->student_number }}</td>
        </tr>
        @if ($student->department || $student->year_level)
            <tr>
                <td class="meta-label">Department</td>
                <td>{{ $student->department->department_name ?? '—' }}</td>
                <td class="meta-label">Year level</td>
                <td>{{ $student->year_level ?? '—' }}</td>
            </tr>
        @endif
    </table>

    <table class="subjects">
        <thead>
            <tr>
                <th style="width:16%;">Subject</th>
                <th style="width:14%;">Teacher</th>
                <th style="width:32%;">Grade breakdown</th>
                <th style="width:10%;">Average</th>
                <th style="width:10%;">Status</th>
                <th style="width:18%;">Attendance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subjects as $report)
                <tr>
                    <td>{{ $report['subject']->subject_name }}</td>
                    <td>{{ $report['subject']->teacher->full_name ?? '—' }}</td>
                    <td class="category-list">
                        @if ($report['categories']->isNotEmpty())
                            @foreach ($report['categories'] as $category)
                                @php($categoryGrades = $report['grades']->where('category_id', $category->id))
                                {{ $category->name }} ({{ rtrim(rtrim(number_format($category->weight_percent, 1), '0'), '.') }}%):
                                {{ $categoryGrades->isNotEmpty() ? round($categoryGrades->avg('grade_value'), 1) : 'no grades yet' }}<br>
                            @endforeach
                        @elseif ($report['grades']->isNotEmpty())
                            @foreach ($report['grades'] as $grade)
                                {{ $grade->label ?? 'Grade' }}: {{ $grade->grade_value }}<br>
                            @endforeach
                        @else
                            No grades recorded
                        @endif
                    </td>
                    <td>{{ $report['average'] !== null ? $report['average'] . '%' : '—' }}</td>
                    <td>
                        @if ($report['status'])
                            <span class="{{ $report['status'] === 'Passed' ? 'status-passed' : 'status-failed' }}">{{ $report['status'] }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($report['attendance_rate'] !== null)
                            {{ $report['attendance_rate'] }}% ({{ $report['attendance_days_recorded'] }} days recorded)
                        @else
                            Not recorded
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No subjects enrolled this semester.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td width="50%" style="padding-right:6px;">
                <table width="100%"><tr><td class="summary-box">
                    <div class="summary-label">Overall average</div>
                    <div class="summary-value">{{ $overall_average !== null ? $overall_average . '%' : '—' }}</div>
                </td></tr></table>
            </td>
            <td width="50%" style="padding-left:6px;">
                <table width="100%"><tr><td class="summary-box">
                    <div class="summary-label">GPA (4.0 scale)</div>
                    <div class="summary-value">{{ $overall_gpa !== null ? number_format($overall_gpa, 2) : '—' }}</div>
                </td></tr></table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Generated {{ now()->format('M j, Y g:i A') }} &middot; Passing threshold: {{ rtrim(rtrim(number_format(\App\Models\Setting::passingThreshold(), 1), '0'), '.') }}%
    </div>
</body>
</html>
