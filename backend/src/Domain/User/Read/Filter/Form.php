<?php

declare(strict_types=1);

namespace App\Domain\User\Read\Filter;

use App\Domain\User\Entity\User\Role;
use App\Domain\User\Entity\User\User;
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
            ->add('name', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => $this->translator->trans('Name'),
                'onchange' => 'this.form.submit()',
            ]])
            ->add('email', Type\EmailType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Email',
                'onchange' => 'this.form.submit()',
            ]])
            ->add('status', Type\ChoiceType::class, ['choices' => [
                $this->translator->trans('Wait') => User::STATUS_WAIT,
                $this->translator->trans('Active') => User::STATUS_ACTIVE,
                $this->translator->trans('Blocked') => User::STATUS_BLOCKED,
            ], 'required' => false, 'placeholder' => $this->translator->trans('All statuses'),
                'attr' => ['onchange' => 'this.form.submit()']])
            ->add('role', Type\ChoiceType::class, ['choices' => [
                $this->translator->trans('User') => Role::USER,
                $this->translator->trans('Admin') => Role::ADMIN,
            ], 'required' => false, 'placeholder' => 'All roles',
                'attr' => ['onchange' => 'this.form.submit()']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}