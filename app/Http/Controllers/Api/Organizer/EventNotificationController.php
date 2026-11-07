<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SendEmailRequest;
use App\Models\Event;
use App\Services\EmailService;
use App\Traits\ApiResponse;

class EventNotificationController extends Controller
{
    use ApiResponse;
    public function sendEmail(SendEmailRequest $request, Event $event,EmailService $service)
    {
        $validatedData = $request->validated();
        $subject = $validatedData['subject'];
        $body = $validatedData['body'];

        $service->sendEmail($event, $subject, $body);

        return $this->successResponse(message:'Promotional email is being sent.');
    }
}
