<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('projects')->delete();
        
        $projects = [
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'TRANSFORMER-BASED MULTIMODAL COMMUNICATION SYSTEM FOR WORD PREDICTION IN APHASIA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => 'FRGS-EC/1/2024/FTKIP/F00601',
                'project_title' => 'ANTIBACTERIAL SELF-HEALING COATING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => 'FRGS-EC/1/2024/FTKIP/F00601',
                'project_title' => 'THE IRON STATUE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => 'FRGS-EC/1/2024/FTKIP/F00601',
                'project_title' => 'ENHANCING ENVIRONMENTAL AWARENESS THROUGH FUNCTIONAL ART: AN IRON STATUE PROJECT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => 'FRGS-EC/1/2024/FTKIP/F00601',
                'project_title' => 'WORKSHOP TOOL TROLLEY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'FRUITY HONEY GUMMY BERRY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'BEWELL - YOUR SMART COMPANION OFR A BETTER SELF-CARE JOURNEY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'HEARTCARE: AI-DRIVEN MOBILE APPLICATION WITH CARDIOVASCULAR HEALTH ASSISTANT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'SMART FOOD EXPIRY TRACKER AND AI RECIPE RECOMMENDER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'NEXT-GEN ACADEMIC INSIGHTS: A HETEROGENEOUS NOSQL GRAPH DATA RETRIEVAL MODEL FOR INSTITUTIONAL MONITORING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'MINDSIGHT : FACIAL EMOTION RECOGNITION FOR MENTAL HEALTH MONITORING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'CROWDVISION : COUNTING PEOPLE IN CROWDED EVENTS VIA COMPUTER VISION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'SAFER CLASS : SMART ATTENDANCE SYSTEM USING FACE RECOGNITION IN CLASSROOM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'TOPSIS-BASED INNOVATIVE GOAT MILK QUALITY CLASSIFICATION USING MULTI-ARRAY SENSOR TECHNOLOGY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'AQUA PREDATOR FISH CONTROL AND BIO-CONVERSION FOR AGRIBUSINESS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'PROTOTYPE OF AUTISM EARLY DIAGNOSIS THROUGH HANDWRITING',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'FOODMIND:SMART FOOD EXPIRY TRACKER AND AI RECIPE RECOMMENDER',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'UTEM TEAM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'GUESS THE LEARNING TYPE: AI?ENHANCED GAMIFIED LEARNING KIT FOR MACHINE LEARNING EDUCATION',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => rand(1000, 9999),
                'grant_no' => '-2',
                'project_title' => 'BRINGING ATOMS TO LIFE: AR-BASED LEARNING IN MICROELECTRONICS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert($project);
        }
    }
}
