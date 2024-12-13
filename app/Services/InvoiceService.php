<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceService
{
    private const REGISTRATION_PRICE = 100;
    private const ACTIVATION_PRICE = 50;
    private const APPOINTMENT_PRICE = 200;

    public function createInvoice(int $customerId, string $startDate, string $endDate): Invoice
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $invoice = new Invoice();
        $invoice->customer_id = $customerId;
        $invoice->start_date = $startDate;
        $invoice->end_date = $endDate;

        // Get all billable events
        $events = $this->collectInvoiceEvents($startDate, $endDate, $customerId);
        
        // Calculate metrics
        $userMetrics = $this->calculateUserMetrics($events, $customerId, $startDate, $endDate);
        $invoice->total_users = $userMetrics['total_users'];
        $invoice->active_users = $userMetrics['active_users'];
        $invoice->registered_users = $userMetrics['registered_users'];
        $invoice->appointment_users = $userMetrics['appointment_users'];
        
        // Calculate total amount by taking the highest price per user
        $invoice->total_amount = $events->groupBy('user_id')
            ->map(function ($userEvents) {
                return $userEvents->max('price');
            })
            ->sum();

        $invoice->save();

        // Store events
        $invoice->events()->createMany($events->toArray());

        return $invoice;
    }

    private function collectInvoiceEvents(Carbon $startDate, Carbon $endDate, int $customerId): Collection
    {
        $events = collect();
        $users = User::where('customer_id', $customerId)->get();

        foreach ($users as $user) {
            $userEvents = collect();

            // Check registration event
            if ($user->registered_at && 
                $user->registered_at >= $startDate && 
                $user->registered_at <= $endDate) {
                $userEvents->push([
                    'user_id' => $user->id,
                    'type' => 'registration',
                    'date' => $user->registered_at,
                    'price' => self::REGISTRATION_PRICE
                ]);
            }

            // Check activation event
            if ($user->activated_at && 
                $user->activated_at >= $startDate && 
                $user->activated_at <= $endDate) {
                $userEvents->push([
                    'user_id' => $user->id,
                    'type' => 'activation',
                    'date' => $user->activated_at,
                    'price' => self::ACTIVATION_PRICE
                ]);
            }

            // Check appointment event
            if ($user->appointment_at && 
                $user->appointment_at >= $startDate && 
                $user->appointment_at <= $endDate) {
                $userEvents->push([
                    'user_id' => $user->id,
                    'type' => 'appointment',
                    'date' => $user->appointment_at,
                    'price' => self::APPOINTMENT_PRICE
                ]);
            }

            // Add all events to the main collection
            $events = $events->concat($userEvents);
        }

        return $events;
    }

    private function calculateUserMetrics(Collection $events, int $customerId, Carbon $startDate, Carbon $endDate): array
    {
        $uniqueUsers = $events->unique('user_id');
        $total_users = $uniqueUsers->count();
        
        $active_users = User::where('customer_id', $customerId)
            ->whereNotNull('activated_at')
            ->whereBetween('activated_at', [$startDate, $endDate])
            ->count();

        $registered_users = User::where('customer_id', $customerId)
            ->whereNotNull('registered_at')
            ->whereBetween('registered_at', [$startDate, $endDate])
            ->count();

        $appointment_users = User::where('customer_id', $customerId)
            ->whereNotNull('appointment_at')
            ->whereBetween('appointment_at', [$startDate, $endDate])
            ->count();

        return [
            'total_users' => $total_users,
            'active_users' => $active_users,
            'registered_users' => $registered_users,
            'appointment_users' => $appointment_users
        ];
    }

    public function getInvoiceDetails(Invoice $invoice): array
    {
        $events = $invoice->events()
            ->with('user:id,name,email')
            ->get();

        $eventFrequency = $events->groupBy('type')
            ->map(fn($group) => $group->count());

        $userDetails = $events->groupBy('user_id')
            ->map(function ($userEvents) use ($invoice) {
                $user = $userEvents->first()->user;
                
                // Get all sessions for this user within the invoice period
                $sessions = UserSession::where('user_id', $user->id)
                    ->where(function ($query) {
                        $query->whereNotNull('activated_at')
                              ->orWhereNotNull('appointment_at');
                    })
                    ->whereBetween('activated_at', [$invoice->start_date, $invoice->end_date])
                    ->orWhereBetween('appointment_at', [$invoice->start_date, $invoice->end_date])
                    ->get();

                return [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'events' => $userEvents->map(fn($event) => [
                        'type' => $event->type,
                        'date' => $event->date,
                        'price' => $event->price
                    ]),
                    'sessions' => $sessions->map(fn($session) => [
                        'id' => $session->id,
                        'activation_date' => $session->activated_at,
                        'appointment_date' => $session->appointment_at
                    ])
                ];
            })
            ->values();

        return [
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'period' => [
                'start' => $invoice->start_date,
                'end' => $invoice->end_date
            ],
            'events' => $events->map(fn($event) => [
                'type' => $event->type,
                'date' => $event->date,
                'price' => $event->price,
                'user_id' => $event->user_id
            ]),
            'event_frequency' => $eventFrequency,
            'price_per_event' => [
                'registration' => self::REGISTRATION_PRICE,
                'activation' => self::ACTIVATION_PRICE,
                'appointment' => self::APPOINTMENT_PRICE
            ],
            'total_amount' => $invoice->total_amount,
            'user_details' => $userDetails,
            'user_metrics' => [
                'total_users' => $invoice->total_users,
                'active_users' => $invoice->active_users,
                'registered_users' => $invoice->registered_users,
                'appointment_users' => $invoice->appointment_users
            ]
        ];
    }
}
