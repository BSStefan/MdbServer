<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cinemas')->insert([
            'name' => 'Cineplexx Big Beograd',
            'address' => 'Višnjička 84',
            'phone' => '+381 11 40 40 780',
            'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=616&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
        ]);
        DB::table('cinemas')->insert([
            'name' => 'Cineplexx Delta City',
            'address' => 'Jurija Gagarina 16/16A',
            'phone' => '+381 11 2203 400',
            'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=611&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
        ]);
        DB::table('cinemas')->insert([
            'name' => 'Cineplexx Kragujevac Plaza',
            'address' => 'Bulevar kraljice Marije 56',
            'phone' => '+381 34 619 50 30',
            'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=612&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
        ]);
        DB::table('cinemas')->insert([
            'name' => 'Cineplexx Niš',
            'address' => 'Bulevar Medijana 21',
            'phone' => '+381 18 300 340',
            'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=615&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
        ]);
        DB::table('cinemas')->insert([
            'name' => 'Cineplexx Ušće Shopping Centar',
            'address' => 'Bulevar Mihajla Pupina 4',
            'phone' => '+381 11 311 33 70',
            'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=614&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
        ]);
    }
}