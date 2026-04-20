<?php

class GlobalPost
{
    use Model;
    protected $table = 'global_posts';

    public function getTotalPosts()
    {
        return (int) $this->count([]);
    }
}
