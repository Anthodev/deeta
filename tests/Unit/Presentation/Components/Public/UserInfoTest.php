<?php

declare(strict_types=1);

namespace App\Tests\Unit\Presentation\Components\Public;

use App\Presentation\Components\Public\UserInfo;
use Faker\Factory;

beforeEach(function () {
    $this->component = new UserInfo();
    $this->faker = Factory::create();
});

it('mounts with correct properties', function () {
    // Given
    $fullName = $this->faker->name();
    $jobTitle = $this->faker->jobTitle();
    $company = $this->faker->company();
    $location = $this->faker->city().', '.$this->faker->country();
    $profileImagePath = $this->faker->imageUrl(640, 480, 'people');

    // When
    $this->component->mount(
        $fullName,
        $jobTitle,
        $company,
        $location,
        $profileImagePath
    );

    // Then
    expect($this->component->fullName)
        ->toBe($fullName)
        ->and($this->component->jobTitle)
        ->toBe($jobTitle)
        ->and($this->component->company)
        ->toBe($company)
        ->and($this->component->location)
        ->toBe($location)
        ->and($this->component->profileImagePath)
        ->toBe($profileImagePath);
});

it('mounts with null values', function () {
    // When
    $this->component->mount();

    // Then
    expect($this->component->fullName)
        ->toBeNull()
        ->and($this->component->jobTitle)
        ->toBeNull()
        ->and($this->component->company)
        ->toBeNull()
        ->and($this->component->location)
        ->toBeNull()
        ->and($this->component->profileImagePath)
        ->toBeNull();
});

it('mounts with partial values', function () {
    // Given
    $fullName = $this->faker->name();
    $jobTitle = $this->faker->jobTitle();

    // When
    $this->component->mount(
        $fullName,
        $jobTitle
    );

    // Then
    expect($this->component->fullName)
        ->toBe($fullName)
        ->and($this->component->jobTitle)
        ->toBe($jobTitle)
        ->and($this->component->company)
        ->toBeNull()
        ->and($this->component->location)
        ->toBeNull()
        ->and($this->component->profileImagePath)
        ->toBeNull();
});
