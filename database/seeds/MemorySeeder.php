<?php

use Illuminate\Database\Seeder;
use App\Post;
use Carbon\Carbon;

class MemorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $stories = [
        //     'Vandaag heeft ons miranda voor de eerste keer een wandeling gemaakt. Ze was nogal moe als ze thuis kwam, amai. Ni gewoen...',
        //     'Onzen Timmy heeft vandaag voor de eerste keer met de auto gereden. Er zijn 3 paaltjes gesneuveld en den bumper ligt nog erges op den E17. Na drie uur vluchten zijn we de politie eindelijk kwijtgeraakt.',
        //     'Twas koud vandaag amai! Ons Frida zegt ineens, "kga ké buite zien". Steekt ze daar ineens een sigaretjn op seg, mokt da mee!',
        //     'Het contract is eindelijk getekend. Ik ben zo trots op onze kleine victor. Hij kocht vandaag zijn eerste huis in Oostenrijk.',
        //     'We kochten vandaag een aapje voor de kleine Timmy. Spijtig genoeg heeft hij al drie kakskes gelegd in de living... Niets aan te doen.',
        //     'De paus kwam vandaag op schoolbezoek bij Sofie. Hij keek een beetje raar naar Tommy, een klasgenootje. Ik vermoed dat de man nogal schrok van zijn driekwartsbroek en roze crocs. Homoseksualiteit wordt dus nog steeds niet aanvaard door de kerk.',
        //     'Joris kocht vandaag een nieuwe grasmaaier. "Tes zo ene da he kunt goan opzittn" riep hij nog net voor hij eraf viel en zijn arm afgekapt werd. De ziekenhuiskosten kan em zelf betale...',
        //     'Vandaag was een drukke dag op de luchthaven voor Steven. Hij had drie uur vertraging op zijn vlucht naar Milaan. Hij liet zijn welverdiende vakantie echter niet in het water vallen en kocht een ander vliegtuig over voor 3 miljoen miljard. Vijf minuten later kon hij zonder enige zorgen vertrekken.',
        //     'Barry pleegde vandaag acht overvallen. Hij werd gefilmd door een bewakingscamera tijdens de laatste overval. Hij komt volgende week voor de rechtbank. Alweer een ervaring rijker.',
        //     'Anne-marie heeft 3 weken geleden voor het eerst seks gehad met de buurjongen. Ik had het haar nog zo gezegd, maarja, nooit luisteren eh... Ze heeft besloten om het kindje te houden.',
        //     'De kleine Druis kocht gisteren een kip bij boer Jeur. Samen met papa leerde hij hoe hij een kip moet slachten en pluimen. Vanavond kip met appelmoes!',
        //     'Koen kocht een Mustang op 2dehands.be. Na grondige inspectie bleek hij int zak gezet te zijn. Het bleek te gaan om een citroën berlingo. Volgende keer beter opletten als je iets op het internet koop kleine Koen!',
        // ];

        $stories = [
            'It was a very sunny day today so we decided to go to the park. Little Amy had so much fun playing around with daddy. After an hour of running around and exploring she came to sit next to me. She had something with her and handed it to me. It was a tiny clover. She said she picked it for me because she loves me so much. I could help but tear up. I will never, ever forget this beautiful moment!',
            'We went to Spain for our summer holiday this year. Because Amy had never seen the sea, we decided to tell her we were going to visit the biggest swimming pool in the world. She didn\'t believe us so when we finally arrived she was shocked. She couldn\'t believe what she was seeing. At first she was scared to enter the water, but in the end she had an amazing day.',
            'The sunset. It\'s so magical. Amy has seen her first one yesterday. For a few minutes the sky had a somewhat pink color, which she obviously liked a lot. She said she wants the sky to always look like that. She enjoyed the evening so much. I don\'t think she will ever forget her first sunset. ps. This morning she was rather disappointed when the sky was blue again.',
            'Tommy made a drawing at school today. When he came home he sat down and explained me what he drew. When he grows up, he wants to be a superhero. He also said he sometimes dreams about being like spiderman or superman. I think we will buy him a superhero outfit for his birthday. He\'ll love it!',
            'Easter egg hunting today was amazing. I think Tommy was searching for about 2 hours. We kept putting the eggs he already found, back in other spots. This way the surpises kept coming and coming. At one moment he almost saw me putting them back but I told him I was just checking if he hadn\'t missed any.'
        ];

        $counter = 0;
        foreach ($stories as $story) {
            $for_child = ($counter <= count($stories) / 2) ? 1 : 2;

            $counter++;
            $new_memory = new Post();
            $new_memory->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_memory->child_id = $for_child;
            $new_memory->story = $story;
            $new_memory->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/stories/img' . $counter . '.jpg');
            $new_memory->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_memory->is_memory = true;
            $new_memory->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $new_memory->save();
            $new_memory->addMedia(storage_path() . '/quote-seeder-img/stories/img' . $counter . '.jpg')->preservingOriginal()->toMediaLibrary('baked');
        }
    }

    private function getSmallSizeImage($image) {
        return Image::make($image)
        ->resize(5, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('data-url')
        ->encoded;
    }
}
