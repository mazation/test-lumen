<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $tsbegin = Carbon::now()->addDays(rand(1, 52))->addHours(rand(1, 12));
        return [
            'name' => $this->faker->text,
            'tsbegin' => $tsbegin->format("Y-m-d H:i:s"),
            'tsend' => $tsbegin->addHours(rand(1,3))->format("Y-m-d H:i:s"),
            'owner_id' => rand(1, 20)
        ];
    }
}
