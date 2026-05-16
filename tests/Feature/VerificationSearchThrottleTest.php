<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class VerificationSearchThrottleTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_verify_search_returns_too_many_requests_after_burst(): void
    {
        $limit = (int) env('VERIFY_SEARCH_RATE_LIMIT_PER_MINUTE', 20);
        $burst = $limit + 1;

        for ($i = 0; $i < $burst; $i++) {
            $response = $this->post(route('verify.search'), [
                'search_term' => 'abc'.$i,
            ]);
            if ($i < $limit) {
                $response->assertRedirect();
            } else {
                $response->assertStatus(429);
            }
        }
    }

    public function test_legacy_verify_search_route_is_throttled(): void
    {
        $limit = (int) env('VERIFY_SEARCH_RATE_LIMIT_PER_MINUTE', 20);
        $burst = $limit + 1;

        for ($i = 0; $i < $burst; $i++) {
            $response = $this->post(route('public.student-verification.search'), [
                'search_term' => 'xyz'.$i,
            ]);
            if ($i < $limit) {
                $response->assertRedirect();
            } else {
                $response->assertStatus(429);
            }
        }
    }
}
