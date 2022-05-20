<div class="relative" x-data="{isVisible:true}" @click.away="isVisible=false">
    <input 
        wire:model.debounce.300ms ="search"
        type="text" 
        class="bg-gray-800 text-sm rounded-full focus:outline-none focus:shadow-outline w-64 px-3 pl-8 py-1" 
        placeholder="Search (Press '/' to focus)"
        x-ref="search"
        @keydown.window="
            if(event.keyCode == 191)
            {
                event.preventDefault();
                $refs.search.focus();
            }

        "
        @focus="isVisible=true"
        @keydown.escape.window = "isVisible=false"
        @keydown = "isVisible=true"
        @keydown.shift.tab = "isVisible = false"
        >
    <div class="absolute top-0 flex items-center h-full ml-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
    </div>

    <div wire:loading class="spinner top-0 right-0 mr-4 mt-3" style="position: absolute;"></div>

    @if(strlen($search)>=2)
        <div class="absolute z-50 bg-gray-800 text-xs rounded w-64 mt-2" x-show.transition.opacity.duration.1000="isVisible">
            @if (count($searchResults)>0)
                <ul>
                    @foreach($searchResults as $game)
                        <li class="border-b border-gray-700">
                            <a 
                                href="{{route('games.show',$game['slug'])}}" 
                                class="block hover:bg-gray-700 flex items-center transistion ease-in-out duration-150 px-3 py-3 "
                                @if($loop->last)@keydown.tab="isVisible=false" @endif
                            >
                                @if(isset($game['cover']))
                                    <img src="{{\Str::replaceFirst('thumb','cover_small',$game['cover']['url'])}}" class="w-10">
                                @else
                                    <img src="https://via.placeholder.com/264x352" class="w-10">
                                @endif
                                    <span class="ml-4">{{$game['name']}}</span>
                            </a>
                        </li>
            
                    @endforeach

                </ul>
            @else
                <div class="py-3 px-3">No results for "{{$search}}"</div>
            @endif
        </div>
        
    @endif
   
</div>
