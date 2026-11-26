<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\ValueObject;

use AlexandreBulete\DddFoundation\Domain\ValueObject\StringVO;

/**
 * Where a site links a page — its footer, its menu, a reserved spot — as the
 * key the application declares (`legal`, `menu`…). Which keys exist is the
 * application's business, not the page's.
 */
final readonly class Placement extends StringVO
{
    public const int MAX_LENGTH = 50;
    public const string PATTERN = '[a-z][a-z0-9_]*';

    protected function normalize(string $value): string
    {
        return trim($value);
    }

    protected function validate(string $value): void
    {
        if (strlen($value) > self::MAX_LENGTH || preg_match('/^' . self::PATTERN . '$/', $value) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid placement "%s": lowercase letters, digits and underscores, %d characters at most.', $value, self::MAX_LENGTH));
        }
    }
}
