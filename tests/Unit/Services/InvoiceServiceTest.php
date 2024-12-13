<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    private InvoiceService $invoiceService;
    private Customer $customer;
    private string $startDate;
    private string $endDate;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->invoiceService = new InvoiceService();
        $this->customer = Customer::factory()->create();
        
        $this->startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
    }

    public function testCreateInvoiceWithNoUsers()
    {
        $invoice = $this->invoiceService->createInvoice(
            $this->customer->id,
            $this->startDate,
            $this->endDate
        );

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($this->customer->id, $invoice->customer_id);
        $this->assertEquals($this->startDate, $invoice->start_date->toDateString());
        $this->assertEquals($this->endDate, $invoice->end_date->toDateString());
        $this->assertEquals(0, $invoice->total_amount);
        $this->assertEquals(0, $invoice->total_users);
    }

    public function testCreateInvoiceWithRegisteredUser()
    {
        User::factory()->create([
            'customer_id' => $this->customer->id,
            'registered_at' => Carbon::parse($this->startDate)->addDays(5),
            'activated_at' => null,
            'appointment_at' => null
        ]);

        $invoice = $this->invoiceService->createInvoice(
            $this->customer->id,
            $this->startDate,
            $this->endDate
        );

        $this->assertEquals(100, $invoice->total_amount); 
        $this->assertEquals(1, $invoice->total_users);
        $this->assertEquals(1, $invoice->registered_users);
        $this->assertEquals(0, $invoice->active_users);
        $this->assertEquals(0, $invoice->appointment_users);
    }

    public function testCreateInvoiceWithActivatedUser()
    {
        User::factory()->create([
            'customer_id' => $this->customer->id,
            'registered_at' => Carbon::parse($this->startDate)->subMonth(),
            'activated_at' => Carbon::parse($this->startDate)->addDays(5),
            'appointment_at' => null
        ]);

        $invoice = $this->invoiceService->createInvoice(
            $this->customer->id,
            $this->startDate,
            $this->endDate
        );

        $this->assertEquals(50, $invoice->total_amount); 
        $this->assertEquals(1, $invoice->total_users);
        $this->assertEquals(0, $invoice->registered_users);
        $this->assertEquals(1, $invoice->active_users);
        $this->assertEquals(0, $invoice->appointment_users);
    }

    public function testCreateInvoiceWithAppointmentUser()
    {
        User::factory()->create([
            'customer_id' => $this->customer->id,
            'registered_at' => Carbon::parse($this->startDate)->subMonth(),
            'activated_at' => Carbon::parse($this->startDate)->subMonth(),
            'appointment_at' => Carbon::parse($this->startDate)->addDays(5)
        ]);

        $invoice = $this->invoiceService->createInvoice(
            $this->customer->id,
            $this->startDate,
            $this->endDate
        );

        $this->assertEquals(200, $invoice->total_amount); 
        $this->assertEquals(1, $invoice->total_users);
        $this->assertEquals(0, $invoice->registered_users);
        $this->assertEquals(0, $invoice->active_users);
        $this->assertEquals(1, $invoice->appointment_users);
    }

    public function testCreateInvoiceWithMultipleEventsChargesHighestOnly()
    {
        User::factory()->create([
            'customer_id' => $this->customer->id,
            'registered_at' => Carbon::parse($this->startDate)->addDays(1), // 100 SAR
            'activated_at' => Carbon::parse($this->startDate)->addDays(5),  // 50 SAR
            'appointment_at' => Carbon::parse($this->startDate)->addDays(10) // 200 SAR
        ]);

        $invoice = $this->invoiceService->createInvoice(
            $this->customer->id,
            $this->startDate,
            $this->endDate
        );

        $this->assertEquals(200, $invoice->total_amount); 
        $this->assertEquals(1, $invoice->total_users);
        $this->assertEquals(1, $invoice->registered_users);
        $this->assertEquals(1, $invoice->active_users);
        $this->assertEquals(1, $invoice->appointment_users);
    }
} 