<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;

class NewsApiOrgService
{
    protected $endpoint = 'https://newsapi.org/v2/everything';
    protected $apiKey = '8b5b2b7078d34315bf249a665477936c';
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
        ?int $page_size = 10
    )
    {
        $news = [];
        $result = ['news' => $news];
        $news_api_endpoint = $this->endpoint;

        $newsapi_params = [
            'q' => $keyword ? $keyword : 'a', //required, should not be empty
            'sortBy' => 'publishedAt',
            'pageSize' => $page_size,
            'apiKey' => $this->apiKey,
            'page' => $page
        ];

        if($from_date) {
            $from_date_limit = now()->subMonth()->format(('Y-m-d')); //News api only allow article queries from present to last month for free users
            $from_date = date('Y-m-d', strtotime($from_date));
            if($from_date < $from_date_limit){
                $from_date = $from_date_limit;
            }
            $newsapi_params['from'] = $from_date;
        } 
        $to_date ? $newsapi_params['to'] = date('Y-m-d', strtotime($to_date)):'';

        //can't mix category and source params on newsapi.org, source:https://newsapi.org/docs/endpoints/top-headlines
        if($sources){
            $newsapi_params['sources'] = strtolower(implode(',', $sources));
        }
        else if($categories){
            $newsapi_params['category'] = strtolower(implode('&', $categories));
            $news_api_endpoint = 'https://newsapi.org/v2/top-headlines';
            //category param is only available on /top-headlines endpoint but not in /everything endpoint
            //so we have to update the endpoint in order to filter category
        }        

        $news_api = Http::get($news_api_endpoint, $newsapi_params);        
        $response = json_decode($news_api->getBody());
        
        if(isset($response->articles)){            

            $articles = collect($response->articles);
        
            foreach($articles as $art){
                $new_article =  [
                    'source_name' => $art->source->name,
                    'author' => $art->author,
                    'title' => $art->title,
                    'description' => $art->description,
                    'thumbnail' => $art->urlToImage,
                    'url' => $art->url,
                    'published_at' => date('F j, Y', strtotime($art->publishedAt)),
                    'date' => $art->publishedAt,
                    'content' => $art->content,
                    'author_image' => 'https://www.seekpng.com/png/detail/966-9665317_placeholder-image-person-jpg.png'                
                ];
                $news[] = $new_article;
            }
        
            $newsApi_total_pages = $response->totalResults > 0 ? ceil($response->totalResults / $page_size) : 0;            

            $result['news'] = $news;
            $result['articles_count'] = $articles->count();
            $result['total_results'] = $response->totalResults;
            $result['max_pages'] = $newsApi_total_pages;  
        }            
        
        return $result;
    }

   public function getSources(){
        $news_api = Http::get('https://newsapi.org/v2/top-headlines/sources',
                            ['apiKey' => $this->apiKey]
                        );
        $response = json_decode($news_api->getBody());        
        $response_sources = collect($response->sources)->values();
        
        $sources = [];        
        foreach($response_sources  as $source){
            $new_source = [
                "value" => $source->id,
                "label" => $source->name
            ];
            $sources[] = $new_source;
        }
        $additional_sources = [
            ['value' => 'the-guardian', 'label' => 'The Guardian'],
            ['value' => 'nytimes', 'label' => 'The New York Times'],
        ];
        $sources = array_merge($sources, $additional_sources);

        return response()->json($sources , 200);
   }

}
