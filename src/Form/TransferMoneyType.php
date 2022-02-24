<?php

namespace App\Form;

use App\Entity\Account;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class TransferMoneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            -> add('recipient', EntityType::class,[
                'class'=>Account::class ,
                'choice_label' => 'nom_compte'] )
            ->add('money', NumberType::class, [
                'label' => "Montant d'argent",
                'attr' => ['placeholder' => '200'],
                'constraints' => [
                    new  Positive([
                        'message' => 'Attention le montant doit être au minimum de 1€'
                    ]),
                    new NotBlank([
                        'message' => 'Merci de donner un montant',
                    ]),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' =>  "Envoyer",
                'attr' => ['class' => 'btn my-[2rem] mx-auto hover:opacity-80']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Account::class,
        ]);
    }
}