<?php

namespace ESolution\DBEncryption\Tests\Database\Factories;

use ESolution\DBEncryption\Tests\TestUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TestUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'     => fake()->name,
            'email'    => fake()->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ];
    }
}
