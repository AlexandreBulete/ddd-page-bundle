<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\ValueObject;

use AlexandreBulete\DddFoundation\Domain\ValueObject\StringVO;

/**
 * The URL segment of a page: lowercase ASCII letters and digits, separated by
 * single hyphens. Uniqueness is a use case's business.
 */
final readonly class Slug extends StringVO
{
    public const int MAX_LENGTH = 190;
    public const string PATTERN = '[a-z0-9]+(?:-[a-z0-9]+)*';

    protected function normalize(string $value): string
    {
        return strtolower(trim($value));
    }

    protected function validate(string $value): void
    {
        if (strlen($value) > self::MAX_LENGTH || preg_match('/^' . self::PATTERN . '$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid slug "%s": lowercase letters and digits separated by hyphens, %d characters at most.', $value, self::MAX_LENGTH));
        }
    }
}
