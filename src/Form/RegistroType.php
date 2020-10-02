<?php

namespace App\Form;

use App\Entity\Registro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Intl\Countries;



class RegistroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countries = Countries::getNames();

        foreach($countries as $country =>  $name){

        $countries2[$name]= $name;

    }

        $builder
            ->add('nombre')
            ->add('apaterno')
            ->add('institucion')
            ->add('genero', ChoiceType::class, [
                'label'=>'Género',
                'required'=>true,
                'placeholder'=>'Seleccionar',
                'choices'  => [
                    'Femenino' => 'Femenino',
                    'Masculino' => 'Masculino',
                    'Otro'=>'Otro',
                    'Prefiero no especificar'=>'Prefiero no especificar'
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
            ->add('pais', ChoiceType::class, [
                'choices' => $countries2,
                'label'=>'País de residencia',
                'placeholder'  => 'Seleccionar'
                ,
            ])
            ->add('profesorInst')
            ->add('profesorCorreo')
            ->add('credencialFile', VichFileType::class, array(
                'required'=> false,
                'label' => 'Credencial vigente o algo que muestre que estás inscrito este semestre'
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
