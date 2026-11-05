<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SendEmailRequest;
use App\Models\Event;
use App\Services\EmailService;
class EventNotificationController extends Controller
{
    public function sendEmail(SendEmailRequest $request, Event $event,EmailService $service)
    {
        $validatedData = $request->validated();
        $subject = $validatedData['subject'];
        $body = $validatedData['body'];

        $service->sendEmail($event, $subject, $body);


        return response()->json(['message' => 'Promotional email is being sent.'], 200);
    }
}
