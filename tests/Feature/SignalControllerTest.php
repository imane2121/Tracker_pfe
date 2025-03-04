<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Signal;
use App\Models\WasteTypes;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class SignalControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $wasteType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create();

        // Create a test waste type
        $this->wasteType = WasteTypes::create([
            'name' => 'Test Waste',
            'type' => 'general'
        ]);
    }

    public function test_normal_distance_report()
    {
        $this->actingAs($this->user);

        // Create first report
        $response = $this->post('/signal/store', [
            'location' => 'Location 1',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        // Wait for 31 minutes (simulated)
        $this->travel(31)->minutes();

        // Create second report with reasonable distance
        $response = $this->post('/signal/store', [
            'location' => 'Location 2',
            'latitude' => 48.8766, // About 2km away
            'longitude' => 2.3722,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        $response->assertRedirect(route('signal.thank-you'));
        $this->assertDatabaseHas('signals', [
            'anomalyFlag' => false,
            'status' => 'pending'
        ]);
    }

    public function test_anomalous_distance_report()
    {
        $this->actingAs($this->user);

        // Create first report
        $response = $this->post('/signal/store', [
            'location' => 'Paris',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        // Wait for just 5 minutes (simulated)
        $this->travel(5)->minutes();

        // Create second report with unreasonable distance (London coordinates)
        $response = $this->post('/signal/store', [
            'location' => 'London',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        $response->assertRedirect(route('signal.index'));
        $this->assertDatabaseHas('signals', [
            'anomalyFlag' => true,
            'status' => 'rejected'
        ]);
        $response->assertSessionHas('warning');
    }

    public function test_very_close_distance_report()
    {
        $this->actingAs($this->user);

        // Create first report
        $response = $this->post('/signal/store', [
            'location' => 'Location 1',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        // Create second report very close to first one (less than 1km)
        $response = $this->post('/signal/store', [
            'location' => 'Location 2',
            'latitude' => 48.8570,
            'longitude' => 2.3525,
            'volume' => 10,
            'waste_types' => [$this->wasteType->id],
            'description' => 'Test description'
        ]);

        $response->assertRedirect(route('signal.thank-you'));
        $this->assertDatabaseHas('signals', [
            'anomalyFlag' => false,
            'status' => 'pending'
        ]);
    }
} 