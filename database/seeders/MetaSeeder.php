<?php

namespace Database\Seeders;

use App\Models\Meta;
use Illuminate\Database\Seeder;

class MetaSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $metas = [
            [
                'type'        => 'lookingFor',
                'description' => 'Business Partner',
            ],
            [
                'type'        => 'lookingFor',
                'description' => 'Investor',
            ],
            [
                'type'        => 'lookingFor',
                'description' => 'INVESTMENT OPPORTUNITIES',
            ],
            [
                'type'        => 'lookingFor',
                'description' => 'Business Mentor',
            ],
            [
                'type'        => 'industry',
                'description' => 'Technology',
            ],
            [
                'type'        => 'industry',
                'description' => 'Finance',
            ],
            [
                'type'        => 'industry',
                'description' => 'Healthcare',
            ],
            [
                'type'        => 'industry',
                'description' => 'Education',
            ],
            [
                'type'        => 'industry',
                'description' => 'Manufacturing',
            ],
            [
                'type'        => 'industry',
                'description' => 'Retail',
            ],
            [
                'type'        => 'industry',
                'description' => 'Other',
            ],
            [
                'type'        => 'yearsOfExperience',
                'description' => 'Less than 1 year',
            ],
            [
                'type'        => 'yearsOfExperience',
                'description' => '1-3 years',
            ],
            [
                'type'        => 'yearsOfExperience',
                'description' => '3-5 years',
            ],
            [
                'type'        => 'yearsOfExperience',
                'description' => '5-10 years',
            ],
            [
                'type'        => 'yearsOfExperience',
                'description' => 'More than 10 years',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Marketing',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Sales',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Operations',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Finance',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Product Development',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Legal',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Technology',
            ],
            [
                'type'        => 'expertise',
                'description' => 'Other',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Mentorship',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Investment',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Partnership',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Advisory',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Networking',
            ],
            [
                'type'        => 'supportOffer',
                'description' => 'Other',
            ],
        ];

        Meta::insert($metas);
    }
}
