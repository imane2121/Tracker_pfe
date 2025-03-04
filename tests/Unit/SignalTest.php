<?php

namespace Tests\Unit;

use App\Models\Signal;
use PHPUnit\Framework\TestCase;

class SignalTest extends TestCase
{
    public function test_calculate_distance()
    {
        // Test case 1: Same point (should return 0)
        $distance = Signal::calculateDistance(0, 0, 0, 0);
        $this->assertEquals(0, round($distance, 2));

        // Test case 2: Known distance between two points
        // Paris (48.8566, 2.3522) to London (51.5074, -0.1278)
        $distance = Signal::calculateDistance(48.8566, 2.3522, 51.5074, -0.1278);
        $this->assertEquals(343.47, round($distance, 2));

        // Test case 3: Short distance (less than 1km)
        // Two points in the same city
        $distance = Signal::calculateDistance(48.8566, 2.3522, 48.8570, 2.3525);
        $this->assertLessThan(1, $distance);
    }
} 