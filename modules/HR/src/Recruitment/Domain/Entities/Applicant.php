<?php

namespace Modules\HR\Recruitment\Domain\Entities;

class Applicant implements \JsonSerializable
{
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $phone;

    public function __construct(string $id, string $firstName, string $lastName, string $email, ?string $phone = null)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}