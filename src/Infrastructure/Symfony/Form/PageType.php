<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Form;

use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddPageBundle\Infrastructure\Config\ConfiguredPlacementCatalog;
use AlexandreBulete\DddSymfonyBundle\Form\Type\MarkdownType;
use AlexandreBulete\DddPageBundle\Infrastructure\Symfony\PublicPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function __construct(
        private readonly ConfiguredPlacementCatalog $placements,
        private readonly ?PublicPage $publicPage = null,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'page.page.title', 'section' => 'content'])
                        ->add('body', MarkdownType::class, ['label' => 'page.page.body', 'help' => 'page.page.body_help', 'required' => false, 'attr' => ['rows' => 22], 'section' => 'content'])
            ->add('placement', ChoiceType::class, [
                'label' => 'page.page.placement',
                'help' => 'page.page.placement_help',
                'required' => false,
                'placeholder' => 'page.page.placement_none',
                'choices' => $this->placements->choices(),
                'section' => 'publication',
            ])
            ->add('published', CheckboxType::class, ['label' => 'page.page.published', 'help' => 'page.page.published_help', 'required' => false, 'switch' => true, 'section' => 'publication'])
            ->add('slug', TextType::class, ['label' => 'page.slug.label', 'label_attr' => ['class' => 'visually-hidden'], 'help' => 'page.slug.help', 'required' => false, 'prefix' => $this->publicPage?->prefix(), 'section' => 'address']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageResource::class,
            'sections' => [
                'content' => ['label' => 'page.section.content'],
                'publication' => ['label' => 'page.section.publication', 'column' => 'side'],
                'address' => ['label' => 'page.section.address', 'column' => 'side'],
            ],
        ]);
    }
}
