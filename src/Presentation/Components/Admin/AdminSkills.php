<?php

declare(strict_types=1);

namespace App\Presentation\Components\Admin;

use App\Application\Command\Info\CreateSkillCommand;
use App\Application\Query\Info\GetSkillsForUserQuery;
use App\Domain\Model\Info\Skill;
use App\Presentation\Components\Common\FlashBag;
use App\Shared\Dto\Info\SkillDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'Components/Admin/admin_skills.html.twig')]
final class AdminSkills
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?string $userId = null;

    /**
     * @var array<int, SkillDto>
     */
    #[LiveProp(
        writable: false,
        useSerializerForHydration: true,
        serializationContext: [
            'groups' => ['skill'],
            'item_type' => SkillDto::class,
        ]
    )]
    public array $skills = [];

    #[LiveProp(writable: true)]
    public string $skillLabel = '';

    #[LiveProp(writable: true)]
    public string $skillDefaultColor = '';

    #[LiveProp(writable: true)]
    public int $skillPosition = 0;

    /**
     * @var array<string, string>
     */
    #[LiveProp]
    public array $errors = [];

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
        $this->errors = [];
    }

    /**
     * @param Skill[] $skills
     */
    public function mount(
        string $userId,
        array $skills,
    ): void {
        $this->userId = $userId;
        $this->skills = array_map(
            fn (Skill $skill): SkillDto => new SkillDto(
                id: $skill->getId() ?? '',
                label: $skill->getLabel(),
                position: $skill->getPosition(),
                userId: $skill->getUser()->getId() ?? '',
                defaultColor: $skill->getDefaultColor() ?? '',
            ),
            $skills
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[LiveAction]
    public function save(): void
    {
        $this->errors = [];
        $isValid = true;

        if (empty($this->skillLabel)) {
            $this->errors['skillLabel'] = 'Veuillez saisir un label';
            $isValid = false;
        }

        if (!$isValid) {
            return;
        }

        $userId = $this->userId;

        if (null === $userId) {
            $this->logger->error('Impossible de créer une compétence, l\'ID utilisateur est null');

            $this->emit(
                FlashBag::MESSAGE_NEW,
                [
                    'message' => 'Veuillez vous reconnecter à votre compte',
                    'type' => FlashBag::TYPE_ERROR,
                ]);

            $this->resetForm();

            return;
        }

        $this->messageBus->dispatch(new CreateSkillCommand(
            userId: $userId,
            label: $this->skillLabel,
            position: $this->skillPosition,
            defaultColor: $this->skillDefaultColor,
        ));

        $this->emit(
            FlashBag::MESSAGE_NEW,
            [
                'message' => 'Compétence créée',
                'type' => FlashBag::TYPE_SUCCESS,
            ]);

        $this->resetForm();

        $this->refreshSkills();
    }

    #[LiveListener('skill-deleted')]
    public function onSkillDeleted(): void
    {
        $this->refreshSkills();
    }

    private function refreshSkills(): void
    {
        $userId = $this->userId;

        if (null === $userId) {
            return;
        }

        $envelope = $this->messageBus->dispatch(new GetSkillsForUserQuery(
            userId: $userId,
        ));

        $this->skills = $this->getContentFromMessage($envelope);
    }

    private function resetForm(): void
    {
        $this->skillLabel = '';
        $this->skillDefaultColor = '';
        $this->skillPosition = 0;
        $this->errors = [];
    }

    /**
     * @return array<int, SkillDto>
     */
    private function getContentFromMessage(Envelope $envelope): array
    {
        $stamp = $envelope->last(HandledStamp::class);
        if (!$stamp) {
            $this->logger->error('Récupération des compétences pour l\'utilisateur échouée');

            return [];
        }

        $result = $stamp->getResult();

        if (null === $result) {
            return [];
        }

        try {
            if (!is_object($result) || !method_exists($result, 'getContent')) {
                $this->logger->error('Le résultat n\'a pas de méthode getContent');

                return [];
            }

            $content = $result->getContent();

            if (is_array($content) && (!empty($content) && $content[0] instanceof SkillDto)) {
                return $content;
            }

            if (!is_array($content)) {
                $this->logger->error('Le contenu des compétences n\'est pas un tableau');

                return [];
            }

            /** @var array<int, SkillDto> */
            return $content;
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la récupération des compétences: '.$e->getMessage());

            return [];
        }
    }
}
