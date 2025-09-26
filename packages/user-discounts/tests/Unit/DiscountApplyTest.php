<?php

namespace UserDiscounts\Tests\Unit;

use PHPUnit\Framework\TestCase;
use UserDiscounts\DiscountService;

class DiscountApplyTest extends TestCase
{
    public function test_deterministic_application()
    {
        $svc = new DiscountService();
        // Simulate: two discounts, 10% and $20 fixed on $200 amount
        $result = $svc->apply(['PERCENT10','FIXED20'], 1, 200.0);
        $this->assertArrayHasKey('total',$result);
        $this->assertIsFloat($result['total']);
    }
}
