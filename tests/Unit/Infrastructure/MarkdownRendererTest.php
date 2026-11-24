<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Tests\Unit\Infrastructure;

use AlexandreBulete\DddPageBundle\Infrastructure\Markdown\MarkdownRenderer;
use AlexandreBulete\DddPageBundle\Infrastructure\Text\AsciiSlugGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MarkdownRendererTest extends TestCase
{
    #[Test]
    public function markdown_is_rendered_and_html_is_not(): void
    {
        $html = (new MarkdownRenderer())->toHtml("## Host\n\n**Name**, [site](https://example.test)\n\n<script>alert(1)</script>\n\n[x](javascript:alert(1))");

        self::assertStringContainsString('<h2>Host</h2>', $html);
        self::assertStringContainsString('<strong>Name</strong>', $html);
        self::assertStringContainsString('<a href="https://example.test">site</a>', $html);
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringNotContainsString('javascript:', $html);
    }

    #[Test]
    public function a_title_becomes_a_slug(): void
    {
        self::assertSame('politique-de-confidentialite', (new AsciiSlugGenerator())->slugify('Politique de confidentialité')->value());
    }
}
