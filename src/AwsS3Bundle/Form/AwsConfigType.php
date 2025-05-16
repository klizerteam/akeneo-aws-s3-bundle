<?php

namespace Klizer\AwsS3Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AwsConfigType
 *
 * This form handles the AWS S3 configuration input fields.
 */
class AwsConfigType extends AbstractType
{
    /**
     * Build the AWS S3 configuration form fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('AWS_ACCESS_KEY_ID', TextType::class, [
                'label' => 'Access Key ID',
            ])
            ->add('AWS_SECRET_ACCESS_KEY', TextType::class, [
                'label' => 'Secret Access Key',
            ])
            ->add('AWS_REGION', TextType::class, [
                'label' => 'AWS Region',
            ])
            ->add('AWS_BUCKET_NAME', TextType::class, [
                'label' => 'Bucket Name',
            ])
            ->add('AWS_PREFIX', TextType::class, [
                'label'    => 'File Path Prefix (optional)',
                'required' => false,
            ]);
    }

    /**
     * Configure the default options for this form type.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Can be changed to an entity or DTO class later
        ]);
    }
}
