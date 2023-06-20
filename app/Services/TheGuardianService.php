<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;

class TheGuardianService
{
    protected $endpoint = 'https://content.guardianapis.com/search';
    protected $apiKey = 'f26e36cf-7772-45c1-8e7e-221d8bd79614';
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

        $guardian_params = [
            'show-fields' => 'starRating,headline,thumbnail,short-url,trailText',
            'show-tags' => 'contributor',
            'page-size' => '10',
            'page' => $page,
            'show-references' => 'author',
            'order-by' => 'newest',
            'api-key' => $this->apiKey,
        ];

        $keyword ? $guardian_params['q'] = $keyword : '';
        $from_date ? $guardian_params['from-date'] = date('Y-m-d', strtotime($from_date)):'';
        $to_date ? $guardian_params['to-date'] = date('Y-m-d', strtotime($to_date)):'';
        $categories ? $guardian_params['section'] = '('.strtolower(implode('|', $categories)).')' : '';
        
        $guardian_api = Http::get($this->endpoint, $guardian_params);
        $response = json_decode($guardian_api->getBody()); 
        
        if(isset($response->response->results))   {                            
            $articles = collect($response->response->results);            
            foreach($articles as $art){
                $new_article =  [
                    'source_name' => 'The Guardian',
                    'author' => $art->tags[0]->webTitle ?? 'Anonymous',
                    'title' => $art->webTitle,
                    'description' => $art->fields->trailText,
                    'thumbnail' => $art->fields->thumbnail,
                    'url' => $art->webUrl,
                    'published_at' => date('F j, Y', strtotime($art->webPublicationDate)),
                    'date' => $art->webPublicationDate,
                    'content' => $art->fields->trailText,
                    'category' => $art->sectionName,
                    'author_image' => $art->tags[0]->bylineImageUrl ?? 'https://p7.hiclipart.com/preview/707/137/261/the-guardian-guardian-media-group-theguardian-com-news-journalism-the-guardian-logo.jpg'
                ];
                $news[] = $new_article;
            }
        }  

        $result['news'] = $news;
        $result['articles_count'] = $articles->count();
        $result['total_results'] = $response->response->total;
        $result['max_pages'] = $response->response->pages;      
        
        return $result;
    }

    public function getCategories(){

        $categories = [];
        $news_api = Http::get('https://content.guardianapis.com/sections',
                            ['api-key' => $this->apiKey]
                        );
        $response = json_decode($news_api->getBody());
        if(isset($response->response->results)){
            $response_categories = collect($response->response->results)->values();
                
            foreach($response_categories as $category){
                $cat = [
                    "value" => $category->id,
                    "label" => $category->webTitle
                ];
                $categories[] = $cat;
            }
        }
        


        return response()->json($categories, 200);
    }

   

}
