<?php

namespace App\Providers;

use App\Services\Contracts\LLMServiceInterface;
use App\Services\GeminiService;
use App\Services\HuggingFaceService;
use App\Services\OllamaService;
use App\Services\OpenAIService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

 
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LLMServiceInterface::class, function () {
    
            return match (config('ai.provider')) {
                'openai' => new OpenAIService(),
                'huggingface' => new HuggingFaceService(),
                'ollama' => new OllamaService(),
                default => new GeminiService(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
      public function boot(): void
        {
            Paginator::useBootstrap();
        }

        

}
