<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PopularGames extends Component
{
    public $popularGames = [];

    public function loadPopularGames()
    {

        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;
        
        $popularGamesUnformatted = Cache::remember('popular-games', 7, function () use ($before,$after) {
            return \Http::withHeaders(config('services.igdb'))
                    ->send('POST','https://api.igdb.com/v4/games/', [
                        'body' => "fields name, cover.url, first_release_date,total_rating_count, platforms.abbreviation, rating, slug; 
                            where platforms = (48,49,130,6) & 
                            (first_release_date >={$before} & first_release_date < {$after})
                            & cover != null;
                            sort rating desc;
                            limit 12;"
                    ])->json();
        });

        //dump($this->formatForView($popularGamesUnformatted));
        $this->popularGames = $this->formatForView($popularGamesUnformatted);
        collect($this->popularGames)->filter(function($game){
            return $game['rating'];
        })->each(function ($game){
            $this->emit('gameWithRatingAdded',[
                'slug'=>$game['slug'],
                'rating'=>$game['rating']/100
            ]);
        });
    }

    public function render()
    {
        return view('livewire.popular-games');
    }

    public function formatForView($games)
    {

        return collect($games)->map(function($game){
            return collect($game)->merge([
                "coverImageUrl"=>\Str::replaceFirst('thumb','cover_big',$game['cover']['url']),
                "rating"=>isset($game['rating'])?round($game['rating']):null,
                "platforms"=>collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }
}
