<?php

namespace Modules\HR\PersonnelAdmin\Domain\Entities;

class Employee implements \JsonSerializable
{
    private $id;
    private $salary;
    private $address;
    private $maritalStatus;
    private $lastName;
    private $emergencyContact;
    private $bankDetails;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSalary(): ?float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): void
    {
        $this->salary = $salary;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus(string $maritalStatus): void
    {
        $this->maritalStatus = $maritalStatus;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmergencyContact(): ?string
    {
        return $this->emergencyContact;
    }

    public function setEmergencyContact(string $emergencyContact): void
    {
        $this->emergencyContact = $emergencyContact;
    }

    public function getBankDetails(): ?string
    {
        return $this->bankDetails;
    }

    public function setBankDetails(string $bankDetails): void
    {
        $this->bankDetails = $bankDetails;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'salary' => $this->salary,
            'address' => $this->address,
            'marital_status' => $this->maritalStatus,
            'last_name' => $this->lastName,
            'emergency_contact' => $this->emergencyContact,
            'bank_details' => $this->bankDetails,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}