<?php
require_once APP_PATH . '/models/Post.php';

class BlogController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }

    public function index()
    {
        $posts = $this->postModel->getPublished();
        return ['posts' => $posts];
    }

    public function show($slug)
    {
        $post = $this->postModel->findBySlug($slug);
        if (!$post) {
            return null;
        }
        return ['post' => $post];
    }
}
