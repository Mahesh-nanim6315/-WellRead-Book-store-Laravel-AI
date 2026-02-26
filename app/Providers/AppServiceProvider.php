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
        // $this->app->bind(LLMServiceInterface::class, function () {
    
        //     return match (config('ai.provider')) {
        //         'openai' => new OpenAIService(),
        //         'huggingface' => new HuggingFaceService(),
        //         'ollama' => new OllamaService(),
        //         default => new GeminiService(),
        //     };
        // });
        $this->app->bind(LLMServiceInterface::class, function () {
        $provider = config('ai.provider');
        $providers = config('ai.providers');

        if (! isset($providers[$provider])) {
            throw new \RuntimeException("Invalid AI provider: {$provider}");
        }

        return app($providers[$provider]);
    });

    // dd(config('ai.provider'));
    }

    /**
     * Bootstrap any application services.
     */
      public function boot(): void
        {
            Paginator::useBootstrap();
        }

        

}
