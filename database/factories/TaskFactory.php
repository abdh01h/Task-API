<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'       => User::all()->random()->id,
            'title'         => $this->faker->word,
            'description'   => $this->faker->paragraph,
            'due_date'      => $this->faker->date('Y-m-d'),
        ];
    }
}
