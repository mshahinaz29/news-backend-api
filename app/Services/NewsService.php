<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Models\UserPreference;
use App\Services\{NYTimesService, TheGuardianService, NewsApiOrgService};

class NewsService
{
    /**
     * @var $newsRepository
     */
    protected $newsRepository;

    public $news = [];
    public $articles_count_fetched = 0;
    public $total_results = 0;
    public $max_pages = 0;
    /**
     * NewsService constructor.
     *
     * @param NewsRepository $newsRepository
     */
    // public function __construct(NewsRepository $newsRepository)
    // {
    //     $this->newsRepository = $newsRepository;
    // }

    public function getAll(
        int $user_id,
        ?string $keyword,
        int $page = 1,
        ?array $categories,
        ?array $sources,
        ?string $from_date,
        ?string $to_date,
    )
    {
        $max_articles_per_page = 30;
        $total_pages = 0;
        $cat_labels = [];
        $source_labels = [];
        $source_values = [];

        // if no categories and sources filtered
        // show preferred categories and sources instead
        if($user_id && !$categories && !$sources && !$keyword){ 
            $preference = UserPreference::where('user_id',$user_id)->first();
            if($preference){
                $categories = json_decode($preference->categories);
                $sources = json_decode($preference->sources);
            }
        }
        
        if($categories){
            $cat_labels = array_column($categories, 'label');
        }
        if($sources){
            $source_labels = array_column($sources, 'label');
            $source_values = array_column($sources, 'value');
        }
        
        //The New York Times news api
        $nyt = new NYTimesService();
        $response = $nyt->getNews($keyword, $page, $cat_labels, $source_labels, $from_date, $to_date );     
        $this->mapNewsData($response);   
        
        //The guardian API
        if(!$sources || ($sources && in_array("The Guardian", $source_labels)))
        {
            $guardian_api = new TheGuardianService;
            $response = $guardian_api->getNews($keyword, $page, $cat_labels, $source_labels, $from_date, $to_date );     
            $this->mapNewsData($response);  
        }
        
        
        // NEWS API
        $news_api_page_size = $max_articles_per_page - $this->articles_count_fetched;
        $news_api_org = new NewsApiOrgService;
        $response = $news_api_org->getNews($keyword, $page, $cat_labels, $source_values, $from_date, $to_date, $news_api_page_size );     
        $this->mapNewsData($response);

        $news = collect($this->news)->sortByDesc('date')->values();
        $result['articles'] = $news->toArray();
        $result['meta'] = [
                            'total_results' => $this->total_results,
                            'max_pages' => $this->max_pages
                          ];
        return $result;
        
    }

    public function mapNewsData($response)
    {
        $this->news = isset($response['news']) ? array_merge($this->news, $response['news']) : $this->news;
        $this->articles_count_fetched += isset($response['articles_count']) ? $response['articles_count'] : 0;
        $this->total_results += isset($response['total_results']) ? $response['total_results'] : 0;
        $this->max_pages = isset($response['max_pages']) && $response['max_pages'] > $this->max_pages ? $response['max_pages'] : $this->max_pages;
    }

    public function getCategories()
    {
        $guardian_api = new TheGuardianService;
        $response = $guardian_api->getCategories();

        return $response;
    }

    public function getSources()
    {
        $news_api_org = new NewsApiOrgService;
        $response = $news_api_org->getSources();

        return $response;
    }


}
