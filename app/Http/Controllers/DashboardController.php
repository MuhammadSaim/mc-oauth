<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MCGraphAPI;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{


    public function index(Request $request, MCGraphAPI $MCGraphAPI): View
    {
        return view('dashboard', [
            'link' => $MCGraphAPI->getAuthCodeUrl()
        ]);
    }


    public function mc_webhook(Request $request, MCGraphAPI $MCGraphAPI)
    {
        $state = $request->get('state');
        $code = $request->get('code');
        if($state && $code){
            if($MCGraphAPI->verifyAuthCode($state)){
                $data = $MCGraphAPI->getAccessToken($code);
                User::where('id', auth()->id())->update([
                    'mc_access_token' => $data['access_token'],
                    'mc_refresh_token' => $data['refresh_token'],
                ]);
                return redirect()->route('dashboard');
            }
            return redirect()->route('dashboard');
        }
        return redirect()->route('dashboard');
    }



}
