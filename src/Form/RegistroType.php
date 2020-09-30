<?php

namespace App\Form;

use App\Entity\Registro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;




class RegistroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('apaterno')
            ->add('institucion')
            ->add('genero', ChoiceType::class, [
                'choices'  => [
                    'Seleccionar' => null,
                    'Hombre' => true,
                    'Mujer' => false,
                ],
            ])
            ->add('correo')
            ->add('porcentaje', ChoiceType::class, [
                'choices'=>array(
                    '50'=>'50',
                    '60'=>'60',
                    '70'=>'70',
                    '80'=>'80',
                    '90'=>'90',
                    '100'=>'100'),
                'placeholder'=>'Seleccionar',
                'required'=>true,
    ])
            ->add('profesorInst')
            ->add('profesorCorreo')
            ->add('credencialFile', VichFileType::class, array(
                'required'=> true,
                'label' => 'Credencial vigente o algo que muestre que está inscrito este semestre'
            ))
            ->add('historialFile', VichFileType::class, array(
                'required'=> true,
                'label' => 'Historial académico'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registro::class,
        ]);
    }
}
