<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Validator;

use AlexandreBulete\DddPageBundle\Domain\Repository\PageRepositoryInterface;
use AlexandreBulete\DddPageBundle\Domain\Service\SlugGeneratorInterface;
use AlexandreBulete\DddPageBundle\Domain\ValueObject\Slug;
use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniquePageSlugValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PageRepositoryInterface $pages,
        private readonly SlugGeneratorInterface $slugger,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePageSlug) {
            throw new UnexpectedTypeException($constraint, UniquePageSlug::class);
        }
        if (!$value instanceof PageResource) {
            return;
        }

        $typed = $value->slug !== null && trim($value->slug) !== '';
        $source = $typed ? $value->slug : $value->title;
        if ($source === null || trim($source) === '') {
            return;
        }

        try {
            $slug = $typed ? Slug::fromString($source) : $this->slugger->slugify($source);
        } catch (\InvalidArgumentException) {
            // A malformed slug is the Regex constraint's to report.
            return;
        }

        $holder = $this->pages->findBySlug($slug);
        if ($holder === null || ($value->id !== null && $holder->id->value()->equals($value->id))) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ slug }}', $slug->value())
            ->atPath($typed ? 'slug' : 'title')
            ->addViolation();
    }
}
