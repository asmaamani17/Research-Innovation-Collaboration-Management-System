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
                'No,ProjectID,ProjectTitle,ExhibitionName,ExhibitionLevelID,ExhibitionResultID,ExhibitionPlace,StartDate,EndDate,Amount,Invention_Award',
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
            'type' => 'required|in:Template_RD8_1_New,Template_RD8_New,myMOHE,myRA,awards,events,faculties,projects,staff,ip',
        ]);

        $type = $request->type;
        $file = $request->file('file');

        try {
            // Check if file is tab-separated or comma-separated
            $firstLine = file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)[0];
            $isTabSeparated = strpos($firstLine, "\t") !== false;

            if ($isTabSeparated) {
                // Parse tab-separated file
                $csvData = array_map(function($line) {
                    return str_getcsv($line, "\t");
                }, file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            } else {
                // Parse comma-separated file
                $csvData = array_map('str_getcsv', file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            }

            $headers = array_shift($csvData);

            switch ($type) {
                case 'Template_RD8_1_New':
                    $this->importTemplateRD8_1_New($csvData);
                    break;
                case 'Template_RD8_New':
                    $this->importMyRA($csvData);
                    break;
                case 'myMOHE':
                    $this->importMyMOHE($csvData);
                    break;
                case 'myRA':
                    $this->importMyRA($csvData);
                    break;
                case 'awards':
                    $this->importAwards($csvData);
                    break;
                case 'events':
                    $this->importEvents($csvData);
                    break;
                case 'faculties':
                    $this->importFaculties($csvData);
                    break;
                case 'projects':
                    $this->importProjects($csvData);
                    break;
                case 'staff':
                    $this->importStaff($csvData);
                    break;
                case 'ip':
                    $this->importIP($csvData);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully.',
                'imported' => count($csvData),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function importTemplateRD8_1_New($data)
    {
        $importedCount = 0;
        $skippedCount = 0;

        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map CSV columns to database fields
            // Id_Recognation,ProjectID,Person_RefNo,PersonTypeID,FullName
            $idRecognation = $row[0] ?? null;
            $projectId = $row[1] ?? null;
            $personRefNo = $row[2] ?? null;
            $personTypeId = $row[3] ?? null;
            $fullName = $row[4] ?? null;

            // Find or create project
            $project = Project::where('project_id', $projectId)->first();
            if (!$project && $projectId) {
                $project = Project::create([
                    'project_id' => $projectId,
                    'project_title' => 'Imported Project ' . $projectId,
                ]);
            }

            // Find or create staff
            $staff = Staff::where('staff_id', $personRefNo)->first();
            if (!$staff && $personRefNo && $fullName) {
                $staff = Staff::create([
                    'staff_id' => $personRefNo,
                    'staff_name' => $fullName,
                    'PersonTypeID' => $personTypeId ?? 1,
                ]);
            }

            // Only create award if both project and staff exist
            if ($project && $staff) {
                // Check if award already exists for this project-staff combination
                $existingAward = Award::where('project_id', $project->id)
                    ->where('staff_id', $staff->id)
                    ->first();

                if (!$existingAward) {
                    // Create new award record
                    Award::create([
                        'project_id' => $project->id,
                        'staff_id' => $staff->id,
                        'award_name' => 'Imported Award ' . $idRecognation,
                        'award_level' => 'N/A',
                        'award_type' => 'Imported',
                        'award_date' => now(),
                    ]);
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            } else {
                $skippedCount++;
            }
        }

        \Log::info("Template_RD8_1_New Import", [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
            'total_rows' => count($data)
        ]);
    }

    private function importAwards($data)
    {
        foreach ($data as $row) {
            Award::create([
                'award_name' => $row[0] ?? null,
                'award_level' => $row[1] ?? null,
                'award_type' => $row[2] ?? null,
                'award_date' => $row[3] ? date('Y-m-d', strtotime($row[3])) : null,
                'evidence_document' => $row[4] ?? null,
            ]);
        }
    }

    private function importEvents($data)
    {
        foreach ($data as $row) {
            Competition::create([
                'event_name' => $row[0] ?? null,
                'organizer' => $row[1] ?? null,
                'exhibition_place' => $row[2] ?? null,
                'exhibition_level' => $row[3] ?? null,
                'start_date' => $row[4] ? date('Y-m-d', strtotime($row[4])) : null,
                'end_date' => $row[5] ? date('Y-m-d', strtotime($row[5])) : null,
            ]);
        }
    }

    private function importFaculties($data)
    {
        foreach ($data as $row) {
            Faculty::create([
                'faculty_code' => $row[0] ?? null,
                'faculty_name' => $row[1] ?? null,
            ]);
        }
    }

    private function importProjects($data)
    {
        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            $projectId = $row[0] ?? null;
            $projectTitle = $row[1] ?? null;

            // Check if project already exists by project_id (as string)
            $existingProject = Project::where('project_id', $projectId)->first();
            
            if ($existingProject) {
                // Update existing project
                $existingProject->update([
                    'project_title' => $projectTitle,
                ]);
            } else {
                // Create new project - project_id will be stored as string in database
                // The database column should be varchar to support alphanumeric IDs
                Project::create([
                    'project_id' => $projectId,
                    'project_title' => $projectTitle,
                ]);
            }
        }
    }

    private function importStaff($data)
    {
        foreach ($data as $row) {
            $faculty = Faculty::where('faculty_code', $row[2] ?? null)->first();
            Staff::create([
                'staff_id' => $row[0] ?? null,
                'staff_name' => $row[1] ?? null,
                'faculty_id' => $faculty ? $faculty->id : null,
            ]);
        }
    }

    private function importMyMOHE($data)
    {
        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map CSV columns to database fields
            // NO,PROJECT ID,PERSON REFF NO,STAFF NAME,FACULTY,NAME OF AWARD,LEVEL OF AWARD,TYPE OF AWARD,ORGANIZER,EXIBITION LEVEL,TITLE OF INVENTION,EVENT,DATE AWARDS (dd-mm-yyyy),LINK TO EVIDENCE
            $projectId = $row[1] ?? null;
            $staffId = $row[2] ?? null;
            $staffName = $row[3] ?? null;
            $facultyName = $row[4] ?? null;
            $awardName = $row[5] ?? null;
            $awardLevel = $row[6] ?? null;
            $awardType = $row[7] ?? null;
            $organizer = $row[8] ?? null;
            $exhibitionLevel = $row[9] ?? null;
            $projectTitle = $row[10] ?? null;
            $eventName = $row[11] ?? null;
            $awardDate = $row[12] ?? null;
            $evidenceLink = $row[13] ?? null;

            // Find or create faculty
            $faculty = Faculty::where('faculty_name', $facultyName)->first();
            if (!$faculty && $facultyName) {
                $faculty = Faculty::create([
                    'faculty_code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $facultyName), 0, 5)),
                    'faculty_name' => $facultyName,
                ]);
            }

            // Find or create staff
            $staff = Staff::where('staff_id', $staffId)->first();
            if (!$staff && $staffId && $staffName) {
                $staff = Staff::create([
                    'staff_id' => $staffId,
                    'staff_name' => $staffName,
                    'faculty_id' => $faculty ? $faculty->id : null,
                ]);
            }

            // Find or create project
            $project = Project::where('project_id', $projectId)->first();
            if (!$project && $projectId && $projectTitle) {
                $project = Project::create([
                    'project_id' => $projectId,
                    'project_title' => $projectTitle,
                ]);
            }

            // Find or create competition/event
            $competition = Competition::where('event_name', $eventName)->first();
            if (!$competition && $eventName) {
                $competition = Competition::create([
                    'event_name' => $eventName,
                    'organizer' => $organizer,
                    'exhibition_level' => $exhibitionLevel,
                    'start_date' => $awardDate ? $this->parseDate($awardDate) : null,
                    'end_date' => $awardDate ? $this->parseDate($awardDate) : null,
                ]);
            }

            // Create award
            Award::create([
                'project_id' => $project ? $project->id : null,
                'staff_id' => $staff ? $staff->id : null,
                'competition_id' => $competition ? $competition->id : null,
                'award_name' => $awardName,
                'award_level' => $awardLevel,
                'award_type' => $awardType,
                'award_date' => $awardDate ? $this->parseDate($awardDate) : null,
                'evidence_link' => $evidenceLink,
            ]);
        }
    }

    private function importMyRA($data)
    {
        foreach ($data as $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map CSV columns to database fields
            // No,ProjectID,ProjectTitle,ExhibitionName,ExhibitionLevelID,ExhibitionResultID,ExhibitionPlace,StartDate,EndDate,Amount,Invention_Award
            $no = $row[0] ?? null;
            $projectId = $row[1] ?? null;
            $projectTitle = $row[2] ?? null;
            $exhibitionName = $row[3] ?? null;
            $exhibitionLevelId = $row[4] ?? null;
            $exhibitionResultId = $row[5] ?? null;
            $exhibitionPlace = $row[6] ?? null;
            $startDate = $row[7] ?? null;
            $endDate = $row[8] ?? null;
            $amount = $row[9] ?? null;
            $inventionAward = $row[10] ?? null;

            // Find existing project by project_id
            $project = Project::where('project_id', $projectId)->first();

            // Find or create competition/event
            $competition = null;
            if ($exhibitionName) {
                $competition = Competition::where('event_name', $exhibitionName)->first();
                if (!$competition) {
                    $competition = Competition::create([
                        'event_name' => $exhibitionName,
                        'exhibition_level_id' => $exhibitionLevelId,
                        'exhibition_place' => $exhibitionPlace,
                        'start_date' => $startDate ? date('Y-m-d', strtotime($startDate)) : null,
                        'end_date' => $endDate ? date('Y-m-d', strtotime($endDate)) : null,
                    ]);
                }
            }

            // Only create award if project exists
            if ($project) {
                // Check if award already exists for this project-competition combination
                $existingAward = Award::where('project_id', $project->id)
                    ->where('competition_id', $competition?->id)
                    ->first();

                if (!$existingAward) {
                    // Create new award record
                    Award::create([
                        'project_id' => $project->id,
                        'competition_id' => $competition?->id,
                        'exhibition_result_id' => $exhibitionResultId,
                        'award_name' => $projectTitle ?? 'Imported Award',
                        'amount' => $amount ? str_replace(['RM', ','], '', $amount) : null,
                        'award_date' => $startDate ? date('Y-m-d', strtotime($startDate)) : now(),
                    ]);
                }
            }
        }
    }

    private function parseDate($dateString)
    {
        if (!$dateString) {
            return null;
        }

        // Try dd-mm-yyyy format
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dateString, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}"; // Convert to yyyy-mm-dd
        }

        // Try other formats
        try {
            return date('Y-m-d', strtotime($dateString));
        } catch (\Exception $e) {
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
                    'filing_date' => $filingDate,
                    'grant_date' => $grantDate,
                    'expiry_date' => $expiryDate,
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
    }
}
