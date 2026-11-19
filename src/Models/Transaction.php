<?php

declare(strict_types=1);

namespace App\Models;

class Transaction
{
    private string $id;
    private string $userId;
    private string $ebookId;
    private string $borrowedAt;
    private string $dueDate;
    private ?string $returnedAt;
    private float $rentalFee;
    private string $status;

    public function __construct(
        string $id,
        string $userId,
        string $ebookId,
        string $borrowedAt,
        string $dueDate,
        float $rentalFee,
        string $status = 'Active',
        ?string $returnedAt = null
    ) {
        $this->setId($id);
        $this->setUserId($userId);
        $this->setEbookId($ebookId);
        $this->setBorrowedAt($borrowedAt);
        $this->setDueDate($dueDate);
        $this->setRentalFee($rentalFee);
        $this->setStatus($status);
        $this->setReturnedAt($returnedAt);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = trim($id);
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = trim($userId);
    }

    public function getEbookId(): string
    {
        return $this->ebookId;
    }

    public function setEbookId(string $ebookId): void
    {
        $this->ebookId = trim($ebookId);
    }

    public function getBorrowedAt(): string
    {
        return $this->borrowedAt;
    }

    public function setBorrowedAt(string $borrowedAt): void
    {
        $this->borrowedAt = trim($borrowedAt);
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function setDueDate(string $dueDate): void
    {
        $this->dueDate = trim($dueDate);
    }

    public function getReturnedAt(): ?string
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?string $returnedAt): void
    {
        $this->returnedAt = $returnedAt !== null ? trim($returnedAt) : null;
    }

    public function getRentalFee(): float
    {
        return $this->rentalFee;
    }

    public function setRentalFee(float $rentalFee): void
    {
        $this->rentalFee = max(0.0, $rentalFee);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $allowed = ['Active', 'Returned'];
        $clean = trim($status);
        $this->status = in_array($clean, $allowed, true) ? $clean : 'Active';
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public function markReturned(?string $returnDate = null): void
    {
        $this->status = 'Returned';
        $this->returnedAt = $returnDate ?? date('Y-m-d');
    }

    public function getDetails(): string
    {
        return sprintf(
            'Transaksi %s: Anggota %s meminjam %s pada %s (Jatuh tempo: %s) - %s',
            $this->id,
            $this->userId,
            $this->ebookId,
            $this->borrowedAt,
            $this->dueDate,
            $this->isActive() ? 'Aktif' : 'Dikembalikan'
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'ebookId' => $this->ebookId,
            'borrowedAt' => $this->borrowedAt,
            'dueDate' => $this->dueDate,
            'returnedAt' => $this->returnedAt,
            'rentalFee' => $this->rentalFee,
            'status' => $this->status,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['userId'] ?? ''),
            (string) ($data['ebookId'] ?? ''),
            (string) ($data['borrowedAt'] ?? date('Y-m-d')),
            (string) ($data['dueDate'] ?? date('Y-m-d', strtotime('+14 days'))),
            (float) ($data['rentalFee'] ?? 0.0),
            (string) ($data['status'] ?? 'Active'),
            isset($data['returnedAt']) ? (string) $data['returnedAt'] : null
        );
    }
}

