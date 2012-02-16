<?php

/**
 * JobeetJob form.
 *
 * @package    jobeet
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class JobeetJobForm extends BaseJobeetJobForm
{
  public function configure()
  {
    $this->useFields(array('category_id', 'type', 'company', 'logo', 'url', 'position', 'location', 'description', 'how_to_apply', 'is_public', 'email'));

    $this->widgetSchema->setLabels(array(
      'category_id'    => 'Category',
      'is_public'      => 'Public?',
      'how_to_apply'   => 'How to apply?',
    ));

    $this->widgetSchema['category_id'] = new sfWidgetFormChoice(array(
        'choices'  => array( 'Choise!' ) + $this->widgetSchema['category_id']->getChoices(),
        'multiple' => false,
        'expanded' => false,
        ));
    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
        'choices'  => array( 'Choise!' ) + Doctrine_Core::getTable('JobeetJob')->getTypes(),
        'multiple' => false,
        'expanded' => false,
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array(
        'choices' => array_keys(Doctrine_Core::getTable('JobeetJob')->getTypes()),
    ));

    $this->widgetSchema['logo'] = new sfWidgetFormInputFile(array(
    'label' => 'Company logo',
    ));
    $this->validatorSchema['logo'] = new sfValidatorFile(array(
    'required'   => false,
    'path'       => sfConfig::get('sf_upload_dir').'/jobs',
    'mime_types' => 'web_images',
    ));    

    $this->widgetSchema->setHelp('is_public', 'Whether the job can also be published on affiliate websites or not.');

    $this->validatorSchema['email'] = 
            new sfValidatorAnd(
                    array( $this->validatorSchema['email'], new sfValidatorEmail(),)
                    );


  }
}
