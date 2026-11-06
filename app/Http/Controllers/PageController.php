<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * The home page.
     */
    public function home(): View
    {
        $departures = collect([
            ['code' => 'OC 101', 'dest' => 'UK', 'label' => 'United Kingdom'],
            ['code' => 'OC 102', 'dest' => 'USA', 'label' => 'USA'],
            ['code' => 'OC 103', 'dest' => 'CANADA', 'label' => 'Canada'],
            ['code' => 'OC 104', 'dest' => 'AUSTRALIA', 'label' => 'Australia'],
            ['code' => 'OC 105', 'dest' => 'DUBAI', 'label' => 'Dubai'],
            ['code' => 'OC 106', 'dest' => 'MAURITIUS', 'label' => 'Mauritius'],
        ])->map(fn (array $row) => $row + [
            'tiles' => str_split(str_pad($row['dest'], 9)),
        ]);

        $destinations = [
            ['code' => 'GB', 'route' => 'IND → LHR', 'name' => 'United Kingdom', 'blurb' => "Many master's degrees take just one year, with post-study work options after you graduate.", 'cta' => 'Check my UK eligibility'],
            ['code' => 'US', 'route' => 'IND → JFK', 'name' => 'USA', 'blurb' => 'The widest choice of universities and programs anywhere, with especially strong STEM options.', 'cta' => 'Check my USA eligibility'],
            ['code' => 'CA', 'route' => 'IND → YYZ', 'name' => 'Canada', 'blurb' => 'Respected public universities and colleges, with co-op programs that build real work experience.', 'cta' => 'Check my Canada eligibility'],
            ['code' => 'AU', 'route' => 'IND → SYD', 'name' => 'Australia', 'blurb' => 'Highly ranked universities in student-friendly cities, with part-time work rights while you study.', 'cta' => 'Check my Australia eligibility'],
            ['code' => 'DE', 'route' => 'IND → FRA', 'name' => 'Germany', 'blurb' => 'Low or no tuition at many public universities, and world-class engineering programs.', 'cta' => 'Check my Germany eligibility'],
            ['code' => 'IE', 'route' => 'IND → DUB', 'name' => 'Ireland', 'blurb' => "An English-speaking European hub for tech and pharma, with one-year master's options.", 'cta' => 'Check my Ireland eligibility'],
        ];

        $steps = [
            ['title' => 'Free counselling', 'text' => 'Talk through your marks, budget and goals with a counsellor. Parents welcome.'],
            ['title' => 'Shortlist & apply', 'text' => 'We shortlist universities or internship programs, then prepare and submit your applications.'],
            ['title' => 'Offers & funding', 'text' => 'Compare offers, secure scholarships or stipends, and line up your funding.'],
            ['title' => 'Visa', 'text' => "We build your visa file with you and practise the interview until you're ready."],
            ['title' => 'Fly out', 'text' => 'A pre-departure briefing, with accommodation and travel sorted before you go.'],
        ];

        $testimonials = [
            ['initials' => 'RD', 'tone' => 'accent', 'name' => 'Riya Deshmukh', 'meta' => 'MSc Data Science · Manchester, UK', 'quote' => "My counsellor built a shortlist that actually matched my marks and budget. I got four offers — and a scholarship I didn't even know I could apply for."],
            ['initials' => 'AK', 'tone' => 'blue', 'name' => 'Aditya Kulkarni', 'meta' => 'PG Diploma, Business · Toronto, Canada', 'quote' => 'They practised the visa interview with me three times and updated my parents every week. The whole process felt calm instead of scary.'],
            ['initials' => 'SP', 'tone' => 'navy', 'name' => 'Sneha Patil', 'meta' => 'F&B Intern · Five-star resort, Dubai', 'quote' => 'Six months at a five-star resort in Dubai changed my CV completely. They handled the paperwork and prepared me for every interview.'],
        ];

        $faqs = [
            ['q' => 'Is the counselling really free?', 'a' => 'Yes. Your counselling sessions and profile assessment are free, with no obligation to sign up. Application and visa services are quoted upfront, with no hidden charges.'],
            ['q' => 'Do you also arrange internships abroad?', 'a' => 'Yes. We help students and recent graduates find international internships, with guidance on eligibility, duration, stipend and visas. Most placements are in hospitality, business and IT, in Dubai, Mauritius, France and the USA.'],
            ['q' => 'Do I need IELTS to study abroad?', 'a' => 'Most universities ask for an English test such as IELTS, PTE, TOEFL or Duolingo, though some accept waivers. Your counsellor will confirm exactly what each university on your shortlist needs.'],
            ['q' => 'When should I start the process?', 'a' => "Ideally 8–12 months before your intake, so there's time for tests, applications, scholarships and the visa. If your intake is sooner, talk to us anyway — we'll tell you honestly what's still possible."],
            ['q' => 'Can you help with scholarships and education loans?', 'a' => "Yes. We look for scholarships you're eligible for and help you compare education loans from leading Indian banks and NBFCs."],
            ['q' => 'Which countries do you cover?', 'a' => 'For study abroad: the UK, USA, Canada, Australia, Germany and Ireland. For internships: Dubai, Mauritius, France, the USA and the Maldives. Ask us about other countries too.'],
            ['q' => 'Can my parents join the counselling?', 'a' => "Absolutely. Parents are welcome on the call or at our office — it's a family decision, and we treat it like one."],
        ];

        return view('pages.home', compact('departures', 'destinations', 'steps', 'testimonials', 'faqs'));
    }

    /**
     * The about us page.
     */
    public function about(): View
    {
        $leaders = [
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
        ];

        $team = [
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
        ];

        return view('pages.about', compact('leaders', 'team'));
    }
}
