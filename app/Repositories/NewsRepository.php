<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    /**
     * @var News
     */
    protected $news;

    /**
     * NewsRepository constructor.
     *
     * @param News $news
     */
    public function __construct(News $news)
    {
        $this->news = $news;
    }

    /**
     * Get all newss.
     *
     * @return News $news
     */
    public function getAll()
    {
        return $this->news
            ->get();
    }

    /**
     * Get news by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->news
            ->where('id', $id)
            ->get();
    }

    /**
     * Save News
     *
     * @param $data
     * @return News
     */
    public function save($data)
    {
        $news = new $this->news;

        $news->name = $data['name'];
        $news->is_completed = $data['is_completed'];

        $news->save();

        return $news->fresh();
    }

    /**
     * Update News
     *
     * @param $data
     * @return News
     */
    public function update($data, $id)
    {
        
        $news = $this->news->find($id);

        $news->is_completed = $data['is_completed'];

        $news->update();

        return $news;
    }

    /**
     * Update News
     *
     * @param $data
     * @return News
     */
    public function delete($id)
    {
        
        $news = $this->news->find($id);
        $news->delete();

        return $news;
    }

}
