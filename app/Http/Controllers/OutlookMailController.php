<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OutlookMailService;

class OutlookMailController extends Controller
{

    public function index(OutlookMailService $outlookMailService)
    {
        $user = User::where('id', auth()->id())->first();
        $data = $outlookMailService->send_mail(
            $user,
            'This is test email from outlook using Graph API Laravel',
                    'this is the content of an email TO Laravel',
            [
                [
                    'emailAddress' => [
                        'address' => 'muhammadsaim494@gmail.com'
                    ]
                ]
            ]
        );

        dd($data);
    }

}
