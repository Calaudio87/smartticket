<?php

namespace App\Providers;

use App\Contracts\TicketAnalyzerInterface;
use App\Services\Triage\MockTicketAnalyzer;
use App\Services\Triage\OpenAITicketAnalyzer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TicketAnalyzerInterface::class, function ($app) {
            return config('services.triage.driver', 'mock') === 'ai'
                ? new OpenAITicketAnalyzer()
                : new MockTicketAnalyzer();
        });
    }

    public function boot(): void
    {
        //
    }
}
