<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LINEBaseNotification;
use App\Services\RootInitiateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LINENotifySetupController extends Controller
{
    public function create()
    {
        $url = 'https://notify-bot.line.me/oauth/authorize?response_type=code&scope=notify';
        $url .= '&client_id='.config('line-notify.client_id');
        $url .= '&state='.csrf_token();
        $url .= '&redirect_uri='.route('line-notify.callback');

        return redirect()->to($url);
    }

    public function store(Request $request)
    {
        if ($request->input('error')) {
            return redirect()->route('dashboard')->withErrors(['error' => 'LINE notify: '.$request->input('error_description')]);
        }

        if (! $request->input('code')) {
            return redirect()->route('dashboard')->withErrors(['error' => 'LINE notify: Callback response error']);
        }

        $url = 'https://notify-bot.line.me/oauth/token';
        $response = Http::asForm()
            ->post($url, [
                'grant_type' => 'authorization_code',
                'code' => $request->input('code'),
                'redirect_uri' => route('line-notify.callback'),
                'client_id' => config('line-notify.client_id'),
                'client_secret' => config('line-notify.client_secret'),
            ]);

        if ($response->status() !== 200) {
            return redirect()->route('dashboard')->withErrors(['error' => 'LINE notify: '.$response->json('message')]);
        }

        $request->user()->update(['line_notify_token' => $response->json('access_token')]);

        $request->user()->notify(new LINEBaseNotification('LINE notify: Setup success'));

        $this->shouldInitRoot($request->user());

        return redirect()->route('dashboard')->with(['status' => 'LINE notify: '.$response->json('message')]);
    }

    protected function shouldInitRoot(User $user): void
    {
        $service = new RootInitiateService();
        if ($service->isRootInitiated()) {
            return;
        }

        $service->sendCode($user);
    }
}
