<?php

declare(strict_types=1);

namespace App\Models;

abstract class Media
{
    protected string $id;
    protected string $title;
    protected string $author;
    protected int $publishedYear;

    public function __construct(
        string $id,
        string $title,
        string $author,
        int $publishedYear
    ) {
        $this->setId($id);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->setPublishedYear($publishedYear);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = trim($id);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = trim($author);
    }

    public function getPublishedYear(): int
    {
        return $this->publishedYear;
    }

    public function setPublishedYear(int $publishedYear): void
    {
        $this->publishedYear = $publishedYear;
    }

    public function getDetails(): string
    {
        return sprintf('%s oleh %s (%d)', $this->title, $this->author, $this->publishedYear);
    }
}

