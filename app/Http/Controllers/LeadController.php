<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallbackRequest;
use App\Http\Requests\StoreCounsellingRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Store a "call me back" request from the home page or a custom page.
     */
    public function storeCallback(StoreCallbackRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pageSlug = $data['page_slug'] ?? null;

        Lead::create($data + [
            'source' => $pageSlug ? Lead::SOURCE_PAGE : Lead::SOURCE_CALLBACK,
            'status' => Lead::STATUS_NEW,
        ]);

        $redirectTo = $pageSlug ? url('/'.$pageSlug).'#callback' : route('home').'#callback';

        return redirect()->to($redirectTo)
            ->with('callback_sent', true);
    }

    /**
     * Show the free counselling eligibility form.
     */
    public function create(): View
    {
        $options = config('site.counselling');
        $lead = session('counselling_lead') ? Lead::find(session('counselling_lead')) : null;

        return view('pages.apply', [
            'levels' => $options['levels'],
            'countries' => $options['countries'],
            'intakes' => $options['intakes'],
            'lead' => $lead,
        ]);
    }

    /**
     * Store a completed free counselling form.
     */
    public function store(StoreCounsellingRequest $request): RedirectResponse
    {
        $lead = Lead::create($request->validated() + [
            'source' => Lead::SOURCE_COUNSELLING,
            'status' => Lead::STATUS_NEW,
            'consent' => true,
        ]);

        return redirect()->route('apply')->with('counselling_lead', $lead->id);
    }
}
