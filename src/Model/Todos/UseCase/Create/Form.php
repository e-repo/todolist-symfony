<?php

declare(strict_types=1);

namespace App\Model\Todos\UseCase\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class Form extends AbstractType
{
    private TranslatorInterface $translator;

    /**
     * Form constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, ['attr' => [
                'placeholder' => $this->translator->trans('Task name', [], 'task')
            ]])
            ->add('description', Type\TextareaType::class, ['attr' => [
                'placeholder' => $this->translator->trans('Task description', [], 'task'),
                'rows' => 4
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}