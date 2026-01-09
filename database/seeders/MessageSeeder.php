<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Početno vreme: 1 sat unazad
        $timestamp = Carbon::now()->subHour();

        // $messages = [
        //     'U2FsdGVkX19iHIp/3U3QsDkh/aL2SVXaf7xYhSpMSgU=',
        //     'U2FsdGVkX1/ceey+nurZLHdJC9/Kv0C92uGGeA8aPlU=',
        //     'U2FsdGVkX18DUlP60W4Ya0m8ZptstIJMsKU17iYyNa4=',
        //     'U2FsdGVkX1/EvKlNXJpin2APhiazST5AxgBHpSFGlvw=',
        //     'U2FsdGVkX1/TiGro9g+0gJ8LJTzW3qU95Xi/yNDqo935GMb4RCyZPmoRDvFq8WlS',
        //     'U2FsdGVkX1/zyzHgVk786fXz2oh3jUn+nyvIgCqPCpY=',
        //     'U2FsdGVkX1/fN0f4RhL7IHs/m6Gow8BmY21Wev1Klqk=',
        //     'U2FsdGVkX1+M7KpAWKITE1oothclg8Jvh5qnoQ4AWLw=',
        //     'U2FsdGVkX18FPplL2vLRsbttGO88xPZLelRe8leyD08=',
        //     'U2FsdGVkX1/S2wRAQD4GsqcmY3qZUhN+2B6j9XY96wM=',
        //     'U2FsdGVkX1+eU3b1P7RzaveA/zWlqDIAHJN9/YREIus=',
        //     'U2FsdGVkX1/PALPTHXmFkkhJhFh+L8BJlwxNkaNp2w8=',
        //     'U2FsdGVkX1/HkNHWjyVjvfStD1Kgyvv6k34taqnR888=',
        //     'U2FsdGVkX199ayOJPjzxdSpC9e0+Y3K95VJRmdMqXeCee3Y9ytta+G66OrytfHNz',
        //     'U2FsdGVkX18ouRk5J0alF5G3EQNk25dSEX/17DtmsPU=',
        //     'U2FsdGVkX1/5vtfqhvznFU8WG/qA1EEydI/zgtI4uRQ=',
        //     'U2FsdGVkX19mSjCGsFWO86MvjKX36V7sVVWTBXngHilDNAPn4bHuxI49mtZ8wS5J',
        //     'U2FsdGVkX1/ZODl8dAGkRm7Z4VWB/kKbVsw56xAeJs4=',
        //     'U2FsdGVkX1+IpZLy5K1JCntusnofQOUuDptF/wY0McQ=',
        //     'U2FsdGVkX1/dpxKRjPIzROg0MAaifQwRlUZq75SmK5c=',
        //     'U2FsdGVkX1+caDUvlgRQ05RMRZf6aeS5SK4e7hxF59Q=',
        //     'U2FsdGVkX1+THLy9nCBBSKRz7+gByhbSnmO6HywTDr4=',
        //     'U2FsdGVkX1/UAEGqqUs+n5jcqFqtMriMZy1Uc32jusPwHjflzd8jXN0RhWfpkf0i',
        //     'U2FsdGVkX190xghUh3b+StX4/BSLFY8j82w68t9fh90=',
        //     'U2FsdGVkX1/oXdntbmvxMOkW/SJLdfLeAfthLLJQGYs=',
        //     'U2FsdGVkX1/1NC9gSduRh87/m0oNbQvnJqb/UR0UvtU=',
        //     'U2FsdGVkX1/H+CJ9V1EnrrCzlQ2jeHMtrPJNr3mxg5E=',
        //     'U2FsdGVkX1+ut8cBqADKmSDPbJ22M4BXZY3irZ3uZ50=',
        //     'U2FsdGVkX19d1IjjvaCEJIocWR1XVGT89Kec++lhZlE=',
        // ];


        $messages = [
            'Ćao!',
            'Hej, kako ide danas?',
            'Sve okej za sada. Malo posla, malo kafe, klasična priča.',
            'Razumem te potpuno. Ja sam već na trećoj kafi, a još je jutro.',
            'Radim na jednom projektu i pokušavam da sredim layout za chat. Nije komplikovano, ali sitnice umeju da nerviraju. Posebno kada želiš da izgleda kao pravi production app.',
            'Da, chat UI zna da biti zeznut.',
            'Najviše me nervira kad poruke zauzimaju celu širinu ekrana. To odmah izgleda amaterski i nepregledno.',
            'Slažem se. Bubble mora da prati tekst, a ne obrnuto.',
            'Upravo to. Plus dark/light tema mora da radi bez ikakvog cimanja. Ako tu krene hackovanje, kasnije sve eksplodira.',
            'Amin na to.',
            'Zato sada testiram razne dužine poruka. Kratke, srednje i baš dugačke, da vidim kako se ponašaju. Bolje sada nego kasnije.',
            'Pametno razmišljanje.',
            'Posebno na desktopu, jer ljudi često zaborave da chat nije samo mobilna stvar. Na velikom ekranu se greške još više vide.',
            'Da, desktop chat mora da diše.',
            'Kad ovo završim, mogu mirno da pređem na auto-scroll i optimizaciju. Jedan problem manje u glavi. Pokušavam sve da sredim sa css a ako ne moze onda javascript... Hoću sigurno. Bolje je pitati nego kasnije refaktorisati pola aplikacije. Jedan problem manje u glavi. Pokušavam sve da sredim sa css a ako ne moze onda javascript. Jedan problem manje u glavi. Pokušavam sve da sredim sa css a ako ne moze onda javascript',
            'Javi ako zapne negde.',
            'Hoću sigurno. Bolje je pitati nego kasnije refaktorisati pola aplikacije.',
            'Istina.',
            'Ajde da ovo izguraš do kraja pa da vidiš kako lepo legne kad je sve čisto. Dobro složen chat je pola UX-a.',
            'Biće to dobro.',
            'Usput, kako rešavaš max širinu bubble-a? Procenat ili fiksna vrednost?',
            'Kombinacija. Na desktopu max-width u ch jedinicama, na mobilnom malo šire.',
            'To ima smisla. Ch je često zapostavljen, a baš lepo radi za tekst.',
            'Da, posebno kad hoćeš konzistentnu čitljivost.',
            'Razmišljaš li o različitom poravnanju za sent i received poruke?',
            'Naravno. To odmah daje vizuelnu hijerarhiju bez dodatnih elemenata.',
            'Plus avatar samo gde ima smisla, ne svuda.',
            'Tačno. Ako je jedan-na-jedan chat, avatar na svakoj poruci je čisti noise.',
            'Kako stojiš sa timestampovima?',
            'Diskretno ispod poruke, manji font, low contrast.',
            'Super. Najgore je kad timestamp preuzme pažnju.',
            'Slažem se. Fokus mora da ostane na sadržaju.',
            'Da li planiraš message grouping?',
            'Da, iste poruke u nizu bez razmaka i sa manjim radiusom.',
            'To baš daje osećaj ozbiljnog proizvoda.',
            'Upravo to jurim. Da izgleda jednostavno, ali promišljeno.',
            'A performanse? Virtualizacija ili još ne?',
            'Za sada ne, ali struktura je spremna ako zatreba.',
            'Pametno. Ne optimizuješ prerano, ali nisi ni slep.',
            'Auto-scroll zna da bude klizav teren.',
            'Da, pogotovo kad korisnik scrolluje nagore pa stigne nova poruka.',
            'Tu obavezno moraš da poštuješ user intent.',
            'Naravno. Scroll samo ako je već na dnu.',
            'Kad sve to spojiš, chat deluje lako, a ispod haos.',
            'Klasična priča.',
            'Ali baš zato je zanimljivo raditi na ovim detaljima.',
            'Slažem se. Ovo su stvari koje prave razliku.',
            'Kad završiš, imaćeš dobar template za buduće projekte.',
            'To mi je i cilj.',
            'Onda si na pravom putu.'
        ];

        $messages2 = [
            'Ej, jesi danas trenirao?',
            'Jesam, ali jedva. Noge su mi još uvek mrtve od prošlog treninga.',
            'Znači dobar trening 😄',
            'Da, ali sad razmišljam da malo smanjim volumen. Ne treba mi overkill svaki put.',
            'Pametno. Ljudi često misle da više uvek znači bolje, a u praksi se samo zakucaju u zid.',
            'Upravo to. Plus san mi je bio loš poslednjih dana, pa se i to oseća.',
            'San je pola oporavka, minimum.',
            'Znam, ali kad uđeš u ritam posla, teško je isključiti mozak uveče.',
            'Probaj bar da ugasiš ekran sat vremena ranije. Meni je to dosta pomoglo.',
            'Moraću. Ne može se ići protiv tela zauvek.',
            'Tačno. Bolje sporije i dugoročno nego brzo pa pauza od mesec dana.',
            'Ishranu si sredio ili i tu improvizuješ?',
            'Tu sam dosta disciplinovan, iskreno. To mi je najmanji problem.',
            'Onda si već ispred većine.',
            'Videćemo kako ide sledeće nedelje. Ako krene bolje, znam da sam na dobrom putu.',
            'Držim palčeve.',
            'Hvala, javljam update.',
            'Važi.',
            'Ajde, čujemo se kasnije.',
            'Dogovoreno.',
            'Usput, koji ti je fokus sada? Snaga, masa ili održavanje?',
            'Trenutno bih rekao održavanje, uz malo snage.',
            'To je dobar phase, posebno kad imaš puno obaveza.',
            'Da, ne jurim PR-ove po svaku cenu.',
            'Koliko puta nedeljno treniraš?',
            'Četiri puta. Pet mi je previše u ovom periodu.',
            'Četiri je sasvim korektno ako je pametno složeno.',
            'Da, gledam da imam bar jedan full rest dan.',
            'To mnogi preskaču, a onda se pitaju zašto stagniraju.',
            'Tačno. I glava se odmori, ne samo telo.',
            'Radiš li mobilnost ili istezanje?',
            'Minimalno, ali znam da bih mogao više.',
            'Uvek može bolje, ali i minimum je bolji nego ništa.',
            'Istina. Nekad je najbitnije samo održati kontinuitet.',
            'Kako stojiš sa kardijom?',
            'Uglavnom šetnje i povremeno bicikl.',
            'To je sasvim dovoljno za zdravlje.',
            'Da, ne želim da mi trening postane dodatni stres.',
            'Pametan pristup.',
            'Naučio sam na teži način.',
            'Bitno je da si naučio.',
            'Sad samo treba slušati telo.',
            'I biti strpljiv.',
            'Najteži deo.',
            'Ali dugoročno se uvek isplati.',
            'Slažem se.',
            'Ajde, javi kako ide sledeći trening.',
            'Hoću, sigurno.',
            'Čujemo se.',
            'Važi.'
        ];


    $messages3 = [
        'Razmišljam da pobegnem negde za vikend.',
        'Negde blizu ili baš daleko?',
        'Nešto kratko, 2–3 dana, da promenim malo okruženje.',
        'Planina ili grad?',
        'Iskreno, više me vuče priroda. Treba mi mir.',
        'Skroz te razumem. Grad ume da iscrpi, čak i kad nemaš obaveze.',
        'Baš to. Hoću da se probudim bez alarma i bez buke.',
        'I da kafa traje duže od pet minuta.',
        'Tačno tako.',
        'Jesi gledao neke konkretne lokacije?',
        'Jesam par opcija, ali još nisam presekao.',
        'Nemoj previše da analiziraš. Često se najbolji izleti dese spontano.',
        'Znam, ali opet volim da imam bar neki okvir.',
        'Fair enough.',
        'Bitno je samo da se isključiš malo.',
        'Da, reset glave pre nove radne nedelje.',
        'Ako odeš, slikaj nešto lepo.',
        'Hoću, obavezno.',
        'Ajde, javi šta si odlučio.',
        'Javljam čim presečem.',
        'Gledaš li više planinske kuće ili neki mali smeštaj?',
        'Više nešto skromno, ali da ima pogled i tišinu.',
        'To je često i najbolja varijanta.',
        'Da, ne treba mi luksuz, samo mir.',
        'Ideš sam ili u društvu?',
        'Verovatno sam. Treba mi malo vremena bez priče.',
        'I to je skroz okej. Retko ko to sebi priušti.',
        'Istina. Uvek smo nekako u gužvi.',
        'Hoćeš li poneti laptop ili totalni off?',
        'Razmišljam da ga ostavim kući.',
        'Pametna odluka, ako možeš.',
        'Bar da probam. Ako ne ide, uvek mogu da se vratim.',
        'Planiraš šetnje ili samo odmor?',
        'Malo hodanja, ali bez ikakvog plana.',
        'Najbolji način da se stvarno odmoriš.',
        'I da se malo razbistri glava.',
        'To mi trenutno najviše treba.',
        'Hoćeš li čitati nešto?',
        'Da, imam knjigu koju već mesecima nosim sa sobom.',
        'Idealna prilika.',
        'Takvi vikendi često ostanu duže u sećanju.',
        'Da, iako traju kratko.',
        'Bitno je kako se vratiš.',
        'Ako se vratiš mirniji, vredelo je.',
        'Slažem se.',
        'Ajde, uživaj gde god da odeš.',
        'Hvala.',  
        'Čujemo se kad se vratiš.',
        'Važi.'
    ];

        // Ubacujemo po dve poruke po iteraciji
        // Konverzacija 1
        for ($i = 0; $i < count($messages); $i += 2) {
            // user 1 → user 2
            DB::table('messages')->insert([
                'conversation_id' => 1,
                'sender_id' => 1,
                'message' => $messages[$i],
                'created_at' => $timestamp->copy(),
                'updated_at' => $timestamp->copy(),
            ]);
            $timestamp->addMinutes(1);

            // user 2 → user 1
            if (isset($messages[$i + 1])) {
                DB::table('messages')->insert([
                    'conversation_id' => 1,
                    'sender_id' => 2,
                    'message' => $messages[$i + 1],
                    'created_at' => $timestamp->copy(),
                    'updated_at' => $timestamp->copy(),
                ]);
                $timestamp->addMinutes(1);
            }
        }

        // Konverzacija 2
        for ($i = 0; $i < count($messages2); $i += 2) {
            // user 1 → user 2
            DB::table('messages')->insert([
                'conversation_id' => 2,
                'sender_id' => 1,
                'message' => $messages2[$i],
                'created_at' => $timestamp->copy(),
                'updated_at' => $timestamp->copy(),
            ]);
            $timestamp->addMinutes(1);

            // user 2 → user 1
            if (isset($messages2[$i + 1])) {
                DB::table('messages')->insert([
                    'conversation_id' => 2,
                    'sender_id' => 2,
                    'message' => $messages2[$i + 1],
                    'created_at' => $timestamp->copy(),
                    'updated_at' => $timestamp->copy(),
                ]);
                $timestamp->addMinutes(1);
            }
        }

        // Konverzacija 3 (3. korisnik povremeno)
        for ($i = 0; $i < count($messages3); $i += 3) {
            // user 1 → user 2
            DB::table('messages')->insert([
                'conversation_id' => 3,
                'sender_id' => 1,
                'message' => $messages3[$i],
                'created_at' => $timestamp->copy(),
                'updated_at' => $timestamp->copy(),
            ]);
            $timestamp->addMinutes(1);

            // user 2 → user 1
            if (isset($messages3[$i + 1])) {
                DB::table('messages')->insert([
                    'conversation_id' => 3,
                    'sender_id' => 2,
                    'message' => $messages3[$i + 1],
                    'created_at' => $timestamp->copy(),
                    'updated_at' => $timestamp->copy(),
                ]);
                $timestamp->addMinutes(1);
            }

            // user 3 → user 1 (ako postoji sledeća poruka)
            if (isset($messages3[$i + 2])) {
                DB::table('messages')->insert([
                    'conversation_id' => 3,
                    'sender_id' => 1001,
                    'message' => $messages3[$i + 2],
                    'created_at' => $timestamp->copy(),
                    'updated_at' => $timestamp->copy(),
                ]);
                $timestamp->addMinutes(1);
            }
        }

    }
}
