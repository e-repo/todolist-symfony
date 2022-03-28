<?php

declare(strict_types=1);

namespace App\Domain\Todos\Read\Task\Filter;

use App\Domain\Todos\Entity\Task\Task;
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
                'placeholder' => $this->translator->trans('Task name', [], 'task'),
                'onchange' => 'this.form.submit()',
            ]])
            ->add('description', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => $this->translator->trans('Task description', [], 'task'),
                'onchange' => 'this.form.submit()',
            ]])
            ->add('status', Type\ChoiceType::class, ['choices' => [
                $this->translator->trans('Published', [], 'task') => Task::STATUS_PUBLISHED,
                $this->translator->trans('Fulfilled', [], 'task') => Task::STATUS_FULFILLED,
            ], 'required' => false, 'placeholder' => $this->translator->trans('All statuses')])
            ->add('date', Type\TextType::class, ['required' => false, 'attr' => [
                'placeholder' => $this->translator->trans('Date'),
                'class' => 'js-task-date',
                'autocomplete' => 'off',
                'onchange' => 'this.form.submit()',
            ]]);
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