<?php

declare(strict_types=1);

namespace App\Models;

class User extends Person
{
    private string $membershipTier;
    private string $joinedAt;

    public function __construct(
        string $id,
        string $fullName,
        string $email,
        string $membershipTier = 'Standard',
        string $joinedAt = ''
    ) {
        parent::__construct($id, $fullName, $email);
        $this->setMembershipTier($membershipTier);
        $this->setJoinedAt($joinedAt !== '' ? $joinedAt : date('Y-m-d'));
    }

    public function getMembershipTier(): string
    {
        return $this->membershipTier;
    }

    public function setMembershipTier(string $membershipTier): void
    {
        $allowed = ['Standard', 'Student', 'Premium'];
        $tier = trim($membershipTier);
        $this->membershipTier = in_array($tier, $allowed, true) ? $tier : 'Standard';
    }

    public function getJoinedAt(): string
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(string $joinedAt): void
    {
        $this->joinedAt = trim($joinedAt);
    }

    public function getDetails(): string
    {
        $tierLabels = [
            'Standard' => 'Standar',
            'Student' => 'Pelajar',
            'Premium' => 'Premium',
        ];
        $tierLabel = $tierLabels[$this->membershipTier] ?? $this->membershipTier;

        return sprintf(
            '%s | Paket: %s | Bergabung: %s',
            parent::getDetails(),
            $tierLabel,
            $this->joinedAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'membershipTier' => $this->membershipTier,
            'joinedAt' => $this->joinedAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['fullName'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['membershipTier'] ?? 'Standard'),
            (string) ($data['joinedAt'] ?? date('Y-m-d'))
        );
    }
}

