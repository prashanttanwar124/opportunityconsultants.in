<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Support\SiteContent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (idempotent & safe to re-run on production).
     */
    public function run(): void
    {
        // 1. Initial Super Admin login
        User::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@opportunityconsultant.in')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => env('ADMIN_PASSWORD', 'Admin@1234'),
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        // 2. Default CMS settings (only insert missing keys so live edits are preserved)
        foreach (SiteContent::defaults() as $group => $values) {
            foreach ($values as $field => $value) {
                SiteSetting::query()->firstOrCreate(
                    ['key' => "{$group}.{$field}"],
                    ['group' => $group, 'value' => $value],
                );
            }
        }
        SiteContent::clearCache();

        // 3. Starter WhatsApp Quick-Reply Templates
        $templates = [
            [
                'name' => 'Initial Counselling Intro',
                'sort_order' => 1,
                'body' => "Hi {name}, thank you for contacting {site_name}! Our counsellor received your enquiry for {country}. When is a good time for a quick 10-minute free counselling call today?",
            ],
            [
                'name' => 'Document Checklist',
                'sort_order' => 2,
                'body' => "Hi {name}, to help us shortlist the best {country} options for you at {site_name}, please keep these handy for our call:\n1. Latest marksheets (10th, 12th & graduation)\n2. Updated CV / Resume\n3. IELTS / PTE / TOEFL score (if taken)\n4. Preferred intake & rough budget",
            ],
        ];

        foreach ($templates as $template) {
            WhatsappTemplate::query()->firstOrCreate(
                ['name' => $template['name']],
                [
                    'body' => $template['body'],
                    'is_active' => true,
                    'sort_order' => $template['sort_order'],
                ],
            );
        }
    }
}
