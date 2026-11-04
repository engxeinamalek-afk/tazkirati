<?php

namespace App\Http\Requests;

use App\Enums\TicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            
            'tickets' => ['required', 'array', 'min:1'],
            
            'tickets.*' => ['required', 'array:type,quantity'],
            
            'tickets.*.type' => [
                'required',
                'string',
                'distinct',
                Rule::in(array_column(TicketType::cases(), 'value')),
                Rule::exists('event_ticket_types', 'type')->where('event_id', $this->input('event_id')),
            ],
            
            'tickets.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

}
