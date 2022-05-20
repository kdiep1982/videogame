<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SearchDropdown extends Component
{
    public $search = '';
    public $searchResult = [];



    public function render()
    {
        if(strlen($this->search)>=2){
            $this->searchResults  = \Http::withHeaders(config('services.igdb'))
            ->send('POST','https://api.igdb.com/v4/games/', [
            'body' => "
                        search \"{$this->search}\";
                        fields name, slug, cover.url;
                        limit 6;
                        "
                    ])->json();
        }
        

        return view('livewire.search-dropdown');
    }
}
