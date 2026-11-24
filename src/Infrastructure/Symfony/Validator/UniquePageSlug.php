<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * The slug a back-office form will end up with — typed, or derived from the
 * title — is not held by another page. The use case checks it again; this
 * turns the common case into a form error instead of an error page.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class UniquePageSlug extends Constraint
{
    public string $message = 'page.slug.taken';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
