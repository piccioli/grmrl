<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationConfirmation;
use App\Models\Activity;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function showForm(): View
    {
        $activities = Activity::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (Activity $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'description' => $a->description,
                'meeting_time' => $a->meeting_time,
                'meeting_place' => $a->meeting_place,
                'available_spots' => $a->availableSpots(),
                'is_full' => $a->isFull(),
            ]);

        return view('registration.form', compact('activities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $isCaiMember = $request->boolean('is_cai_member');

        $request->validate([
            'first_name'                    => ['required', 'string', 'max:255'],
            'last_name'                     => ['required', 'string', 'max:255'],
            'email'                         => ['required', 'email', 'max:255'],
            'phone'                         => ['required', 'string', 'max:50'],
            'birth_date'                    => ['required', 'date'],
            'is_cai_member'                 => ['nullable', 'boolean'],
            'cai_section_id'                => [Rule::requiredIf($isCaiMember), 'nullable', 'exists:cai_sections,id'],
            'fiscal_code'                   => [Rule::requiredIf(! $isCaiMember), 'nullable', 'string', 'size:16'],
            'activity_id'                   => ['required', 'integer', 'exists:activities,id'],
            'privacy_accepted'              => ['accepted'],
            'photo_release_accepted'        => ['accepted'],
            'rules_accepted'               => ['accepted'],
            'weather_cancellation_accepted' => ['accepted'],
            'equipment_check_accepted'      => ['accepted'],
            'minors'                        => ['nullable', 'array', 'max:3'],
            'minors.*.first_name'           => ['required', 'string', 'max:255'],
            'minors.*.last_name'            => ['required', 'string', 'max:255'],
            'minors.*.birth_date'           => ['required', 'date'],
        ]);

        $registration = null;

        DB::transaction(function () use ($request, &$registration) {
            $activity = Activity::lockForUpdate()->findOrFail($request->integer('activity_id'));

            if ($activity->isFull()) {
                return;
            }

            $reg = Registration::create([
                'first_name'                    => $request->input('first_name'),
                'last_name'                     => $request->input('last_name'),
                'email'                         => $request->input('email'),
                'phone'                         => $request->input('phone'),
                'birth_date'                    => $request->input('birth_date'),
                'is_cai_member'                 => $request->boolean('is_cai_member'),
                'cai_section_id'                => $request->input('cai_section_id') ?: null,
                'fiscal_code'                   => $request->input('fiscal_code') ?: null,
                'activity_id'                   => $request->integer('activity_id'),
                'privacy_accepted'              => true,
                'photo_release_accepted'        => true,
                'rules_accepted'               => true,
                'weather_cancellation_accepted' => true,
                'equipment_check_accepted'      => true,
            ]);

            foreach ($request->input('minors', []) as $minorData) {
                $reg->minors()->create([
                    'first_name'     => $minorData['first_name'],
                    'last_name'      => $minorData['last_name'],
                    'birth_date'     => $minorData['birth_date'],
                    'is_cai_member'  => ! empty($minorData['is_cai_member']),
                    'cai_section_id' => $minorData['cai_section_id'] ?? null ?: null,
                    'fiscal_code'    => $minorData['fiscal_code'] ?? null ?: null,
                ]);
            }

            $registration = $reg;
        });

        if ($registration === null) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['activity_id' => "Siamo spiacenti, i posti per questa attività sono esauriti. Scegli un'altra attività."]);
        }

        Mail::to($registration->email)->send(new RegistrationConfirmation($registration));

        return redirect()->route('registrations.confirm')->with('email', $registration->email);
    }

    public function confirm(): View
    {
        return view('registration.confirm');
    }
}
