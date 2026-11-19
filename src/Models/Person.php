<?php

declare(strict_types=1);

namespace App\Models;

abstract class Person
{
    protected string $id;
    protected string $fullName;
    protected string $email;

    public function __construct(string $id, string $fullName, string $email)
    {
        $this->setId($id);
        $this->setFullName($fullName);
        $this->setEmail($email);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = trim($id);
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = trim($fullName);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower(trim($email));
    }

    public function getDetails(): string
    {
        return sprintf('%s (%s)', $this->fullName, $this->email);
    }
}

