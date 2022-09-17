<?php

declare(strict_types=1);

namespace App\Domain\Auth\User\UseCase\Role\Change;

use App\Domain\Auth\User\Entity\User\Role;
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
            ->add('role', Type\ChoiceType::class, ['choices' => [
                $this->translator->trans('User') => Role::USER,
                $this->translator->trans('Admin') => Role::ADMIN,
            ], 'required' => true, 'placeholder' => $this->translator->trans('Select role')]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Command::class);
    }
}