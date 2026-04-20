<?php

class UniversityPost
{
    use Model;
    protected $table = 'university_posts';

    public function getTotalPosts()
    {
        return (int) $this->count([]);
    }
}
