<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BulkImportUsersRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BulkImportController extends AdminController
{
    /**
     * Expects a CSV with header row:
     * full_name,username,email,password,role,department_code
     *
     * Mirrors the legacy bulk-import script's row-by-row processing and
     * error collection, but wraps each row's related writes (user + student
     * provisioning) in its own transaction so one bad row doesn't corrupt
     * a partially-committed sibling row.
     */
    public function __invoke(BulkImportUsersRequest $request): RedirectResponse
    {
        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $imported = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $record = array_combine($header, $row);

            try {
                DB::transaction(function () use ($record) {
                    $department = !empty($record['department_code'])
                        ? \App\Models\Department::where('department_code', $record['department_code'])->first()
                        : null;

                    $user = User::create([
                        'full_name' => $record['full_name'],
                        'username' => $record['username'],
                        'email' => $record['email'],
                        'password' => Hash::make($record['password'] ?: Str::random(12)),
                        'role' => $record['role'] ?: 'Student',
                        'status' => 'Active',
                        'department_id' => $department?->id,
                    ]);

                    if ($user->role === 'Student') {
                        Student::create([
                            'user_id' => $user->id,
                            'student_number' => 'STU-' . Str::padLeft((string) $user->id, 5, '0'),
                            'department_id' => $department?->id,
                        ]);
                    }
                });

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNumber}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        $this->logActivity($request, 'user.bulk_imported', "Bulk imported {$imported} users");

        return back()->with('status', "Imported {$imported} users.")->with('import_errors', $errors);
    }
}
