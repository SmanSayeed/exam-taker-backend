<?php

// App\DTOs\StudentDTO\StudentRegistrationData.php

namespace App\DTOs\StudentDTO;

use Spatie\LaravelData\Data;

class StudentRegistrationData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public int $section_id,
        public string $password,
        public string $password_confirmation,
        public ?string $profile_image,
        public ?string $ip_address,
        public ?string $country,
        public ?string $country_code,
        public ?string $address,
        public bool $active_status
    ) {}
    /**
     * Custom method to convert DTO to array
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}
