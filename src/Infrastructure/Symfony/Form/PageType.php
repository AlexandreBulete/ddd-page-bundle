<?php

declare(strict_types=1);

namespace AlexandreBulete\DddPageBundle\Infrastructure\Symfony\Form;

use AlexandreBulete\DddPageBundle\Infrastructure\Sylius\Resource\PageResource;
use AlexandreBulete\DddPageBundle\Infrastructure\Config\ConfiguredPlacementCatalog;
use AlexandreBulete\DddSymfonyBundle\Form\Type\MarkdownType;
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
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'page.page.title'])
            ->add('slug', TextType::class, ['label' => 'page.slug.label', 'help' => 'page.slug.help', 'required' => false])
            ->add('body', MarkdownType::class, ['label' => 'page.page.body', 'help' => 'page.page.body_help', 'required' => false, 'attr' => ['rows' => 22]])
            ->add('placement', ChoiceType::class, [
                'label' => 'page.page.placement',
                'help' => 'page.page.placement_help',
                'required' => false,
                'placeholder' => 'page.page.placement_none',
                'choices' => $this->placements->choices(),
            ])
            ->add('published', CheckboxType::class, ['label' => 'page.page.published', 'help' => 'page.page.published_help', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageResource::class]);
    }
}
