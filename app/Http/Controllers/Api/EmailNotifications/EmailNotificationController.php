<?php

namespace App\Http\Controllers\Api\EmailNotifications;

use App\Exceptions\EmailNotificationException;
use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Http\Request;

class EmailNotificationController extends Controller
{
    public function sendTestEmailNotification(Request $request, Merchant $merchant, $group, $type)
    {
        $request->validate([
            'to_email' => 'required|email',
            'to_name'  => 'string|max:191',
        ]);

        $group = str_replace('-', '_', $group);
        $type = str_replace('-', '_', $type);

        $notification_type = $group.'_'.$type;

        $to_name = $request->get('to_name') ? htmlspecialchars($request->get('to_name')) : '';
        $to_email = $request->get('to_email') ?: '';

        $tags = [];

        try {
            app('email_notification')->send($notification_type, $merchant->id, $to_name, $to_email, $tags);
        } catch (EmailNotificationException $exception) {
            return response()->json([
                'message' => 'An error occurred while attempting to send email. '.$exception->getMessage(),
            ], 500);
        }
    }
}