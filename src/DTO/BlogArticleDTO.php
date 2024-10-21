<?php 

namespace App\DTO;

use App\Entity\User;

class BlogArticleDTO
{
    public string $id;
    public string $title;
    public string $slug;
    public string $content;
    public string $author;

    public function __construct(string $id, string $title, string $slug, string $content, User $author)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->author = $author->getUsername();
    }
}
