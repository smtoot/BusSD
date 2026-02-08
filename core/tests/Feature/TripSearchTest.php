<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TripSearchTest extends TestCase
{
    /**
     * Test validation of search API.
     */
    public function test_search_api_validation()
    {
        $response = $this->getJson('/api/v1/search');

        $response->assertStatus(422);
    }

    /**
     * Test search API with uplifted parameters validation.
     */
    public function test_search_api_parameters_validation()
    {
        $pickup_id = 1;
        $destination_id = 1;
        $date = Carbon::now()->format('Y-m-d');

        // Test with invalid sort_by
        $response = $this->getJson("/api/v1/search?pickup_id=$pickup_id&destination_id=$destination_id&date=$date&sort_by=invalid");
        $response->assertStatus(422);

        // Test with invalid departure_time
        $response = $this->getJson("/api/v1/search?pickup_id=$pickup_id&destination_id=$destination_id&date=$date&departure_time_start=25:00");
        $response->assertStatus(422);

        // Test with valid parameters (even if results are empty)
        $response = $this->getJson("/api/v1/search?pickup_id=$pickup_id&destination_id=$destination_id&date=$date&min_price=10&max_price=100&sort_by=price_asc");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'status']);
    }
}
