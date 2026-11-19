<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\Lead;
use App\Models\Page;
use App\Models\User;
use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAndCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_and_registration_is_disabled(): void
    {
        $this->get('/')->assertOk()->assertSee('25+')->assertSee('+91 7977632506');
        $this->get('/about')->assertOk();
        $this->get('/free-counselling')->assertOk();

        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_super_admin_can_access_all_admin_pages(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        $lead = Lead::create([
            'source' => Lead::SOURCE_CALLBACK,
            'name' => 'Test Student',
            'phone' => '9876543210',
            'interest' => 'study',
            'status' => Lead::STATUS_NEW,
        ]);

        $page = Page::create([
            'title' => 'Sample Page',
            'slug' => 'sample-page',
            'is_published' => true,
        ]);

        $this->actingAs($admin);

        $this->get('/admin')->assertOk();
        $this->get('/admin/leads')->assertOk();
        $this->get('/admin/leads/create')->assertOk();
        $this->get("/admin/leads/{$lead->id}/edit")->assertOk();
        $this->get('/admin/pages')->assertOk();
        $this->get('/admin/pages/create')->assertOk();
        $this->get("/admin/pages/{$page->id}/edit")->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/users/create')->assertOk();
        $this->get("/admin/users/{$admin->id}/edit")->assertOk();
        $this->get('/admin/manage-site-settings')->assertOk();
        $this->get('/admin/manage-home-content')->assertOk();
        $this->get('/admin/manage-about-content')->assertOk();
    }

    public function test_counsellor_can_manage_leads_and_cms_but_not_admins_or_site_settings(): void
    {
        $counsellor = User::factory()->create([
            'role' => User::ROLE_COUNSELLOR,
            'is_active' => true,
        ]);

        $this->actingAs($counsellor);

        $this->get('/admin')->assertOk();
        $this->get('/admin/leads')->assertOk();
        $this->get('/admin/pages')->assertOk();
        $this->get('/admin/manage-home-content')->assertOk();
        $this->get('/admin/manage-about-content')->assertOk();

        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/manage-site-settings')->assertForbidden();
    }

    public function test_inactive_admin_cannot_access_panel(): void
    {
        $inactive = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => false,
        ]);

        $this->actingAs($inactive)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_user_deletion_protects_self_and_last_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);
        $counsellor = User::factory()->create([
            'role' => User::ROLE_COUNSELLOR,
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        $this->assertFalse(UserResource::canDelete($superAdmin));
        $this->assertTrue(UserResource::canDelete($counsellor));
    }

    public function test_cms_content_updates_reflect_immediately_on_home_page(): void
    {
        SiteContent::setGroup('home', [
            'hero_title_accent' => 'ready for takeoff.',
        ]);

        $this->get('/')->assertOk()->assertSee('ready for takeoff.');
    }

    public function test_custom_pages_render_and_appear_in_navigation(): void
    {
        $draft = Page::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'is_published' => false,
        ]);

        $this->get('/'.$draft->slug)->assertNotFound();

        $published = Page::create([
            'title' => 'Study in UK',
            'slug' => 'study-in-uk',
            'eyebrow' => 'United Kingdom',
            'subtitle' => 'Explore top UK universities.',
            'is_published' => true,
            'show_in_header' => true,
            'show_in_footer' => true,
            'blocks' => [
                [
                    'type' => 'rich_text',
                    'data' => [
                        'heading' => 'Why the UK?',
                        'content' => '<p>One-year master degrees and post-study work options.</p>',
                    ],
                ],
                [
                    'type' => 'callback_form',
                    'data' => [
                        'heading' => 'Ask our UK counsellor',
                    ],
                ],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Study in UK');

        $this->get('/study-in-uk')
            ->assertOk()
            ->assertSee('Why the UK?')
            ->assertSee('Ask our UK counsellor');

        $this->post('/callback', [
            'name' => 'Aarav Patel',
            'phone' => '9876543210',
            'interest' => 'study',
            'page_slug' => $published->slug,
        ])->assertRedirect(url('/study-in-uk').'#callback');

        $this->assertDatabaseHas('leads', [
            'name' => 'Aarav Patel',
            'source' => Lead::SOURCE_PAGE,
            'page_slug' => 'study-in-uk',
            'status' => Lead::STATUS_NEW,
        ]);
    }

    public function test_cms_settings_pages_can_save_via_livewire(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        \Livewire\Livewire::test(\App\Filament\Pages\ManageSiteSettings::class)
            ->call('save')
            ->assertHasNoErrors();

        \Livewire\Livewire::test(\App\Filament\Pages\ManageHomeContent::class)
            ->call('save')
            ->assertHasNoErrors();

        \Livewire\Livewire::test(\App\Filament\Pages\ManageAboutContent::class)
            ->call('save')
            ->assertHasNoErrors();
    }

    public function test_admin_can_create_lead_page_and_admin_user_via_livewire(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // Create Lead
        \Livewire\Livewire::test(\App\Filament\Resources\LeadResource\Pages\CreateLead::class)
            ->fillForm([
                'name' => 'Manual Lead',
                'phone' => '9123456789',
                'source' => Lead::SOURCE_CALLBACK,
                'interest' => 'study',
                'status' => Lead::STATUS_NEW,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('leads', ['name' => 'Manual Lead']);

        // Create Custom Page
        \Livewire\Livewire::test(\App\Filament\Resources\PageResource\Pages\CreatePage::class)
            ->fillForm([
                'title' => 'Study in Germany',
                'slug' => 'study-in-germany',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', ['slug' => 'study-in-germany']);

        // Create Counsellor Admin
        \Livewire\Livewire::test(\App\Filament\Resources\UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New Counsellor',
                'email' => 'counsellor@opportunityconsultant.in',
                'role' => User::ROLE_COUNSELLOR,
                'is_active' => true,
                'password' => 'Secret@1234',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'counsellor@opportunityconsultant.in',
            'role' => User::ROLE_COUNSELLOR,
        ]);

        // Submit Free Counselling form and verify it appears in Leads table
        $this->post('/free-counselling', [
            'level' => 'pg',
            'country' => 'GB',
            'intake' => '12m',
            'name' => 'Rohan Shah',
            'phone' => '9876501234',
            'email' => 'rohan@example.com',
            'city' => 'Mumbai',
            'consent' => '1',
        ])->assertRedirect(route('apply'));

        $this->get('/admin/leads')
            ->assertOk()
            ->assertSee('Rohan Shah')
            ->assertSee('United Kingdom');
    }

    public function test_new_crm_blog_templates_and_activity_log_features(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);
        $counsellor = User::factory()->create([
            'role' => User::ROLE_COUNSELLOR,
            'is_active' => true,
        ]);

        // Empty blog state works publicly
        $this->get('/blog')->assertOk()->assertSee('Guides coming soon');

        $this->actingAs($admin);

        // New admin routes all load cleanly
        $this->get('/admin/whatsapp-templates')->assertOk();
        $this->get('/admin/whatsapp-templates/create')->assertOk();
        $this->get('/admin/posts')->assertOk();
        $this->get('/admin/posts/create')->assertOk();
        $this->get('/admin/activity-logs')->assertOk();

        // Create a WhatsApp template and verify placeholder rendering
        $template = \App\Models\WhatsappTemplate::create([
            'name' => 'Initial Intro',
            'body' => 'Hi {name}, welcome to {site_name} for {country}!',
            'is_active' => true,
        ]);

        $lead = Lead::create([
            'source' => Lead::SOURCE_COUNSELLING,
            'name' => 'Meera Joshi',
            'phone' => '9876543210',
            'country' => 'GB',
            'status' => Lead::STATUS_NEW,
            'follow_up_at' => now()->subHour(),
        ]);

        $this->assertTrue($lead->isFollowUpDue());
        $this->assertStringContainsString('Hi Meera Joshi', $template->renderForLead($lead));
        $this->assertStringContainsString('United Kingdom', $template->renderForLead($lead));

        // Create a blog post via Filament and verify public rendering
        \Livewire\Livewire::test(\App\Filament\Resources\PostResource\Pages\CreatePost::class)
            ->fillForm([
                'title' => 'UK Intake Guide',
                'slug' => 'uk-intake-guide',
                'category' => 'Study Abroad',
                'excerpt' => 'Everything you need for September intake.',
                'content' => '<p>Start preparing 8 months before your intake.</p>',
                'is_published' => true,
                'published_at' => now()->subMinute(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/blog')->assertOk()->assertSee('UK Intake Guide');
        $this->get('/blog/uk-intake-guide')->assertOk()->assertSee('Start preparing 8 months');

        // Activity log recorded automatically and accessible to Counsellor too
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'subject_type' => 'Post',
        ]);

        $this->actingAs($counsellor)
            ->get('/admin/activity-logs')
            ->assertOk();
    }
}
