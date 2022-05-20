@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h2 class="text-blue-500 uppercase tracking-wide font-semibold">
            Popular Games
        </h2>

        <!-- popular game start -->
        <livewire:popular-games />
        <!-- end of papular games-->

        <div class="flex flex-col lg:flex-row my-10">
            <div class="recently-reviewd w-full lg:w-3/4 mr-0 lg:mr-32">
                <!-- begin recently reviewed-->
                <h2 class="text-blue-500 uppercase tracking-wide font-semibold">Recently Reviewed</h2>

                <livewire:recently-reviewed />
                <!-- end recently reviewed-->
            </div>
            <div class="recently-anticipated lg:w-1/4 mt-12 lg:mt-0">
                   <!-- begin most anticipated-->
                <h2 class="text-blue-500 uppercase tracking-wide font-semibold">Most Anticipated</h2>
             
               <livewire:most-anticipated />
                <!-- end most anticipated-->

                <!-- begin coming soon-->
                <h2 class="text-blue-500 uppercase tracking-wide font-semibold mt-8">Coming Soon</h2>
           
               <livewire:coming-soon />
                <!-- end comming soon-->
            </div>
        </div>
    </div>


@endsection