<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MovieSeeder extends Seeder
{
    private $movies
        = [
            [1, 550, 1, "Fight Club", "Fight Club", 63000000, "http://www.foxmovies.com/movies/fight-club", "A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy. Their concept catches on, with underground \"fight clubs\" forming in every town, until an eccentric gets in the way and ignites an out-of-control spiral toward oblivion.", "en", "Mischief. Mayhem. Soap.", "1999-10-15", "139", "images/movies/369a08858d607df8c8ee57bb3d8011c3.jpg", false],
            [2, 203835, 2, "Amityville: The Awakening", "Amityville: The Awakening", 0, "", "Belle, her little sister, and her comatose twin brother move into a new house with their single mother Joan in order to save money to help pay for her brother\'s expensive healthcare. But when strange phenomena begin to occur in the house including the miraculous recovery of her brother, Belle begins to suspect her Mother isn\'t telling her everything and soon realizes they just moved into the infamous Amityville house.", "en", "Every house has a history. This one has a legend.", "2017-06-30", "85", "images/movies/f804e7f56e3c47d132191c8f8bded66b.jpg", true],
            [3, 396422, 3, "Annabelle: Creation", "Annabelle: Creation", 15000000, "http://annabellemovie.com", "Several years after the tragic death of their little girl, a dollmaker and his wife welcome a nun and several girls from a shuttered orphanage into their home, soon becoming the target of the dollmaker's possessed creation, Annabelle.", "en", "You don't know the real story", "2017-08-09", "109", "images/movies/6946900ac0a1b7871c531fdcdd37bf08.jpg", true],
            [4, 341013, 4, "Atomic Blonde", "Atomic Blonde", 30000000, "http://www.atomicblonde.com/", "An undercover MI6 agent is sent to Berlin during the Cold War to investigate the murder of a fellow agent and recover a missing list of double agents.", "en", "", "2017-07-26", "115", "images/movies/22ae6748ba07a0167e9ef984da3daf64.jpg", true],
            [5, 260514, 5, "Cars 3", "Cars 3", 175000000, "http://cars.disney.com", "Blindsided by a new generation of blazing-fast racers, the legendary Lightning McQueen is suddenly pushed out of the sport he loves. To get back in the game, he will need the help of an eager young race technician with her own plan to win, inspiration from the late Fabulous Hudson Hornet, and a few unexpected turns. Proving that #95 isn't through yet will test the heart of a champion on Piston Cup Racing’s biggest stage!", "en", "From this moment, everything will change", "2017-06-15", "109", "images/movies/1ae8ad3b438fd2ea3f6c8158e0c7b789.jpg", true],
            [6, 297762, 6, "Wonder Woman", "Wonder Woman", 149000000, "http://www.warnerbros.com/wonder-woman", "An Amazon princess comes to the world of Man to become the greatest of the female superheroes.", "en", "Power. Grace. Wisdom. Wonder.", "2017-05-30", "141", "images/movies/6bdd7f88d6f20d7cd33d15269961e163.jpg", true],
            [7, 339846, 7, "Baywatch", "Baywatch", 69000000, "http://www.thebaywatchmovie.com/", "Devoted lifeguard Mitch Buchannon butts heads with a brash new recruit. Together, they uncover a local criminal plot that threatens the future of the Bay.", "en", "Don't worry, summer is coming", "2017-05-12", "116", "images/movies/26adabfe6bb97f25e34c07df26c859bb.jpg", true],
            [8, 374720, 8, "Dunkirk", "Dunkirk", 150000000, "http://www.dunkirkmovie.com/", "Miraculous evacuation of Allied soldiers from Belgium, Britain, Canada, and France, who were cut off and surrounded by the German army from the beaches and harbor of Dunkirk, France, between May 26 and June 04, 1940, during Battle of France in World War II.", "en", "The event that shaped our world", "2017-07-19", "107", "images/movies/bd60ad890f481d2b2aab2c9ce5f884bd.jpg", true],
            [9, 378236, 9, "The Emoji Movie", "The Emoji Movie", 50000000, "http://www.theemoji-movie.com/", "Gene, a multi-expressional emoji, sets out on a journey to become a normal emoji.", "en", "Not easy being meh", "2017-07-28", "91", "images/movies/cec39b2e10d807eee78bed846affeb10.jpg", true],
            [10, 324852, 10, "Despicable Me 3", "Despicable Me 3", 0, "http://www.despicable.me", "Gru and his wife Lucy must stop former '80s child star Balthazar Bratt from achieving world domination.", "en", "Oh brother.", "2017-06-15", "96", "images/movies/68b88b84dbf5726aa97824136b02e333.jpg", true],
            [11, 397422, 11, "Rough Night", "Rough Night", 0, "http://www.roughnightmovie.com/", "Five best friends from college reunite 10 years later for a wild bachelorette weekend in Miami. Their hard partying takes a hilariously dark turn when they accidentally kill a male stripper. Amidst the craziness of trying to cover it up, they're ultimately brought closer together when it matters most.", "en", "The hangover will be the least of their problems", "2017-06-16", "101", "images/movies/46e9a4e5c89e1daea8eb9ce2f0325681.jpg", true],
            [12, 432787, 12, "Fun Mom Dinner", "Fun Mom Dinner", 0, "", "Four women, whose kids attend the same preschool class, get together for a \"fun mom dinner.\" When the night takes an unexpected turn, these unlikely new friends realize they have more in common than just marriage and motherhood.", "en", "Every mom needs a time out.", "2017-08-04", "89", "images/movies/890d5a642e5ccbec2517751862a09008.jpg", true],
            [13, 413391, 13, "A Few Less Men", "A Few Less Men", 0, "", "Travel plans for three men in ill-fitting wedding tuxedos goes horribly wrong.", "en", "A lot more laughs!", "2017-03-09", "96", "images/movies/e2b00ed6761730ad044884bcb5740c4c.jpg", true],
            [14, 353491, 14, "The Dark Tower", "The Dark Tower", 0, "http://thedarktower-movie.com", "The last Gunslinger, Roland Deschain, has been locked in an eternal battle with Walter O’Dim, also known as the Man in Black, determined to prevent him from toppling the Dark Tower, which holds the universe together. With the fate of the worlds at stake, good and evil will collide in the ultimate battle as only Roland can defend the Tower from the Man in Black.", "en", "There are other worlds than these", "2017-08-03", "95", "images/movies/2c2c562cda1b73ac55fc72210b4eceeb.jpg", true],
            [15, 439152, 15, "Like Mother, Like Daughter", "Telle mère, telle fille", 0, "", "An attention-craving mother nearing 50, unemployed and living with her pregnant daughter and son-in-law, suddenly finds herself with child, too...", "fr", "Baby Bump(s)", "2017-03-29", "94", "images/movies/6d46bfde5dbbc6f6244d23c6e3911693.jpg", true],
            [16, 166426, 16, "Pirates of the Caribbean: Dead Men Tell No Tales", "Pirates of the Caribbean: Dead Men Tell No Tales", 230000000, "http://pirates.disney.com/pirates-of-the-caribbean-dead-men-tell-no-tales", "Thrust into an all-new adventure, a down-on-his-luck Capt. Jack Sparrow feels the winds of ill-fortune blowing even more strongly when deadly ghost sailors led by his old nemesis, the evil Capt. Salazar, escape from the Devil's Triangle. Jack's only hope of survival lies in seeking out the legendary Trident of Poseidon, but to find it, he must forge an uneasy alliance with a brilliant and beautiful astronomer and a headstrong young man in the British navy.", "en", "", "2017-05-23", "129", "images/movies/e34868d08138aab5e6193dfdbd532794.jpg", true],
            [18, 281338, 17, "War for the Planet of the Apes", "War for the Planet of the Apes", 152000000, "http://www.foxmovies.com/movies/war-for-the-planet-of-the-apes", "Caesar and his apes are forced into a deadly conflict with an army of humans led by a ruthless Colonel. After the apes suffer unimaginable losses, Caesar wrestles with his darker instincts and begins his own mythic quest to avenge his kind. As the journey finally brings them face to face, Caesar and the Colonel are pitted against each other in an epic battle that will determine the fate of both their species and the future of the planet.", "en", "For freedom. For family. For the planet.", "2017-07-11", "140", "images/movies/80aa0606071f6c1d87fe1bf2d6e93bee.jpg", true],
            [19, 315635, 18, "Spider-Man: Homecoming", "Spider-Man: Homecoming", 175000000, "http://www.spidermanhomecoming.com", "Following the events of Captain America: Civil War, Peter Parker, with the help of his mentor Tony Stark, tries to balance his life as an ordinary high school student in Queens, New York City, with fighting crime as his superhero alter ego Spider-Man as a new threat, the Vulture, emerges.", "en", "Homework can wait. The city can't.", "2017-07-05", "133", "images/movies/10903e4253d5b1e5d0b4818ca17b3089.jpg", true],
            [20, 335777, 19, "The Nut Job 2: Nutty by Nature", "The Nut Job 2: Nutty by Nature", 0, "http://thenutjob.com", "When the evil mayor of Oakton decides to bulldoze Liberty Park and build a dangerous amusement park in its place, Surly Squirrel and his ragtag group of animal friends need to band together to save their home, defeat the mayor, and take back the park.", "en", "Get ready. Get set. Get nuts!", "2017-08-11", "91", "images/movies/8e7052148a51265c949c3bacb9ccbfb8.jpg", true],
            [21, 339964, 20, "Valerian and the City of a Thousand Planets", "Valerian and the City of a Thousand Planets", 197471676, "", "In the 28th century, Valerian and Laureline are special operatives charged with keeping order throughout the human territories. On assignment from the Minister of Defense, the two undertake a mission to Alpha, an ever-expanding metropolis where species from across the universe have converged over centuries to share knowledge, intelligence, and cultures. At the center of Alpha is a mysterious dark force which threatens the peaceful existence of the City of a Thousand Planets, and Valerian and Laureline must race to identify the menace and safeguard not just Alpha, but the future of the universe.", "en", "", "2017-07-20", "137", "images/movies/fe655177d9156a2e93eae5a11e0bfc81.jpg", true],
            [22, 339403, 21, "Baby Driver", "Baby Driver", 34000000, "", "After being coerced into working for a crime boss, a young getaway driver finds himself taking part in a heist doomed to fail.", "en", "All you need is one killer track.", "2017-06-28", "113", "images/movies/7547f7478e4412811e2b0038f471d50a.jpg", true]
        ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->movies as $movie)
        {
            DB::table('movies')->insert([
                'id'             => $movie[0],
                'tmdb_id'        => $movie[1],
                'director_id'    => $movie[2],
                'title'          => $movie[3],
                'original_title' => $movie[4],
                'budget'         => $movie[5],
                'homepage'      => $movie[6],
                'description'    => $movie[7],
                'language'       => $movie[8],
                'tag_line'       => $movie[9],
                'release_day'    => $movie[10],
                'runtime'        => $movie[11],
                'image_url'      => $movie[12],
                'in_cinema'      => $movie[13]
            ]);
        }
    }
}
