<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class MostAnticipated extends Component
{
    public $mostAnticipated = [];

    public function loadMostAnticipated()
    {
        $current = Carbon::now()->timestamp;
        $afterFourMonths = Carbon::now()->addMonths(4)->timestamp;
        
        $mostAnticipatedGamesUnformatted = \Http::withHeaders(config('services.igdb'))
        ->send('POST','https://api.igdb.com/v4/games/', [
        'body' => "fields name, cover.url, first_release_date,total_rating_count, platforms.abbreviation, rating, slug,rating_count,summary; 
                    where platforms = (48,49,130,6,165,167,169) & 
                    (first_release_date >={$current} & first_release_date < {$afterFourMonths})
                    & cover != null;
                    sort total_rating_count desc;
                    limit 4;"
                ])->json();

        $this->mostAnticipated = $this->formatForView($mostAnticipatedGamesUnformatted);

    }

    public function render()
    {
        return view('livewire.most-anticipated');
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
