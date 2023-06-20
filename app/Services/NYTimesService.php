<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;

class NYTimesService
{
    protected $endpoint = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
    protected $apiKey = '7mty3VXMw1jlgYGYwS5T45GxCVeb7AZf';
    /**
     * Delete todo by id.
     *
     * @param $id
     * @return String
     */

    /**
     * Get all todo.
     *
     * @return String
     */
    public function getNews(
        ?string $keyword,
        int $page = 1,
        ?array $categories,
        ?array $sources,
        ?string $from_date,
        ?string $to_date,
    )
    {
        $news = [];
        $result = ['news' => $news];
        $nyt_params = [            
            'sort' => 'newest',
            'api-key' => $this->apiKey,
            'page' => $page
        ];

        $keyword ? $nyt_params['q'] = $keyword:'';
        $from_date ? $nyt_params['begin_date'] = date("Ymd", strtotime($from_date)):'';
        $to_date ? $nyt_params['end_date'] = date("Ymd", strtotime($to_date)):'';
        $categories ? $nyt_params['fq'] = 'section_name.contains:('.implode(', ', $categories).')' : '';
        $sources ? $nyt_params['fq'] = 'source.contains:('.implode(', ', $sources).')' : '';
        
        $nyt_api = Http::get($this->endpoint, $nyt_params);
        
        $response = json_decode($nyt_api->getBody());
        
        if(isset($response->response->docs)){            
            // \Log::debug('NYT results: ' . $response->response->meta->hits);
            $articles = collect($response->response->docs);            

            foreach($articles as $art){
                $new_article =  [
                    'source_name' => $art->source,
                    'author' => $art->byline->original ?? 'Anonymous',
                    'title' => $art->headline ?  ($art->headline->print_headline ?? $art->headline->main):'',
                    'description' => $art->snippet,
                    'thumbnail' => $art->multimedia ? 'https://static01.nyt.com/'.$art->multimedia[0]->url : 'https://14thstreetmusic.com/wp-content/uploads/2014/04/new_york_times_logo_0.jpg',
                    'url' => $art->web_url,
                    'published_at' => date('F j, Y', strtotime($art->pub_date)),
                    'date' => $art->pub_date,
                    'content' => $art->snippet,
                    'category' => $art->section_name,
                    'author_image' => 'https://i.pinimg.com/originals/97/7a/31/977a31b32c998dda750cea2db6a7ebf7.png'
                ];
                $news[] = $new_article;
            }
            $result['news'] = $news;
            $result['articles_count'] = $articles->count();
            $result['total_results'] = $response->response->meta->hits;
            $result['max_pages'] = $response->response->meta->hits > 0 ? ceil($response->response->meta->hits / 10) : 0;            
        }

        return $result;
    }

   

}
