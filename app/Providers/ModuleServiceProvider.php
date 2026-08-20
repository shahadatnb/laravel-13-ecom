<?php

namespace App\Providers;

use App\Repositories\CouponRepository;
use App\Repositories\CouponRepositoryInterface;
use App\Repositories\CustomerProfileRepository;
use App\Repositories\CustomerProfileRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Repositories\InventoryRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Services\CouponService;
use App\Services\CouponServiceInterface;
use App\Services\CustomerService;
use App\Services\CustomerServiceInterface;
use App\Services\InventoryService;
use App\Services\InventoryServiceInterface;
use App\Services\OrderService;
use App\Services\OrderServiceInterface;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerProfileRepositoryInterface::class, CustomerProfileRepository::class);
        $this->app->bind(CustomerServiceInterface::class, CustomerService::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(CouponServiceInterface::class, CouponService::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);
    }

    public function boot(): void
    {
    }
}
