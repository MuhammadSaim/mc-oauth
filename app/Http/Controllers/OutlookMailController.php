<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OutlookMailService;

class OutlookMailController extends Controller
{

    public function index(OutlookMailService $outlookMailService)
    {
        $user = User::where('id', auth()->id())->first();
        $outlookMailService->send_mail(
            $user->mc_access_token,
            'This is test email from outlook',
                    'this is the content of an email',
            [
                [
                    'emailAddress' => [
                        'muhammadsaim494@gmail.com'
                    ]
                ]
            ]
        );
    }

}
