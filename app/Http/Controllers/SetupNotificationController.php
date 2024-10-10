<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetupNotificatiionRequest;
use Illuminate\Support\Facades\Http;

class SetupNotificationController
{
    public function index()
    {
        return view('setup-notification', [
            'title' => 'Setup Notification',
        ]);
    }

    public function store(StoreSetupNotificatiionRequest $request)
    {
        $user = $request->user();

        $profile = $user->profile ?? [];
        $profile['slack_webhook_url'] = $request->input('webhook_url');
        $user->profile = $profile;
        $user->save();

        Http::post($request->input('webhook_url'), [
            'text' => 'Slack notification setup successfully : Please say goodbye to LINE notify.',
        ]);

        return redirect()->route('dashboard')->with([
            'status' => 'Slack notification setup successfully : You should receive a slack message.',
        ]);
    }
}
