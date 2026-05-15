<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_response_includes_x_content_type_options_nosniff(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_response_includes_x_frame_options_deny(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_response_includes_referrer_policy(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_response_includes_permissions_policy(): void
    {
        $response = $this->get('/');

        $this->assertNotEmpty($response->headers->get('Permissions-Policy'));
    }

    public function test_response_includes_csp_header(): void
    {
        $response = $this->get('/');

        $this->assertNotEmpty(
            $response->headers->get('Content-Security-Policy-Report-Only')
            ?? $response->headers->get('Content-Security-Policy')
        );
    }
}
