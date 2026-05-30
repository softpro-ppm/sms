<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterCapitalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_normalizes_full_name_to_title_case(): void
    {
        Mail::fake();

        $response = $this->post(route('register'), [
            'aadhar_number' => '123456789012',
            'full_name' => 'gULLa rAjEsH',
            'gender' => 'male',
            'qualification' => 'Graduation',
            'email' => 'rajesh.register@example.test',
            'whatsapp_number' => '9876543210',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/register/success');

        $student = Student::where('email', 'rajesh.register@example.test')->firstOrFail();
        $user = User::where('email', 'rajesh.register@example.test')->firstOrFail();

        $this->assertSame('Gulla Rajesh', $student->full_name);
        $this->assertSame('Gulla Rajesh', $user->name);
    }
}
