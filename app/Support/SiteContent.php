<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteContent
{
    private const CACHE_KEY = 'site_settings.all.v1';

    /** @var array<string, mixed>|null */
    private static ?array $memory = null;

    /**
     * Get a setting value by dot-separated key (e.g. "general.name" or "home.hero_title").
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        $defaults = self::flatDefaults();

        return $defaults[$key] ?? $default;
    }

    /**
     * Get all key/value pairs for a group (with keys stripped of the "group." prefix).
     *
     * @return array<string, mixed>
     */
    public static function group(string $group): array
    {
        $defaults = self::defaults()[$group] ?? [];
        $all = self::all();
        $result = $defaults;

        foreach ($defaults as $field => $fallback) {
            $fullKey = "{$group}.{$field}";
            if (array_key_exists($fullKey, $all) && $all[$fullKey] !== null) {
                $result[$field] = $all[$fullKey];
            }
        }

        return $result;
    }

    /**
     * Save multiple settings for a group and bust caches.
     *
     * @param  array<string, mixed>  $values
     */
    public static function setGroup(string $group, array $values): void
    {
        foreach ($values as $field => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => "{$group}.{$field}"],
                ['group' => $group, 'value' => $value],
            );
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        self::$memory = null;
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$memory !== null) {
            return self::$memory;
        }

        try {
            self::$memory = Cache::rememberForever(self::CACHE_KEY, function (): array {
                if (! Schema::hasTable('site_settings')) {
                    return [];
                }

                return SiteSetting::query()
                    ->pluck('value', 'key')
                    ->all();
            });
        } catch (Throwable) {
            self::$memory = [];
        }

        return self::$memory;
    }

    /**
     * @return array<string, mixed>
     */
    private static function flatDefaults(): array
    {
        $flat = [];
        foreach (self::defaults() as $group => $fields) {
            foreach ($fields as $field => $value) {
                $flat["{$group}.{$field}"] = $value;
            }
        }

        return $flat;
    }

    /**
     * Complete default content matching the original site copy.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function defaults(): array
    {
        return [
            'general' => [
                'name' => config('site.name', 'Opportunity Consultant'),
                'tagline' => config('site.tagline', 'Study Abroad & Internship Consultant'),
                'utility_text' => 'Free counselling for study abroad & international internships',
                'meta_description' => 'Free counselling for study abroad and international internships. We help you choose the country and program, then handle applications, funding and visas.',
                'phone' => config('site.phone', '+917977632506'),
                'phone_display' => config('site.phone_display', '+91 79776 32506'),
                'whatsapp' => config('site.whatsapp', '917977632506'),
                'whatsapp_numbers' => config('site.whatsapp_numbers', [
                    ['display' => '+91 7977632506', 'wa' => '917977632506'],
                    ['display' => '+91 7391833220', 'wa' => '917391833220'],
                ]),
                'email' => config('site.email', 'hello@opportunityconsultant.in'),
                'address' => config('site.address', 'Office 204, Sai Arcade, Station Road, Virar West, Palghar 401303'),
                'address_short' => config('site.address_short', 'Sai Arcade · Virar West'),
                'hours' => config('site.hours', 'Mon–Sat, 10:00 am – 7:00 pm'),
                'directions_url' => config('site.directions_url', 'https://maps.google.com/?q=Sai+Arcade+Station+Road+Virar+West'),
                'instagram_url' => config('site.socials.Instagram', '#'),
                'youtube_url' => config('site.socials.YouTube', '#'),
                'footer_tagline' => 'Free counselling for students and parents.',
            ],
            'scripts' => [
                'head_scripts' => '',
                'body_scripts' => '',
            ],
            'home' => [
                'hero_pill' => 'Study abroad & internship consultants · Free counselling',
                'hero_title' => 'Your journey abroad is',
                'hero_title_accent' => 'now boarding.',
                'hero_text' => 'Study at the right university or intern with an international company. We help you choose the country and program, then handle applications, funding and visas — right up to the day you fly.',
                'hero_primary_cta' => 'Check my eligibility',
                'hero_secondary_cta' => 'Chat on WhatsApp',
                'hero_proof_line1' => '2,500+ students counselled since 2016',
                'hero_proof_line2' => '4.9 ★ on Google from 380+ reviews',
                'departures' => [
                    ['code' => 'OC 101', 'dest' => 'UK', 'label' => 'United Kingdom'],
                    ['code' => 'OC 102', 'dest' => 'USA', 'label' => 'USA'],
                    ['code' => 'OC 103', 'dest' => 'CANADA', 'label' => 'Canada'],
                    ['code' => 'OC 104', 'dest' => 'AUSTRALIA', 'label' => 'Australia'],
                    ['code' => 'OC 105', 'dest' => 'DUBAI', 'label' => 'Dubai'],
                    ['code' => 'OC 106', 'dest' => 'MAURITIUS', 'label' => 'Mauritius'],
                ],
                'callback_title' => 'Talk to a counsellor, free',
                'callback_subtitle' => 'We call back within 24 hours',
                'callback_button' => 'Call me back',
                'marquee_services' => [
                    'FREE COUNSELLING', 'INTERNSHIPS ABROAD', 'UNIVERSITY SHORTLIST',
                    'APPLICATIONS & SOP', 'SCHOLARSHIPS', 'EDUCATION LOANS', 'VISAS', 'PRE-DEPARTURE',
                ],
                'marquee_countries' => [
                    'UNITED KINGDOM', 'USA', 'CANADA', 'AUSTRALIA', 'GERMANY', 'IRELAND', 'DUBAI', 'MAURITIUS',
                ],
                'stats' => [
                    ['value' => '2,500+', 'label' => 'Students counselled', 'accent' => false],
                    ['value' => '150+', 'label' => 'Partner universities and companies', 'accent' => false],
                    ['value' => '96%', 'label' => 'Student visa success rate', 'accent' => false],
                    ['value' => '25+', 'label' => 'Countries for study and internships', 'accent' => true],
                ],
                'programs_eyebrow' => 'Two ways to go global',
                'programs_title' => 'Study abroad. Intern abroad. Or both.',
                'programs_lead' => 'Not sure which suits you? Your counsellor will help you weigh both on the free call.',
                'study_badge' => 'For Class 12 students & graduates',
                'study_title' => 'Study Abroad',
                'study_text' => "Bachelor's, master's, diploma and PhD programs at universities in the UK, USA, Canada, Australia, Germany and Ireland.",
                'study_points' => [
                    'Profile assessment & shortlist',
                    'Applications, SOPs & LORs',
                    'Scholarships & education loans',
                    'Student visa & pre-departure',
                ],
                'internship_badge' => 'For students & recent graduates',
                'internship_title' => 'Internships Abroad',
                'internship_text' => 'International internships that give you real work experience — and a CV that stands out at home and abroad.',
                'internship_facts' => [
                    ['label' => 'INDUSTRIES', 'value' => 'Hospitality, Business & IT'],
                    ['label' => 'DURATION', 'value' => '3–12 months'],
                    ['label' => 'STIPEND', 'value' => 'Paid, up to US$1,500 / month'],
                    ['label' => 'DESTINATIONS', 'value' => 'Dubai, Mauritius, France, USA'],
                ],
                'services_eyebrow' => 'What we do',
                'services_title' => 'Everything between “I want to go abroad” and landing there.',
                'services_lead' => 'One counsellor guides you through every step, so you and your parents always know what comes next.',
                'destinations_eyebrow' => 'Destinations',
                'destinations_title' => "Pick your destination. We'll plan the route.",
                'destinations_lead' => "Not sure which country suits you? That's exactly what the free counselling call is for.",
                'destinations_note' => 'Internship placements are also available in Dubai, Mauritius, France and the Maldives.',
                'destinations' => [
                    ['code' => 'GB', 'route' => 'IND → LHR', 'name' => 'United Kingdom', 'blurb' => "Many master's degrees take just one year, with post-study work options after you graduate.", 'cta' => 'Check my UK eligibility'],
                    ['code' => 'US', 'route' => 'IND → JFK', 'name' => 'USA', 'blurb' => 'The widest choice of universities and programs anywhere, with especially strong STEM options.', 'cta' => 'Check my USA eligibility'],
                    ['code' => 'CA', 'route' => 'IND → YYZ', 'name' => 'Canada', 'blurb' => 'Respected public universities and colleges, with co-op programs that build real work experience.', 'cta' => 'Check my Canada eligibility'],
                    ['code' => 'AU', 'route' => 'IND → SYD', 'name' => 'Australia', 'blurb' => 'Highly ranked universities in student-friendly cities, with part-time work rights while you study.', 'cta' => 'Check my Australia eligibility'],
                    ['code' => 'DE', 'route' => 'IND → FRA', 'name' => 'Germany', 'blurb' => 'Low or no tuition at many public universities, and world-class engineering programs.', 'cta' => 'Check my Germany eligibility'],
                    ['code' => 'IE', 'route' => 'IND → DUB', 'name' => 'Ireland', 'blurb' => "An English-speaking European hub for tech and pharma, with one-year master's options.", 'cta' => 'Check my Ireland eligibility'],
                ],
                'steps_eyebrow' => 'How it works',
                'steps_title' => 'From first call to first day abroad.',
                'steps' => [
                    ['title' => 'Free counselling', 'text' => 'Talk through your marks, budget and goals with a counsellor. Parents welcome.'],
                    ['title' => 'Shortlist & apply', 'text' => 'We shortlist universities or internship programs, then prepare and submit your applications.'],
                    ['title' => 'Offers & funding', 'text' => 'Compare offers, secure scholarships or stipends, and line up your funding.'],
                    ['title' => 'Visa', 'text' => "We build your visa file with you and practise the interview until you're ready."],
                    ['title' => 'Fly out', 'text' => 'A pre-departure briefing, with accommodation and travel sorted before you go.'],
                ],
                'why_title' => 'Honest advice first. Applications second.',
                'why_features' => [
                    ['icon' => 'chat', 'title' => 'Counselling is free', 'text' => 'Ask as many questions as you need before you decide anything.'],
                    ['icon' => 'route', 'title' => 'One counsellor, start to finish', 'text' => 'The person who shortlists your options is the one who files your visa.'],
                    ['icon' => 'star', 'title' => 'Shortlists that fit you', 'text' => 'Universities and internships matched to your profile and budget — explained, not just listed.'],
                    ['icon' => 'users', 'title' => 'Parents in the loop', 'text' => "Family sessions and regular updates, so everyone knows what's happening."],
                ],
                'testimonials_eyebrow' => 'Student stories',
                'testimonials_title' => "Hear it from students who've already flown.",
                'testimonials' => [
                    ['initials' => 'RD', 'tone' => 'accent', 'name' => 'Riya Deshmukh', 'meta' => 'MSc Data Science · Manchester, UK', 'quote' => "My counsellor built a shortlist that actually matched my marks and budget. I got four offers — and a scholarship I didn't even know I could apply for."],
                    ['initials' => 'AK', 'tone' => 'blue', 'name' => 'Aditya Kulkarni', 'meta' => 'PG Diploma, Business · Toronto, Canada', 'quote' => 'They practised the visa interview with me three times and updated my parents every week. The whole process felt calm instead of scary.'],
                    ['initials' => 'SP', 'tone' => 'navy', 'name' => 'Sneha Patil', 'meta' => 'F&B Intern · Five-star resort, Dubai', 'quote' => 'Six months at a five-star resort in Dubai changed my CV completely. They handled the paperwork and prepared me for every interview.'],
                ],
                'faq_title' => 'What students and parents ask us.',
                'faq_subtitle' => 'Question not here? Message us — a counsellor replies on WhatsApp.',
                'faqs' => [
                    ['q' => 'Is the counselling really free?', 'a' => 'Yes. Your counselling sessions and profile assessment are free, with no obligation to sign up. Application and visa services are quoted upfront, with no hidden charges.'],
                    ['q' => 'Do you also arrange internships abroad?', 'a' => 'Yes. We help students and recent graduates find international internships, with guidance on eligibility, duration, stipend and visas. Most placements are in hospitality, business and IT, in Dubai, Mauritius, France and the USA.'],
                    ['q' => 'Do I need IELTS to study abroad?', 'a' => 'Most universities ask for an English test such as IELTS, PTE, TOEFL or Duolingo, though some accept waivers. Your counsellor will confirm exactly what each university on your shortlist needs.'],
                    ['q' => 'When should I start the process?', 'a' => "Ideally 8–12 months before your intake, so there's time for tests, applications, scholarships and the visa. If your intake is sooner, talk to us anyway — we'll tell you honestly what's still possible."],
                    ['q' => 'Can you help with scholarships and education loans?', 'a' => "Yes. We look for scholarships you're eligible for and help you compare education loans from leading Indian banks and NBFCs."],
                    ['q' => 'Which countries do you cover?', 'a' => 'For study abroad: the UK, USA, Canada, Australia, Germany and Ireland. For internships: Dubai, Mauritius, France, the USA and the Maldives. Ask us about other countries too.'],
                    ['q' => 'Can my parents join the counselling?', 'a' => "Absolutely. Parents are welcome on the call or at our office — it's a family decision, and we treat it like one."],
                ],
                'cta_title' => 'Your boarding pass starts with one free call.',
                'cta_text' => 'Answer four quick questions and a counsellor will call you with the study and internship options that fit your profile.',
            ],
            'about' => [
                'hero_title' => 'We turn international education dreams',
                'hero_title_accent' => 'into reality.',
                'hero_text' => "We offer complete, end-to-end solutions tailored to each student's academic and career aspirations — from university shortlisting and language training to visas and post-admission guidance.",
                'glance' => [
                    ['label' => 'COMBINED EXPERIENCE', 'value' => '70+ years'],
                    ['label' => 'OFFICE', 'value' => 'Virar West'],
                    ['label' => 'LANGUAGE TRAINING', 'value' => 'IELTS · TOEFL · PTE'],
                    ['label' => 'COUNTRIES', 'value' => '25+'],
                ],
                'story_eyebrow' => 'Who we are',
                'story_title' => 'Your future knows no borders.',
                'story_text' => "We turn international education dreams into reality. We offer complete, end-to-end solutions tailored to each student's academic and career aspirations. Whether you need expert university shortlisting, comprehensive language training (IELTS, TOEFL, PTE), hassle-free visa processing, or dedicated post-admission guidance, our dedicated team ensures a seamless transition to top institutions worldwide. Start your journey with confidence.",
                'vision_title' => 'Our vision',
                'vision_text' => 'A world where every student who dreams of studying or working abroad gets honest advice and a fair shot at the right opportunity.',
                'mission_title' => 'Our mission',
                'mission_text' => 'To guide every student from their first question to their first day abroad — with transparent advice, careful applications and support for the whole family.',
                'leaders' => [
                    [
                        'initials' => 'MC',
                        'name' => 'Dr. Manasi Chaphekar',
                        'role' => 'Managing Director',
                        'meta' => 'PhD in Hospitality · 23+ years of experience',
                        'bio' => 'Provides strategic leadership, international industry expertise and vision for developing global education and career pathways.',
                        'accent' => true,
                    ],
                    [
                        'initials' => 'MS',
                        'name' => 'Meera Singh',
                        'role' => 'Director',
                        'meta' => '13+ years of experience',
                        'bio' => 'Focuses on organisational development, international partnerships, operational management and building global placement opportunities.',
                        'accent' => false,
                    ],
                    [
                        'initials' => 'PG',
                        'name' => 'Paul Galbano',
                        'role' => 'Chief Operating Officer (COO)',
                        'meta' => '38 years of professional experience',
                        'bio' => 'Brings extensive experience, including leadership positions with major international airlines and reputed organisations. Responsible for business operations, strategic execution, business development and organisational growth.',
                        'accent' => false,
                    ],
                ],
                'team' => [
                    [
                        'initials' => 'KP',
                        'name' => 'Krish Poojari',
                        'role' => 'Head – Human Resources',
                        'bio' => 'Recruitment, employee development, staff training, HR administration and building a professional team culture.',
                    ],
                    [
                        'initials' => 'SK',
                        'name' => 'Sneha Komkar',
                        'role' => 'Admissions Department',
                        'bio' => 'Admission coordination, application processing, documentation, university communication and tracking admission progress.',
                    ],
                ],
                'visit_title' => 'Come in for a chat — bring your parents.',
                'cta_title' => 'Your boarding pass starts with one free call.',
            ],
        ];
    }
}
