<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Domain\Exception;

/**
 * A page online must have a text.
 */
final class EmptyPageException extends \DomainException
{
}
