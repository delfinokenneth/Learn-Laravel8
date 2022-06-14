<?php
 
namespace Database\Factories;
 
use Illuminate\Database\Eloquent\Factories\Factory;

 
class CommentFactory extends Factory
{
    public function definition()
    {
        return [
            'content' => $this->faker->text,
            'created_at' => $this->faker->dateTimeBetween('-3 months'),
        ];
    }
}