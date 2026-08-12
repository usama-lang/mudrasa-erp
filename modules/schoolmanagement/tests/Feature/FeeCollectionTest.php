<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SchoolManagement\Models\FeePayment;
use Modules\SchoolManagement\Models\Student;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    foreach (['school_campus.view', 'school_fee.view', 'school_fee.collect', 'school_fee.report', 'school_fee.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions(['school_campus.view', 'school_fee.view', 'school_fee.collect', 'school_fee.report', 'school_fee.delete']);
    $this->admin->assignRole($role);
});

test('accountant with permission can view fee index', function () {
    $response = $this->actingAs($this->admin)->get(route('school.fees.index'));

    $response->assertStatus(200);
});

test('user without permission cannot view fee index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('school.fees.index'))->assertStatus(403);
});

test('can collect a fee and generate a receipt', function () {
    $student = Student::factory()->create([
        'monthly_fee' => 1500,
        'admission_date' => now()->subMonths(2),
    ]);

    $response = $this->actingAs($this->admin)->post(route('school.fees.store'), [
        'student_id' => $student->id,
        'fee_month' => now()->format('Y-m'),
        'amount_paid' => 1500,
        'discount' => 0,
        'payment_method' => 'cash',
    ]);

    $payment = FeePayment::first();

    expect($payment)->not->toBeNull();
    $response->assertRedirect(route('school.fees.show', $payment->id));

    $this->assertDatabaseHas('school_fee_payments', [
        'student_id' => $student->id,
        'amount_paid' => 1500.00,
        'balance' => 0.00,
        'status' => 'paid',
    ]);

    expect($payment->receipt_no)->toStartWith('RCP-');
});

test('partial payment leaves a balance and partial status', function () {
    $student = Student::factory()->create([
        'monthly_fee' => 2000,
        'admission_date' => now()->subMonth(),
    ]);

    $this->actingAs($this->admin)->post(route('school.fees.store'), [
        'student_id' => $student->id,
        'fee_month' => now()->format('Y-m'),
        'amount_paid' => 1200,
        'discount' => 0,
        'payment_method' => 'cash',
    ]);

    $this->assertDatabaseHas('school_fee_payments', [
        'student_id' => $student->id,
        'balance' => 800.00,
        'status' => 'partial',
    ]);
});

test('cannot collect twice for the same month', function () {
    $student = Student::factory()->create([
        'monthly_fee' => 1500,
        'admission_date' => now()->subMonth(),
    ]);

    $payload = [
        'student_id' => $student->id,
        'fee_month' => now()->format('Y-m'),
        'amount_paid' => 1500,
        'discount' => 0,
        'payment_method' => 'cash',
    ];

    $this->actingAs($this->admin)->post(route('school.fees.store'), $payload);
    $this->actingAs($this->admin)->post(route('school.fees.store'), $payload);

    expect(FeePayment::where('student_id', $student->id)->count())->toBe(1);
});

test('student search returns enrolled students with pending months', function () {
    $student = Student::factory()->create([
        'student_name' => 'Muhammad Ali',
        'monthly_fee' => 1500,
        'admission_date' => now()->subMonths(2),
    ]);

    $response = $this->actingAs($this->admin)->getJson(route('school.fees.search-student', ['q' => 'Muhammad']));

    $response->assertStatus(200)
        ->assertJsonFragment(['student_name' => 'Muhammad Ali']);

    expect($response->json('0.pending_months'))->not->toBeEmpty();
});

test('fee report index is accessible with totals', function () {
    Student::factory()->create(['monthly_fee' => 1000, 'admission_date' => now()->subMonth()]);

    $response = $this->actingAs($this->admin)->get(route('school.fees.reports.index'));

    $response->assertStatus(200);
});
