<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Topic;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
		$user_ids = User::all()->pluck('id')->toArray();
		$categorys = Category::all()->pluck('id')->toArray();
		$faker = app(Faker\Generator::class);
		$topics = factory(Topic::class)
			->times(50)
			->make()
			->each(function ($topic, $index) use ($user_ids,$categorys,$faker) {
				$topic->user_id = $faker->randomElement($user_ids);
				$topic->category_id = $faker->randomElement($categorys);
        });

        Topic::insert($topics->toArray());
    }

}

