<?php
/**
 * Copyright (c) 2016 PMG <https://www.pmg.com>
 *
 * License: MIT
 */
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * Use inside your form types.
 *
 *    public function buildForm(FormBuilderInterface $build, array $options)
 *    {
 *      $build->add('password', PasswordType::class);
 *      $build->add('remove_password', CheckboxType::class, [
 *        'mapped' => false,
 *      ]);
 *      $build->addEventSubscriber(new KeepValueListener('password', 'remove_password'));
 *    }
 */
final class KeepValueListener implements EventSubscriberInterface
{
    /**
     * The name of a field whose value should be kept if an
     * empty value is submitted.
     *
     * @var string
     */
    private $keepField;
    /**
     * The boolean (checkbox) field that tells the listener to clear
     * the field.
     *
     * @var string
     */
    private $clearField;
    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;


    public function __construct($keepField, $clearField=null, PropertyAccessorInterface $accessor=null)
    {
        $this->keepField = $keepField;
        $this->clearField = $clearField;
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $field = $event->getForm()->get($this->keepField);
        $submitData = $event->getData();
        $erase = false;
        if ($this->clearField && isset($submitData[$this->clearField])) {
            $erase = self::asBool($submitData[$this->clearField]);
        }
        if ($erase) {
            $submitData[$this->keepField] = $field->getConfig()->getEmptyData();
            $event->setData($submitData);
            return;
        }
        if (empty($submitData[$this->keepField])) {
            $submitData[$this->keepField] = $this->accessor->getValue(
                $event->getForm()->getData(),
                $field->getPropertyPath()
            );
            $event->setData($submitData);
        }
    }
    private static function asBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}