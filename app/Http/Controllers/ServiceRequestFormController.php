<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestForm;
use App\Models\User;
use App\Notifications\LINEBaseNotification;
use App\Services\RoleUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ServiceRequestFormController extends Controller
{
    protected Collection $services;

    public function __construct()
    {
        $this->services = collect(RoleUserService::SERVICES);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        return view('service-request-form.index')->with([
            'title' => 'Request Service',
            'services' => $this->services,
            'forms' => ServiceRequestForm::query()
                ->with('requester')
                ->when($user->cannot('approve_request_form'), fn ($query) => $query->where('requester_id', $user->id))
                ->when($user->can('approve_request_form'), fn ($query) => $query->with('requester'))
                ->orderByDesc('created_at')
                ->get()
                ->transform(fn ($form) => $this->transform($form, $user)),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(RoleUserService::FORM_RULES);

        $form = [];
        $this->services->pluck('name')->each(function ($service) use (&$form, $validated) {
            if (! isset($validated[$service])) {
                $form[$service] = false;

                return;
            }

            $form[$service] = $validated[$service] === 'on'
                ? true
                : $validated[$service];
        });

        $form['note'] = strip_tags($validated['note']);
        $form['response'] = null;

        ServiceRequestForm::query()
            ->create([
                'requester_id' => $request->user()->id,
                'form' => $form,
            ]);

        $request->user()->notify(new LINEBaseNotification('You just submitted the service request form.'));

        return redirect()->route('dashboard')->with(['status' => 'Service request form submitted.']);
    }

    public function edit(ServiceRequestForm $form, Request $request)
    {
        $user = $request->user();

        $data = [];
        $form->requester;
        $data['form'] = $this->transform($form, $user);
        if ($user->can('response', $form)) {
            $data['title'] = 'Response';
            $data['action'] = [
                'url' => route('service-request-forms.update', $form->hashed_key),
                'label' => 'Response',
            ];
        } elseif ($user->can('revoke', $form)) {
            $data['title'] = 'Revoke';
            $data['action'] = [
                'url' => route('service-request-forms.revoke', $form->hashed_key),
                'label' => 'Revoke',
            ];
        }

        return view('service-request-form.edit')->with($data);
    }

    public function update(ServiceRequestForm $form, Request $request)
    {
        $validated = $request->validate([
            'response' => ['required', 'string', 'in:approved,disapproved'],
            'reply' => ['required_if:response,disapproved', 'nullable', 'string', 'min:32', 'max:256'],
        ]);

        $form->status = $validated['response'];
        $form->form['response'] = strip_tags($validated['reply']);
        $form->authority_id = $request->user()->id;
        $form->save();

        if ($form->status === 'disapproved') {
            $form->requester->notify(new LINEBaseNotification('Your service request was disapproved. Reply: '.$validated['reply']));
        } elseif ($form->status === 'approved') {
            (new RoleUserService)->attachRoles($form);
            $form->requester->notify(new LINEBaseNotification('Your service request was approved. '.$validated['reply']));
        }

        return redirect()->route('dashboard')->with(['status' => 'Service request form responded.']);
    }

    public function destroy(ServiceRequestForm $form)
    {
        $form->status = 'canceled';
        $form->save();
        $form->requester->notify(new LINEBaseNotification('Service request form canceled.'));

        return redirect()->route('dashboard')->with(['status' => 'Service request form canceled.']);
    }

    protected function transform(ServiceRequestForm $form, User $user): array
    {
        $data['submitted_at'] = $form->created_at->tz('asia/bangkok')->format('Y-m-d H:i:s');
        $requests = $form->form->filter(fn ($value, $key) => $value === true)->keys()->toArray();
        $requests = $this->services->filter(fn ($service) => in_array($service['name'], $requests))->pluck('label')->toArray();
        $data['hashed_key'] = $form->hashed_key;
        $data['requests'] = $requests;
        $data['status'] = $form->status;
        $data['note'] = $form->form['note'] ?? null;
        if ($form->relationLoaded('requester')) {
            $data['requester'] = $form->requester->full_name;
        }
        $data['actions'] = collect([
            [
                'type' => 'form',
                'method' => 'delete',
                'label' => 'Cancel',
                'url' => route('service-request-forms.destroy', $form->hashed_key),
                'can' => $user->can('cancel', $form),
            ],
            [
                'type' => 'link',
                'label' => 'Response',
                'url' => route('service-request-forms.edit', $form->hashed_key),
                'can' => $user->can('response', $form),
            ],
            [
                'type' => 'form',
                'method' => 'patch',
                'label' => 'Revoke',
                'url' => route('service-request-forms.revoke', $form->hashed_key),
                'can' => $user->can('revoke', $form),
            ],
        ])->filter(fn ($action) => $action['can']);

        return $data;
    }
}
