<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    protected $model = ShortUrl::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'company_id' => Company::factory(),
            'original_url' => fake()->url(),
            'short_code' => fake()->unique()->bothify('??????'),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'clicks' => 0,
            'is_active' => true,
            'expires_at' => null,
        ];
    }
}
