<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Http;
use App\Models\UserPreference;

class UserPreferenceController extends Controller
{

    /**
     * NewsController Constructor
     *
     * @param NewsService $newsService
     *
     */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = ['status' => 200];
        
        try {
            $preference = UserPreference::where('user_id',$request->user_id)->firstOrFail();

            $result['categories'] = json_decode($preference->categories);
            $result['sources'] = json_decode($preference->sources);
            $result['authors'] = json_decode($preference->authors);

        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($result);
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
        $result = ['status' => 200];
        \Log::debug($request->all());
        try {
            $preference =  UserPreference::updateOrCreate(
                ['user_id' => $request->user_id],
                [
                    'categories' => json_encode($request->categories), 
                    'sources' => json_encode($request->sources),
                    'authors' => json_encode($request->authors)
                ]
            );
            $result['data'] = $preference;
            \Log::debug($preference);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result);
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
