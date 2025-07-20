<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * Test that all main public routes are accessible
     */
    public function test_public_routes_are_accessible()
    {
        // Test home route
        $response = $this->get('/');
        $response->assertStatus(200);

        // Test jobs index route
        $response = $this->get('/jobs');
        $response->assertStatus(200);

        // Test news index route
        $response = $this->get('/news');
        $response->assertStatus(200);

        // Test login page
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Test register page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // Test company register page
        $response = $this->get('/register/company');
        $response->assertStatus(200);
    }

    /**
     * Test that route names exist
     */
    public function test_route_names_exist()
    {
        $this->assertTrue(route('home') !== null);
        $this->assertTrue(route('jobs.index') !== null);
        $this->assertTrue(route('news.index') !== null);
        $this->assertTrue(route('login') !== null);
        $this->assertTrue(route('register') !== null);
        $this->assertTrue(route('company.register') !== null);
    }
}
