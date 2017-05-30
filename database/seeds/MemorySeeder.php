<?php

use Illuminate\Database\Seeder;
use App\Post;

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
        

        for ($i=1; $i < 13; $i++) {
            $new_memory = new Post();
            $new_memory->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_memory->child_id = 2;
            $new_memory->story = $stories[$i-1];
            $new_memory->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/original/img' . $i . '.jpg');
            $new_memory->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_memory->is_memory = true;
            $new_memory->save();
            $new_memory->addMedia(storage_path() . '/quote-seeder-img/original/img' . $i . '.jpg')->preservingOriginal()->toMediaLibrary('baked');
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
