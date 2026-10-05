<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test public landing page loads properly.
     */
    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Koperasi BMI');
    }

    /**
     * Test unauthenticated guest is redirected when accessing dashboard.
     */
    public function test_guest_is_redirected_to_login_on_protected_routes(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test login page loads properly.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Koperasi BMI');
    }
}
