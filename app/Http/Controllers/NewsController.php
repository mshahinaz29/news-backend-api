<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Http;
use App\Models\UserPreference;
use App\Services\NewsService;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{

    /**
     * NewsController Constructor
     *
     * @param NewsService $newsService
     *
     */

     public function __construct(NewsService $newsService)
     {
         $this->newsService = $newsService;
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $result = ['status' => 200];

        try {
            $result['data'] = $this->newsService->getAll(
                $request->user()->id,
                $request->keyword,
                $request->page,
                $request->categories,
                $request->sources,
                $request->from_date,
                $request->to_date
            );
            
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        
        return new NewsResource($result);
                
    }

    public function getNewsCategories()
    {    
        $result = ['status' => 200];

        try {
            $result['data'] = $this->newsService->getCategories();
            
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return new NewsResource($result);        
    }

    public function getNewsSources()
    {
        $result = ['status' => 200];

        try {
            $result['data'] = $this->newsService->getSources();
            
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return new NewsResource($result);        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  News $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        //
    }

    /**
     * Update news.
     *
     * @param Request $request
     * @param id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
