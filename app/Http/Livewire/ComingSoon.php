<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class ComingSoon extends Component
{
    public $comingSoon = [];

    public function loadComingSoon()
    {
        $current = Carbon::now()->timestamp;
        $comingSoonGamesUnformatted = \Http::withHeaders(config('services.igdb'))
                    ->send('POST','https://api.igdb.com/v4/games/', [
                    'body' => "fields name, cover.url, first_release_date,total_rating_count, platforms.abbreviation, rating, slug,rating_count,summary; 
                                where platforms = (48,49,130,6,165,167,169) & 
                                first_release_date >={$current}
                                & cover != null;
                                sort total_rating_count desc;
                                limit 4;"
                ])->json();
        
        $this->comingSoon = $this->formatForView($comingSoonGamesUnformatted);
    }

    public function render()
    {
        return view('livewire.coming-soon');
    }

    private function formatForView($games)
    {
        return collect($games)->map(function($game){
            return collect($game)->merge([
                "coverImageUrl"=>\Str::replaceFirst('thumb','cover_big',$game['cover']['url']),
                "first_release_date"=>date('M d,Y',$game['first_release_date']),
               
            ]);
        })->toArray();
    }
}
