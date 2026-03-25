<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    /**
     * Test the application returns a successful response for the home page
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_project_overview_page_returns_a_successful_response(): void
    {
        $response = $this->get('/project');

        $response->assertStatus(200);
    }
}
