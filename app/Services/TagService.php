<?php

namespace App\Services;

class TagService
{

    protected $model;

    public function __construct(string $model)
    {
        $this->initializeSubject($model);
    }

    protected function initializeSubject($model): static
    {

        $this->model = $model;

        return $this;
    }

    public function get_tags()
    {
        $tags = $this->model::select('tags', 'topics')->whereNotNull('tags')->orWhereNotNull('topics')->lazy(100)->collect();
        return $this->setup_tags($tags);
    }

    private function setup_tags($data)
    {
        $tags_exist = array();
        foreach ($data as $tag) {
            $arr = explode(",",$tag->tags);
            foreach ($arr as $i) {
                if (!(in_array($i, $tags_exist))){
                    array_push($tags_exist,$i);
                }
            }
        }

        $topics_exist = array();
        foreach ($data as $topic) {
            $arr = explode(",",$topic->topics);
            foreach ($arr as $i) {
                if (!(in_array($i, $topics_exist))){
                    array_push($topics_exist,$i);
                }
            }
        }

        return array(
            'tags_exist' => $tags_exist,
            'topics_exist' => $topics_exist,
        );
    }
}
