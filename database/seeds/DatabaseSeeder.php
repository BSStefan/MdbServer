<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CinemaSeeder::class);
        $this->call(GenreSeeder::class);
        $this->call(DirectorSeeder::class);
        $this->call(ActorSeeder::class);
        $this->call(WriterSeeder::class);
        //$this->call(MovieSeeder::class);
    }
}
