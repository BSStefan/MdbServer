<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DirectorSeeder extends Seeder
{
    //TODO SLIKE IMENA DOWNLOAD problem sa jednim datumom rodjenja problem sa slikama
    private $directors = [
            [
                7467,
                'David Fincher',
                'Denver, Colorado, USA',
                'David Andrew Leo Fincher (born August 28, 1962) is an American film director and music video director. Known for his dark and stylish thrillers, such as Seven (1995), The Game (1997), Fight Club (1999), Panic Room (2002), and Zodiac (2007), Fincher received Academy Award nominations for Best Director for his 2008 film The Curious Case of Benjamin Button and his 2010 film The Social Network, which also won him the Golden Globe and the BAFTA for best director.',
                '1962-08-28',
                null,
                'male',
                'images/directors/bb92229a5f73cddebae7d29c358f3628.jpg'
            ],
            [
                64211,
                'Franck Khalfoun',
                null,
                '',
                '1968-03-09',
                null,
                'male',
                'images/directors/f3b3f87c41594507e49c94100c7908d1.jpg'
            ],
            [
                1302082,
                'David F. Sandberg',
                'Jönköping, Jönköpings län, Sweden',
                'David F. Sandberg (b. December 23, 1985) is a Swedish filmmaker. He is best known for his collective no-budget horror short films under the online pseudonym "ponysmasher" and for his 2016 directorial debut Lights Out, based on his 2013 acclaimed horror short of the same name.',
                '1985-12-23',
                null,
                'male',
                'images/directors/1d22d1c0bdec2ec539f8ee94cc53c57b.jpg'
            ],
            [
                40684,
                'David Leitch',
                null,
                'David Leitch is actor, stuntman, writer, producer, stunt coordinator and film director. He has had roles in Confessions of an Action Star, Tron: Legacy, and The Matrix Trilogy.',
                null,
                null,
                'male',
                'images/directors/944ad8be86353fb8c5f0e71fb6a9985b.jpg',
            ],
            [
                1443386,
                'Brian Fee',
                null,
                '',
                null,
                null,
                'male',
                '590510c1931c13dc6c6cd8a52a9bf7cf.jpg'
            ],
            [
                6884,
                'Patty Jenkins',
                'Victorville - California - USA',
                'Patricia Lea "Patty" Jenkins is an American film director and screenwriter. She is best known for directing Monster (2003).',
                '1971-07-24',
                null,
                'female',
                'images/directors/1717833d3c2c5a2acf670d0264603e43.jpg'
            ],
            [
                71600,
                'Seth Gordon',
                'Evanston, Illinois',
                'Seth Gordon has produced and edited the critically lauded films NEW YORK DOLL and CRY WOLF, which grossed $10mat the US box office, and was cinematographer on the Oscar-nominated SHUT UP AND SING.  Most recently, Seth directed the criticallyacclaimed THE KING OF KONG: A FISTFULOF QUARTERS. The film was noted on a host of critics; andpublicationsʼ Best of the Year lists. He is currently set to directHORRIBLE BOSSES starring Jennifer Aniston, Jason Bateman and Kevin Spacey.   Seth is an honors graduate of Yale University, winnerof an Oxford University writing fellowship and alumni of Harvard’s GraduateSchool of Design.  He has also produceddocumentaries for PBS, the Gates Foundation and the UN.',
                '1974-07-20',
                null,
                'male',
                'images/directors/36a0f193d8a14409671abec43995c38a.jpg',
            ],
            [
                525,
                'Christopher Nolan',
                'London, England, UK',
                'Christopher Jonathan James Nolan (born July 30, 1970) is a British/American film director, screenwriter and producer. He is known for writing and directing such critically acclaimed films as Memento (2000), Insomnia (2002), The Prestige (2006), Inception (2010), and rebooting the Batman film franchise. Nolan is the founder of the production company Syncopy Films. He often collaborates with his wife, producer Emma Thomas, and his brother, screenwriter Jonathan Nolan, as well as cinematographer Wally Pfister, film editor Lee Smith, composers David Julyan and Hans Zimmer, special effects coordinator Chris Corbould, and actors Christian Bale and Michael Caine. Nolan\'s most critically and commercially successful film is The Dark Knight.Description above from the Wikipedia article Christopher Nolan, licensed under CC-BY-SA, full list of contributors on Wikipedia',
                '1970-07-30',
                null,
                'male',
                'images/directors/8524ffbc2c070295dba7c7f3ab6472e9.jpg'
            ],
            [
                89112,
                'Anthony Leondis',
                'New York City, New York, USA',
                '',
                '1972-03-24',
                null,
                'male',
                'images/directors/eb81cfd81bcda2845888309df6773da2.jpg'
            ],
            [
                8023,
                'Kyle Balda',
                'USA',
                'Kyle Balda is an American animator and film director, best known for co-directing 2012 animated film The Lorax with Chris Renaud and 2015\'s Minions with Pierre Coffin. He has also worked as animator on several films including Jumanji, Toy Story 2, and Despicable Me. He has worked for Pixar for years and now he is working for Illumination Entertainment.',
                null,
                null,
                'female',
                'images/directors/4844c90d21ba633cea8a0b6c60783b34.jpg'
            ],
            [
                1480620,
                'Lucia Aniello',
                null,
                '',
                null,
                null,
                'female',
                'images/directors/5544bf64e061846f0f2930d679753e1d.jpg'
            ],
            [
                1449583,
                'Alethea Jones',
                null,
                '',
                null,
                null,
                'female',
                'images/directors/2bcde5092cdbe5311cae0f81da9938c9.jpg'
            ],
            [
                58064,
                'Mark Lamprell',
                null,
                '',
                null,
                null,
                'male',
                'images/directors/6c9e1ec72b081c1b8e255e78d9a6c5df.jpg'
            ],
            [
                74752,
                'Nikolaj Arcel',
                'Copenhagen, Denmark',
                'Nikolaj Arcel is a Danish screenwriter and film director.Nikolaj graduated from The Danish Film School and has had breakthrough success in the United States as a writer with screenplays such as The Girl with the Dragon Tattoo and Island of Lost Souls. The Island of Lost Souls was actually written and directed by Nikolaj.  He has also directed successful feature films such as Truth about Men and A Royal Affair.',
                '1972-08-25',
                null,
                'male',
                ''
            ],
            [
                1428168,
                'Noémie Saglio',
                null,
                '',
                null,
                null,
                'female',
                'images/directors/5469290946c41f6a8cc4ff43821487ec.jpg'
            ],
            [
                20307,
                'Joachim Rønning',
                'Norway',
                'From Wikipedia, the free encyclopedia.Joachim Rønning is a Norwegian film director who usually works in a team with Espen Sandberg - both natives of Sandefjord, Norway. As a directing team, they go under the name of Roenberg (their last names put together). They co-own one of Scandinavia\'s largest production companies for commercials called Motion Blur. In 2006 their feature film debut, Bandidas, starring Penelope Cruz and Salma Hayek, was released worldwide through EuropaCorp and Twentieth Century Fox. The movie was written and produced by legendary French filmmaker Luc Besson. Joachim Roenning has two daughters with his wife Kristin. They live in Oslo, Norway.Description above from the Wikipedia article Joachim Rønning, licensed under CC-BY-SA, full list of contributors on Wikipedia.',
                '1972-05-30',
                null,
                'male',
                'images/directors/7b16c7297bdf235c137f8ed94e26fd50.jpg'
            ],
            [
                32278,
                'Matt Reeves',
                '',
                'Matthew George "Matt" Reeves (born April 27, 1966) is an American film writer, director and producer.Description above from the Wikipedia article Matt Reeves, licensed under CC-BY-SA, full list of contributors on Wikipedia.',
                '1966-04-27',
                null,
                'male',
                'images/directors/12e8bd456cfd63af92e9675548103723.jpg'
            ],
            [
                1293994,
                'Jon Watts',
                'Fountain, Colorado, United States',
                'Jon Watts is an American film director, producer and screenwriter. He directed the films Clown and Cop Car and episodes of the Onion News Network.Watts is directing and co-wrote the upcoming Marvel Studios and Sony Pictures produced Spider-Man: Homecoming, slated for a July 7, 2017 release.Watts was born and raised in Fountain, Colorado. He studied film at New York University.Before directing movies, Watts directed commercials for production company Park Pictures. When trying to get the job directing Spider-Man: Homecoming, Watts got a tattoo of Spider-Man on his chest to make himself "stand out in the field".',
                '1981-06-28',
                null,
                'male',
                'images/directors/7fe9ce75d3a5a3f400f21cb6d98c22bc.jpg'
            ],
            [
                1098477,
                'Callan Brunker',
                null,
                '',
                null,
                null,
                'male',
                'images/directors/ca2d571732abea0a2f0cc714a7429c7b.jpg'
            ],
            [
                59,
                'Luc Besson',
                'Paris, France',
                'Luc Besson is a French film director, writer, and producer known for making highly visual thriller and action films.  Besson has been nominated for, and won, numerous awards and honors from the foreign press, and is often credited as inventing the so-called "Cinema du look" movement in French film.  Born in Paris, Besson spent his childhood traveling Europe with his parents and developing an enthusiasm for ocean diving, before an accident would push him toward the world of cinema.  After taking odd jobs in the Parisian film scene of the time, Besson began writing stories which would eventually evolve into some of his greatest film successes: The Fifth Element and Le Grand Bleu.  In 1980 he founded his own production company, Les Films du Loup later Les Films du Dauphin,and later still EuropaCorp film company with his longtime collaborator, Pierre-Ange Le Poga. Besson\'s work stretches over twenty-six years and encompasses at least fifty-films.  .',
                '1959-03-18',
                null,
                'male',
                'images/directors/c84ada6e2a5a1b9a4c03bb3250fada96.jpg'
            ],
            [
                11090,
                'Edgar Wright',
                'Poole, Dorset, England, UK',
                'Edgar Howard Wright (born 18 April 1974) is an English film and television director and writer. He is most famous for his work with Simon Pegg and Nick Frost on the films Shaun of the Dead and Hot Fuzz, the TV series Spaced, and for directing the film Scott Pilgrim vs. the World. He is also an executive producer on his friend Joe Cornish\'s Attack the Block and co-writer of the upcoming Steven Spielberg film The Adventures of Tintin: Secret of the Unicorn with Cornish and Doctor Who and Sherlock writer, Steven Moffat.Description above from the Wikipedia article Edgar Wright, licensed under CC-BY-SA, full list of contributors on Wikipedia.',
                '1974-04-18',
                null,
                'male',
                'mdma'
            ]
        ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->directors as $director)
        {
            DB::table('directors')->insert([
                'tmdb_id' => $director[0],
                'name' => $director[1],
                'place_of_birth' => $director[2],
                'biography' => $director[3],
                'birthday' => $director[4],
                'dead_day' => $director[5],
                'gender' => $director[6],
                'image_url' => $director[7],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
}
