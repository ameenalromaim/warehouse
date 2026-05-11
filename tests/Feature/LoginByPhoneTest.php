<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginByPhoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_with_normalized_yemen_style_phone(): void
    {
        User::factory()->create([
            'phone' => '771738225',
            'password' => Hash::make('secret-pass'),
            'email' => '771738225@test.local',
        ]);

        $response = $this->post('/login', [
            'phone' => '+967 771 738 225',
            'password' => 'secret-pass',
        ]);

        $response->assertRedirect(route('dashboard.purchases'));
        $this->assertAuthenticatedAs(User::where('phone', '771738225')->first());
    }

    public function test_login_succeeds_with_leading_zero_stored_phone(): void
    {
        User::factory()->create([
            'phone' => '0771123456',
            'password' => Hash::make('secret-pass'),
            'email' => '0771123456@test.local',
        ]);

        $response = $this->post('/login', [
            'phone' => '771123456',
            'password' => 'secret-pass',
        ]);

        $response->assertRedirect(route('dashboard.purchases'));
        $this->assertAuthenticated();
    }

    public function test_login_rejects_email_in_phone_field(): void
    {
        User::factory()->create([
            'phone' => '771738225',
            'password' => Hash::make('secret-pass'),
            'email' => 'user@test.local',
        ]);

        $response = $this->from('/login')->post('/login', [
            'phone' => 'user@test.local',
            'password' => 'secret-pass',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'phone' => '771738225',
            'password' => Hash::make('right-pass'),
            'email' => '771738225@test2.local',
        ]);

        $response = $this->from('/login')->post('/login', [
            'phone' => '771738225',
            'password' => 'wrong-pass',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }
}
