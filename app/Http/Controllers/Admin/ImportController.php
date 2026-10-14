<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Competition;
use App\Models\Faculty;
use App\Models\IntellectualProperty;
use App\Models\Project;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function downloadTemplate($type)
    {
        $templates = [
            'Template_RD8_1_New' => [
                'Id_Recognation,ProjectID,Person_RefNo,PersonTypeID,FullName',
                '1,369,1,1,NIK MOHD ZARIFIE BIN HASHIM',
                '2,369,1,1,MOHD FAUZI BIN MAMAT',
                '3,1213,1,1,MOHD FAUZI BIN MAMAT',
            ],
            'Template_RD8_New' => [
                'Id_Recognation,ProjectID,ProjectTitle,ExhibitionName,ExhibitionLevelID,ExhibitionResultID,ExhibitionPlace,StartDate,EndDate,Amount,Invention_Award',
                '1,8653,TRANSFORMER-BASED MULTIMODAL COMMUNICATION SYSTEM FOR WORD PREDICTION IN APHASIA,THE 2ND ARTIFICIAL INTELLIGENCE APPLICATION CHALLENGE BY CHINA-ASEAN INFORMATION HARBOR ELECTRONIC INFORMATION TALENT DEVELOPMENT AND TECHNOLOGY INNOVATION ALLIANCE,1,9,ONLINE,01-07-2025,01-07-2025,RM0.00,1',
                '2,8661,ANTIBACTERIAL SELF-HEALING COATING,4TH INTERNATIONAL RESEARCH & INNOVATIVE TECHNOLOGY COMPETITION 2025 (I-RITEC2025),1,1,4TH INTERNATIONAL RESEARCH & INNOVATIVE TECHNOLOGY COMPETITION 2025 (I-RITEC2025),10-07-2025,25-09-2025,RM250.00,1',
            ],
            'myMOHE' => [
                'NO,PROJECT ID,STAFF ID,STAFF NAME,FACULTY,NAME OF AWARD,LEVEL OF AWARD,TYPE OF AWARD,ORGANIZER,EXIBITION LEVEL,TITLE OF INVENTION,EVENT,DATE AWARDS (dd-mm-yyyy),LINK TO EVIDENCE',
                '1,PRJ001,STF001,Dr. Ahmad Ali,Faculty of Science,Gold Medal,Gold,Research Award,Ministry of Education,National,Smart Agriculture System,Innovation Expo 2024,15-01-2024,https://example.com/evidence',
                '2,PRJ002,STF002,Prof. Sarah Lee,Faculty of Engineering,Silver Award,Silver,Innovation Award,Ministry of Science,International,Medical Diagnostic Tool,Research Conference 2024,20-02-2024,https://example.com/evidence2',
            ],
            'myRA' => [
                'NO,PROJECT ID,GRANT NO,STAFF ID,STAFF NAME,FACULTY,NAME OF AWARD,ORGANIZER,EXIBITION LEVEL,TITLE OF INVENTION,EVENT,EXIBITION PLACE,START DATE (dd-mm-yyyy),END DATE (dd-mm-yyyy),AMOUNT',
                '1,PRJ001,GRN2024-001,STF001,Dr. Ahmad Ali,Faculty of Science,Research Excellence Award,Research Council,National,Smart Agriculture System,Innovation Expo 2024,Kuala Lumpur,15-01-2024,17-01-2024,RM 5000',
                '2,PRJ002,GRN2024-002,STF002,Prof. Sarah Lee,Faculty of Engineering,Innovation Grant,Ministry of Science,International,Medical Diagnostic Tool,Research Conference 2024,Penang,20-02-2024,22-02-2024,RM 10000',
            ],
            'awards' => [
                'award_name,award_level,award_type,award_date,evidence_document',
                'Best Innovation,National,Research Award,2024-01-15,',
                'Outstanding Contribution,International,Service Award,2024-02-20,document.pdf',
            ],
            'events' => [
                'event_name,organizer,exhibition_place,exhibition_level,start_date,end_date',
                'Research Conference 2024,University Research Board,Kuala Lumpur,National,2024-03-01,2024-03-03',
                'Innovation Expo,Ministry of Science,Penang,International,2024-04-10,2024-04-12',
            ],
            'faculties' => [
                'faculty_code,faculty_name',
                'FSK,Faculty of Science',
                'FK,Faculty of Engineering',
            ],
            'projects' => [
                'project_id,project_title',
                'PRJ001,Smart Agriculture System',
                'PRJ002,Medical Diagnostic Tool',
            ],
            'staff' => [
                'staff_id,staff_name,faculty_code',
                'STF001,Dr. Ahmad Ali,FSK',
                'STF002,Prof. Sarah Lee,FK',
            ],
            'ip' => [
                'IP Number,Title,Type,Status,Filing Date,Grant Date,Expiry Date,Country,Staff IDs,Project ID,Evidence URL,Remarks',
                'MY-2023-001,Smart Agriculture System,PATENT,GRANTED,15-01-2023,20-06-2024,20-06-2034,Malaysia,STF001,PRJ001,https://example.com/patent,Patent for smart agriculture',
                'MY-2023-002,Medical Diagnostic Tool,COPYRIGHT,REGISTERED,10-02-2023,,,Malaysia,STF002,PRJ002,https://example.com/copyright,Copyright for diagnostic tool',
            ],
        ];

        if (!isset($templates[$type])) {
            return back()->with('error', 'Invalid template type.');
        }

        $filename = $type . '_template.csv';
        $content = implode("\n", $templates[$type]);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'type' => 'nullable|in:Template_RD8_1_New,Template_RD8_New,myMOHE,myRA,awards,events,faculties,projects,staff,ip',
        ]);

        $type = $request->type ?? 'Template_RD8_1_New';
        $file = $request->file('file');

        try {
            $fileLines = file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (empty($fileLines)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uploaded file is empty.',
                ], 422);
            }

            $firstLine = $fileLines[0];

            // Auto-detect template type if header columns match known formats
            if (strpos($firstLine, 'Person_RefNo') !== false || strpos($firstLine, 'PersonTypeID') !== false || strpos($firstLine, 'FullName') !== false) {
                $type = 'Template_RD8_1_New';
            } elseif (strpos($firstLine, 'ExhibitionName') !== false || strpos($firstLine, 'ProjectTitle') !== false || strpos($firstLine, 'Invention_Award') !== false || strpos($firstLine, 'ExhibitionLevelID') !== false || strpos($firstLine, 'ExhibitionResultID') !== false) {
                $type = 'Template_RD8_New';
            }

            $isTabSeparated = strpos($firstLine, "\t") !== false;

            if ($isTabSeparated) {
                // Parse tab-separated file
                $csvData = array_map(function($line) {
                    return str_getcsv($line, "\t");
                }, $fileLines);
            } else {
                // Parse comma-separated file
                $csvData = array_map('str_getcsv', $fileLines);
            }

            $headers = array_shift($csvData);

            // Wrap entire import inside database transaction to guarantee zero data inserted if any error occurs
            $importedCount = DB::transaction(function () use ($type, $csvData) {
                switch ($type) {
                    case 'Template_RD8_1_New':
                        return $this->importTemplateRD8_1_New($csvData);
                    case 'Template_RD8_New':
                        return $this->importTemplateRD8_New($csvData);
                    case 'myMOHE':
                        return $this->importMyMOHE($csvData);
                    case 'myRA':
                        return $this->importMyRA($csvData);
                    case 'awards':
                        return $this->importAwards($csvData);
                    case 'events':
                        return $this->importEvents($csvData);
                    case 'faculties':
                        return $this->importFaculties($csvData);
                    case 'projects':
                        return $this->importProjects($csvData);
                    case 'staff':
                        return $this->importStaff($csvData);
                    case 'ip':
                        return $this->importIP($csvData);
                    default:
                        return 0;
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'All file data scanned & verified successfully. Data imported into database.',
                'imported' => $importedCount ?? count($csvData),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error during import scan: ' . $e->getMessage() . '. No changes were saved to the database.',
            ], 422);
        }
    }

    private function importTemplateRD8_1_New($data)
    {
        $importedCount = 0;

        foreach ($data as $index => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map CSV columns: Id_Recognation, ProjectID, Person_RefNo, PersonTypeID, FullName
            $idRecognation = trim($row[0] ?? '');
            $projectId = trim($row[1] ?? '');
            $personRefNo = trim($row[2] ?? '');
            $personTypeId = trim($row[3] ?? '');
            $fullName = trim($row[4] ?? '');

            if (!$personRefNo || !$fullName) {
                continue;
            }

            // 1. Find or Create Staff
            $staff = Staff::where('staff_id', $personRefNo)->first();
            if (!$staff) {
                $staff = Staff::create([
                    'staff_id' => $personRefNo,
                    'staff_name' => $fullName,
                    'PersonTypeID' => is_numeric($personTypeId) ? (int)$personTypeId : 1,
                ]);
            } else {
                if (empty($staff->staff_name) || $staff->staff_name === $personRefNo) {
                    $staff->update(['staff_name' => $fullName]);
                }
                if (is_numeric($personTypeId) && !$staff->PersonTypeID) {
                    $staff->update(['PersonTypeID' => (int)$personTypeId]);
                }
            }

            // 2. Find or Create Project (if valid project ID)
            $project = null;
            if ($projectId && strtoupper($projectId) !== 'TIADA') {
                $project = Project::where('project_id', $projectId)->first();
                if (!$project) {
                    $project = Project::create([
                        'project_id' => $projectId,
                        'project_title' => 'Project ' . $projectId,
                    ]);
                }
            }

            // 3. Sync or Create Award record
            $existingAward = null;
            if ($project) {
                // Look for an unassigned award under this project where staff is not yet assigned (created by Template_RD8_New)
                $existingAward = Award::where('project_id', $project->id)
                    ->whereNull('staff_id')
                    ->first();
            } else {
                // For TIADA projects (where project_id is null)
                $existingAward = Award::whereNull('project_id')
                    ->whereNull('staff_id')
                    ->first();
            }

            if ($existingAward) {
                // Update existing unassigned award
                $existingAward->update([
                    'staff_id' => $staff->id,
                    'project_id' => $project?->id ?? $existingAward->project_id,
                    'award_level' => 'Individual',
                    'award_type' => 'Exhibition',
                ]);
                $importedCount++;
            } else {
                // Create new award (competition details will be filled when Template_RD8_New is imported)
                Award::create([
                    'project_id' => $project?->id,
                    'staff_id' => $staff->id,
                    'award_name' => 'PENDING', // Default valid award level until Template_RD8_New updates with exact medal
                    'award_level' => 'Individual',
                    'award_type' => 'Exhibition',
                    'award_date' => now(),
                ]);
                $importedCount++;
            }
        }

        \Log::info("Template_RD8_1_New Import", [
            'imported' => $importedCount,
            'total_rows' => count($data)
        ]);

        return $importedCount;
    }

    private function importTemplateRD8_New($data)
    {
        $importedCount = 0;

        $levelMap = [
            1 => 'International',
            2 => 'National',
            4 => 'University',
        ];

        // Maps ExhibitionResultID to proper UPPERCASE award names for dashboard display
        $resultAwardMap = [
            1 => 'GOLD',
            2 => 'GOLD',
            3 => 'SILVER',
            4 => 'SILVER',
            5 => 'BRONZE',
            6 => 'CERTIFICATE',
            7 => 'PLATINUM',
            8 => 'SPECIAL',
            9 => 'SPECIAL',
            999 => 'OTHERS',
        ];

        foreach ($data as $index => $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Map columns: Id_Recognation, ProjectID, ProjectTitle, ExhibitionName, ExhibitionLevelID, ExhibitionResultID, ExhibitionPlace, StartDate, EndDate, Amount, Invention_Award
            $idRecognation = trim($row[0] ?? '');
            $projectId = trim($row[1] ?? '');
            $projectTitle = trim($row[2] ?? '');
            $exhibitionName = trim($row[3] ?? '');
            $exhibitionLevelId = is_numeric($row[4] ?? null) ? (int)$row[4] : null;
            $exhibitionResultId = is_numeric($row[5] ?? null) ? (int)$row[5] : null;
            $exhibitionPlace = trim($row[6] ?? '');
            $startDateRaw = trim($row[7] ?? '');
            $endDateRaw = trim($row[8] ?? '');
            $amountRaw = trim($row[9] ?? '');
            $inventionAward = trim($row[10] ?? '');

            // Parse dates accurately
            $parsedStartDate = $this->parseDate($startDateRaw);
            $parsedEndDate = $this->parseDate($endDateRaw);

            // Clean numeric amount
            $cleanAmount = $amountRaw ? (float)preg_replace('/[^0-9.]/', '', $amountRaw) : 0.00;

            // Determine exhibition level & award level strings
            $exhibitionLevelName = $exhibitionLevelId ? ($levelMap[$exhibitionLevelId] ?? 'University') : 'University';
            $awardName = $exhibitionResultId ? ($resultAwardMap[$exhibitionResultId] ?? 'OTHERS') : 'OTHERS';

            // 1. Find or Create Project
            $project = null;
            if ($projectId && strtoupper($projectId) !== 'TIADA') {
                $project = Project::where('project_id', $projectId)->first();
                if ($project) {
                    if ($projectTitle && (str_contains($project->project_title, 'Project ') || empty($project->project_title))) {
                        $project->update(['project_title' => $projectTitle]);
                    }
                } else {
                    $project = Project::create([
                        'project_id' => $projectId,
                        'project_title' => $projectTitle ?: ('Project ' . $projectId),
                    ]);
                }
            }

            // 2. Find or Create Competition / Event
            $competition = null;
            if ($exhibitionName) {
                $competition = Competition::where('event_name', $exhibitionName)->first();
                if (!$competition) {
                    $competition = Competition::create([
                        'event_name' => $exhibitionName,
                        'organizer' => $exhibitionName,
                        'exhibition_level_id' => $exhibitionLevelId ?: 4,
                        'exhibition_level' => $exhibitionLevelName,
                        'exhibition_place' => $exhibitionPlace ?: null,
                        'start_date' => $parsedStartDate,
                        'end_date' => $parsedEndDate,
                    ]);
                } else {
                    $updateComp = [];
                    if (!$competition->exhibition_level_id && $exhibitionLevelId) {
                        $updateComp['exhibition_level_id'] = $exhibitionLevelId;
                        $updateComp['exhibition_level'] = $exhibitionLevelName;
                    }
                    if (!$competition->exhibition_place && $exhibitionPlace) {
                        $updateComp['exhibition_place'] = $exhibitionPlace;
                    }
                    if (!$competition->start_date && $parsedStartDate) {
                        $updateComp['start_date'] = $parsedStartDate;
                    }
                    if (!$competition->end_date && $parsedEndDate) {
                        $updateComp['end_date'] = $parsedEndDate;
                    }
                    if (!empty($updateComp)) {
                        $competition->update($updateComp);
                    }
                }
            }

            // 3. Sync or Create Award record
            $existingAward = null;
            if ($project) {
                // Look for an unassigned award under this project where competition is not yet assigned (created by Template_RD8_1_New)
                $existingAward = Award::where('project_id', $project->id)
                    ->whereNull('competition_id')
                    ->first();
            } else {
                // For TIADA projects (where project is null)
                $existingAward = Award::whereNull('project_id')
                    ->whereNull('competition_id')
                    ->first();
            }

            if ($existingAward) {
                // Update existing award
                $existingAward->update([
                    'project_id' => $project?->id ?? $existingAward->project_id,
                    'competition_id' => $competition?->id ?? $existingAward->competition_id,
                    'exhibition_result_id' => $exhibitionResultId ?? $existingAward->exhibition_result_id,
                    'award_name' => $awardName,
                    'award_level' => 'Individual',
                    'award_type' => 'Exhibition',
                    'invention_award' => is_numeric($inventionAward) ? (int)$inventionAward : $existingAward->invention_award,
                    'award_date' => $parsedStartDate ?? ($existingAward->award_date ?? now()),
                    'amount' => $cleanAmount,
                ]);
                $importedCount++;
            } else {
                // Create new award
                Award::create([
                    'project_id' => $project?->id,
                    'competition_id' => $competition?->id,
                    'exhibition_result_id' => $exhibitionResultId,
                    'award_name' => $awardName,
                    'award_level' => 'Individual',
                    'award_type' => 'Exhibition',
                    'invention_award' => is_numeric($inventionAward) ? (int)$inventionAward : null,
                    'award_date' => $parsedStartDate ?? now(),
                    'amount' => $cleanAmount,
                ]);
                $importedCount++;
            }
        }

        return $importedCount;
    }

    private function parseDate($dateString)
    {
        if (!$dateString) {
            return null;
        }

        $dateString = trim($dateString);

        // Try yyyy-mm-dd format (e.g. 2026-01-16)
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $dateString, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
        }

        // Try dd-mm-yyyy format (e.g. 16-01-2026 or 01-07-2025)
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dateString, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        // Try dd/mm/yyyy format
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        try {
            return date('Y-m-d', strtotime($dateString));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function importIP($data)
    {
        $importedCount = 0;
        $skippedCount = 0;

        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map CSV columns to database fields
            // IP Number,Title,Type,Status,Filing Date,Grant Date,Expiry Date,Country,Staff IDs,Project ID,Evidence URL,Remarks
            $ipNumber = $row[0] ?? null;
            $title = $row[1] ?? null;
            $type = $row[2] ?? null;
            $status = $row[3] ?? null;
            $filingDate = $this->parseDate($row[4] ?? null);
            $grantDate = $this->parseDate($row[5] ?? null);
            $expiryDate = $this->parseDate($row[6] ?? null);
            $country = $row[7] ?? null;
            $staffIds = $row[8] ?? null;
            $projectId = $row[9] ?? null;
            $evidenceUrl = $row[10] ?? null;
            $remarks = $row[11] ?? null;

            // Find project by project_id
            $project = null;
            if ($projectId) {
                $project = Project::where('project_id', $projectId)->first();
            }

            // Parse staff IDs (comma-separated)
            $staffList = [];
            if ($staffIds) {
                $staffIdArray = explode(',', $staffIds);
                foreach ($staffIdArray as $staffId) {
                    $staff = Staff::where('staff_id', trim($staffId))->first();
                    if ($staff) {
                        $staffList[] = $staff->id;
                    }
                }
            }

            // Check if IP already exists with same IP number
            $existingIP = null;
            if ($ipNumber) {
                $existingIP = IntellectualProperty::where('ip_number', $ipNumber)->first();
            }

            if (!$existingIP && $title && $type) {
                // Create new IP record
                $ip = IntellectualProperty::create([
                    'ip_number' => $ipNumber,
                    'title' => $title,
                    'type' => $type,
                    'status' => $status,
                    'category' => $type,
                    'status_produk' => $status,
                    'nama_produk' => $title,
                    'no_pemfailan' => $ipNumber,
                    'jenis_ipr' => $type,
                    'filing_date' => $filingDate,
                    'tarikh_difailkan' => $filingDate,
                    'grant_date' => $grantDate,
                    'expiry_date' => $expiryDate,
                    'tarikh_luput' => $expiryDate,
                    'country' => $country,
                    'link_to_evidence' => $evidenceUrl,
                    'remarks' => $remarks,
                    'project_id' => $project?->id,
                ]);

                // Attach staff
                if (!empty($staffList)) {
                    $ip->staff()->attach($staffList);
                }

                $importedCount++;
            } else {
                $skippedCount++;
            }
        }

        \Log::info("IP Import", [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
            'total_rows' => count($data)
        ]);

        return $importedCount;
    }

    private function importMyMOHE($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: NO,PROJECT ID,STAFF ID,STAFF NAME,FACULTY,NAME OF AWARD,LEVEL OF AWARD,TYPE OF AWARD,ORGANIZER,EXIBITION LEVEL,TITLE OF INVENTION,EVENT,DATE AWARDS,LINK TO EVIDENCE
            $projectIdStr = trim($row[1] ?? '');
            $staffIdStr = trim($row[2] ?? '');
            $staffName = trim($row[3] ?? '');
            $facultyStr = trim($row[4] ?? '');
            $awardName = trim($row[5] ?? '');
            $awardLevel = trim($row[6] ?? '');
            $awardType = trim($row[7] ?? '');
            $organizer = trim($row[8] ?? '');
            $exhibitionLevelStr = trim($row[9] ?? '');
            $titleInvention = trim($row[10] ?? '');
            $eventName = trim($row[11] ?? '');
            $dateAwardsRaw = trim($row[12] ?? '');
            $evidenceDoc = trim($row[13] ?? '');

            // Faculty lookup (leave NULL if missing or not found)
            $facultyId = null;
            if ($facultyStr) {
                $facultyObj = Faculty::where('faculty_code', $facultyStr)->orWhere('faculty_name', $facultyStr)->first();
                $facultyId = $facultyObj?->id;
            }

            // Staff lookup/create (leave faculty_id null if missing)
            $staff = null;
            if ($staffIdStr) {
                $staff = Staff::where('staff_id', $staffIdStr)->first();
                if (!$staff) {
                    $staff = Staff::create([
                        'staff_id' => $staffIdStr,
                        'staff_name' => $staffName ?: $staffIdStr,
                        'faculty_id' => $facultyId ?: null,
                    ]);
                } elseif ($facultyId && !$staff->faculty_id) {
                    $staff->update(['faculty_id' => $facultyId]);
                }
            }

            // Project lookup/create
            $project = null;
            if ($projectIdStr && strtoupper($projectIdStr) !== 'TIADA') {
                $project = Project::where('project_id', $projectIdStr)->first();
                if (!$project) {
                    $project = Project::create([
                        'project_id' => $projectIdStr,
                        'project_title' => $titleInvention ?: ('Project ' . $projectIdStr),
                    ]);
                }
            }

            // Competition lookup/create
            $competition = null;
            if ($eventName) {
                $competition = Competition::where('event_name', $eventName)->first();
                if (!$competition) {
                    $competition = Competition::create([
                        'event_name' => $eventName,
                        'organizer' => $organizer ?: $eventName,
                        'exhibition_level' => $exhibitionLevelStr ?: 'University',
                    ]);
                }
            }

            $parsedDate = $this->parseDate($dateAwardsRaw);

            // Create Award record
            Award::create([
                'project_id' => $project?->id ?: null,
                'staff_id' => $staff?->id ?: null,
                'competition_id' => $competition?->id ?: null,
                'award_name' => $awardName ?: ($titleInvention ?: 'Imported Award'),
                'award_level' => $awardLevel ?: 'Gold',
                'award_type' => $awardType ?: 'Research Award',
                'award_date' => $parsedDate ?: now(),
                'evidence_document' => $evidenceDoc ?: null,
            ]);

            $importedCount++;
        }

        return $importedCount;
    }

    private function importAwards($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: award_name, award_level, award_type, award_date, evidence_document
            $awardName = trim($row[0] ?? '');
            $awardLevel = trim($row[1] ?? '');
            $awardType = trim($row[2] ?? '');
            $awardDateRaw = trim($row[3] ?? '');
            $evidenceDoc = trim($row[4] ?? '');

            if (!$awardName) {
                continue;
            }

            Award::create([
                'award_name' => $awardName,
                'award_level' => $awardLevel ?: null,
                'award_type' => $awardType ?: 'Research Award',
                'award_date' => $this->parseDate($awardDateRaw) ?: now(),
                'evidence_document' => $evidenceDoc ?: null,
            ]);

            $importedCount++;
        }

        return $importedCount;
    }

    private function importEvents($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: event_name, organizer, exhibition_place, exhibition_level, start_date, end_date
            $eventName = trim($row[0] ?? '');
            $organizer = trim($row[1] ?? '');
            $place = trim($row[2] ?? '');
            $level = trim($row[3] ?? '');
            $startDate = trim($row[4] ?? '');
            $endDate = trim($row[5] ?? '');

            if (!$eventName) {
                continue;
            }

            Competition::updateOrCreate(
                ['event_name' => $eventName],
                [
                    'organizer' => $organizer ?: null,
                    'exhibition_place' => $place ?: null,
                    'exhibition_level' => $level ?: null,
                    'start_date' => $this->parseDate($startDate),
                    'end_date' => $this->parseDate($endDate),
                ]
            );

            $importedCount++;
        }

        return $importedCount;
    }

    private function importFaculties($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: faculty_code, faculty_name
            $code = trim($row[0] ?? '');
            $name = trim($row[1] ?? '');

            if (!$code && !$name) {
                continue;
            }

            Faculty::updateOrCreate(
                ['faculty_code' => $code],
                ['faculty_name' => $name ?: $code]
            );

            $importedCount++;
        }

        return $importedCount;
    }

    private function importProjects($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: project_id, project_title
            $projId = trim($row[0] ?? '');
            $projTitle = trim($row[1] ?? '');

            if (!$projId) {
                continue;
            }

            Project::updateOrCreate(
                ['project_id' => $projId],
                ['project_title' => $projTitle ?: ('Project ' . $projId)]
            );

            $importedCount++;
        }

        return $importedCount;
    }

    private function importStaff($data)
    {
        $importedCount = 0;

        foreach ($data as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            // Columns: staff_id, staff_name, faculty_code
            $staffId = trim($row[0] ?? '');
            $staffName = trim($row[1] ?? '');
            $facCode = trim($row[2] ?? '');

            if (!$staffId) {
                continue;
            }

            $facultyId = null;
            if ($facCode) {
                $fac = Faculty::where('faculty_code', $facCode)->orWhere('faculty_name', $facCode)->first();
                $facultyId = $fac?->id;
            }

            Staff::updateOrCreate(
                ['staff_id' => $staffId],
                [
                    'staff_name' => $staffName ?: $staffId,
                    'faculty_id' => $facultyId ?: null, // leaves null if faculty is not provided
                ]
            );

            $importedCount++;
        }

        return $importedCount;
    }
}

