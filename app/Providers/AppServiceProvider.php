<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SerializerInterface::class, function() {
            return SerializerBuilder::create()
                ->setSerializationContextFactory(function () { // Create a new serialization context
                    return SerializationContext::create()
                    ->setSerializeNull(true);// Set to serialize null values
                })
                ->setCacheDir(storage_path('cache/jms-serializer'))// Set the cache directory
                ->setDebug(true)
                ->build();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
