<?php

declare(strict_types=1);

namespace App\Models;

class Ebook extends Media
{
    private string $isbn;
    private string $category;
    private float $rentalPrice;
    private int $totalCopies;
    private int $availableCopies;
    private string $description;

    public function __construct(
        string $id,
        string $title,
        string $author,
        int $publishedYear,
        string $isbn,
        string $category,
        float $rentalPrice,
        int $totalCopies,
        int $availableCopies,
        string $description = ''
    ) {
        parent::__construct($id, $title, $author, $publishedYear);
        $this->setIsbn($isbn);
        $this->setCategory($category);
        $this->setRentalPrice($rentalPrice);
        $this->setTotalCopies($totalCopies);
        $this->setAvailableCopies($availableCopies);
        $this->setDescription($description);
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): void
    {
        $this->isbn = trim($isbn);
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = trim($category);
    }

    public function getRentalPrice(): float
    {
        return $this->rentalPrice;
    }

    public function setRentalPrice(float $rentalPrice): void
    {
        $this->rentalPrice = max(0.0, $rentalPrice);
    }

    public function getTotalCopies(): int
    {
        return $this->totalCopies;
    }

    public function setTotalCopies(int $totalCopies): void
    {
        $this->totalCopies = max(0, $totalCopies);
    }

    public function getAvailableCopies(): int
    {
        return $this->availableCopies;
    }

    public function setAvailableCopies(int $availableCopies): void
    {
        $this->availableCopies = max(0, min($availableCopies, $this->totalCopies));
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    public function borrowCopy(): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        $this->availableCopies--;
        return true;
    }

    public function returnCopy(): bool
    {
        if ($this->availableCopies >= $this->totalCopies) {
            return false;
        }
        $this->availableCopies++;
        return true;
    }

    public function getDetails(): string
    {
        return sprintf(
            '%s | %s | ISBN: %s | Rp %s (%d/%d tersedia)',
            parent::getDetails(),
            $this->category,
            $this->isbn,
            number_format($this->rentalPrice, 0, ',', '.'),
            $this->availableCopies,
            $this->totalCopies
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'publishedYear' => $this->publishedYear,
            'isbn' => $this->isbn,
            'category' => $this->category,
            'rentalPrice' => $this->rentalPrice,
            'totalCopies' => $this->totalCopies,
            'availableCopies' => $this->availableCopies,
            'description' => $this->description,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['author'] ?? ''),
            (int) ($data['publishedYear'] ?? 2024),
            (string) ($data['isbn'] ?? ''),
            (string) ($data['category'] ?? 'Umum'),
            (float) ($data['rentalPrice'] ?? 0.0),
            (int) ($data['totalCopies'] ?? 1),
            (int) ($data['availableCopies'] ?? 0),
            (string) ($data['description'] ?? '')
        );
    }
}

