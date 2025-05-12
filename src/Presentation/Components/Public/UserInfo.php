<?php

declare(strict_types=1);

namespace App\Presentation\Components\Public;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Public/user_info.html.twig')]
class UserInfo
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $fullName = null;

    #[LiveProp]
    public ?string $jobTitle = null;

    #[LiveProp]
    public ?string $company = null;

    #[LiveProp]
    public ?string $location = null;

    #[LiveProp]
    public ?string $profileImagePath = null;

    public function mount(
        ?string $fullName = null,
        ?string $jobTitle = null,
        ?string $company = null,
        ?string $location = null,
        ?string $profileImagePath = null,
    ): void {
        $this->fullName = $fullName;
        $this->jobTitle = $jobTitle;
        $this->company = $company;
        $this->location = $location;
        $this->profileImagePath = $profileImagePath;
    }
}
