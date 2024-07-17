<?php

declare(strict_types=1);

namespace App\Application\Common\Action;

use App\Application\Common\Enum\SerializerGroupNameEnum;
use App\Application\Common\Exception\AccessDeniedHttpException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseAction
{
    #[Required]
    public Security $security;

    /**
     * @param string[] $groups
     */
    protected function output(
        mixed $data = null,
        int $status = Response::HTTP_OK,
        array $groups = [],
    ): Response {
        if (null === $data) {
            return new JsonResponse(
                status: $status,
            );
        }

        $groups = array_merge($groups, [SerializerGroupNameEnum::DEFAULT_READ->value]);

        $contexts = [
            UidNormalizer::NORMALIZATION_FORMAT_KEY => UidNormalizer::NORMALIZATION_FORMAT_RFC4122,
            DateTimeNormalizer::FORMAT_KEY => \DateTimeInterface::ATOM,
        ];

        $groupContext = (new ObjectNormalizerContextBuilder())->withGroups($groups)->toArray();

        $contexts = array_merge($contexts, $groupContext);

        $encoders = [new JsonEncoder()];
        $normalizers = [
            new UidNormalizer(),
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: new ClassMetadataFactory(new AttributeLoader()),
            ),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        $serializedContent = $serializer->serialize(
            $data,
            JsonEncoder::FORMAT,
            $contexts,
        );

        return new JsonResponse(
            data: $serializedContent,
            status: $status,
            json: true,
        );
    }

    protected function denyAccessUnlessGranted(string $attribute, mixed $subject = null): void
    {
        if (!$this->security->isGranted($attribute, $subject)) {
            throw new AccessDeniedHttpException();
        }
    }
}
