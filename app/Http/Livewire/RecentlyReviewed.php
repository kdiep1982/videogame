<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class RecentlyReviewed extends Component
{
    public $recentlyReviewed = [];

    public function loadRecentlyReviewed()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;

        $recentlyReviewedUnformatted = \Http::withHeaders(config('services.igdb'))
                ->send('POST','https://api.igdb.com/v4/games/', [
                'body' => "fields name, cover.url, first_release_date,total_rating_count, platforms.abbreviation, rating, slug,rating_count,summary; 
                            where platforms = (48,49,130,6,165,167,169) & 
                            first_release_date >={$before} & first_release_date < {$current}
                            & total_rating_count > 5;
                            sort total_rating_count desc;
                            limit 3;"
        ])->json();

        $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);
        collect($this->recentlyReviewed)->filter(function($game){
            return $game['rating'];
        })->each(function ($game){
            $this->emit('reviewGameWithRatingAdded',[
                'slug'=>'review_'.$game['slug'],
                'rating'=>$game['rating']/100
            ]);
        });
    }

    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    private function formatForView($games)
    {
        return collect($games)->map(function ($game){
            return collect($game)->merge([
                "coverImageUrl"=>\Str::replaceFirst('thumb','cover_big',$game['cover']['url']),
                "rating"=>isset($game['rating'])?round($game['rating']):null,
                "platforms"=>collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }

}
