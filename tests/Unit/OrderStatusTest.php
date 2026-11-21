<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_requested_can_only_be_accepted_declined_or_cancelled(): void
    {
        $this->assertTrue(OrderStatus::Requested->canTransitionTo(OrderStatus::Accepted));
        $this->assertTrue(OrderStatus::Requested->canTransitionTo(OrderStatus::Declined));
        $this->assertTrue(OrderStatus::Requested->canTransitionTo(OrderStatus::Cancelled));
        $this->assertFalse(OrderStatus::Requested->canTransitionTo(OrderStatus::Completed));
        $this->assertFalse(OrderStatus::Requested->canTransitionTo(OrderStatus::Requested));
    }

    public function test_accepted_can_complete_or_cancel_but_declined_and_completed_are_terminal(): void
    {
        $this->assertTrue(OrderStatus::Accepted->canTransitionTo(OrderStatus::Completed));
        $this->assertTrue(OrderStatus::Accepted->canTransitionTo(OrderStatus::Cancelled));
        $this->assertSame([], OrderStatus::Declined->allowedTransitions());
        $this->assertSame([], OrderStatus::Completed->allowedTransitions());
        $this->assertSame([], OrderStatus::Cancelled->allowedTransitions());
    }
}
