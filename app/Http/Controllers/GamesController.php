<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GamesController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }

    public function show($slug)
    {
        $game = \Http::withHeaders(config('services.igdb'))
                    ->send('POST','https://api.igdb.com/v4/games/', [
                        'body' => "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating,
                                    slug, involved_companies.company.name, genres.name, aggregated_rating, summary, websites.*, videos.*,
                                    screenshots.*, similar_games.cover.url, similar_games.name, similar_games.rating, similar_games.platforms.abbreviation,
                                    similar_games.slug;
                                where slug=\"{$slug}\";"
                    ])->json();
        abort_if(!$game,404);
           
        return view('show', [
            'game' => $this->formatGameForView($game[0]),
        ]);
    }

    private function formatGameForView($game)
    {
        return collect($game)->merge([
            'coverImageUrl' => \Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
            'genres' => collect($game['genres'])->pluck('name')->implode(', '),
            'involvedCompanies' => $game['involved_companies'][0]['company']['name'],
            'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            'memberRating' => array_key_exists('rating', $game) ? round($game['rating']) : '0',
            'criticRating' => array_key_exists('aggregated_rating', $game) ? round($game['aggregated_rating']) : '0',
            'trailer' => array_key_exists('videos',$game)?'https://youtube.com/embed/'.$game['videos'][0]['video_id']:null,
            'screenshots' => collect($game['screenshots'])->map(function ($screenshot) {
                return [
                    'big' => \Str::replaceFirst('thumb', 'screenshot_big', $screenshot['url']),
                    'huge' => \Str::replaceFirst('thumb', 'screenshot_huge', $screenshot['url']),
                ];
            })->take(9),
            'similarGames' => collect($game['similar_games'])->map(function ($game) {
                return collect($game)->merge([
                    'coverImageUrl' => array_key_exists('cover', $game)
                        ? \Str::replaceFirst('thumb', 'cover_big', $game['cover']['url'])
                        : 'https://via.placeholder.com/264x352',
                    'rating' => isset($game['rating']) ? round($game['rating']) : null,
                    'platforms' => array_key_exists('platforms', $game)
                        ? collect($game['platforms'])->pluck('abbreviation')->implode(', ')
                        : null,
                ]);
            })->take(6),
            'social' => [
                'website' => collect($game['websites'])->first(),
                'facebook' => collect($game['websites'])->filter(function ($website) {
                    return \Str::contains($website['url'], 'facebook');
                })->first(),
                'twitter' => collect($game['websites'])->filter(function ($website) {
                    return \Str::contains($website['url'], 'twitter');
                })->first(),
                'instagram' => collect($game['websites'])->filter(function ($website) {
                    return \Str::contains($website['url'], 'instagram');
                })->first(),
            ]
        ]);
    }
}

