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
  protected function removeFields()
  {
    unset(
      $this['created_at'], $this['updated_at'],
      $this['expires_at'], $this['is_activated'],
      $this['token']
    );
  }    
  public function configure()
  {

    $this->removeFields();
    
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

    $this->widgetSchema->setNameFormat('job[%s]');

  }
}
class BackendJobeetJobForm extends JobeetJobForm
{
  public function configure()
  {
    parent::configure();
 
    $this->widgetSchema['logo'] = new sfWidgetFormInputFileEditable(array(
      'label'     => 'Company logo',
      'file_src'  => '/uploads/jobs/'.$this->getObject()->getLogo(),
      'is_image'  => true,
      'edit_mode' => !$this->isNew(),
      'template'  => '<div>%file%<br />%input%<br />%delete% %delete_label%</div>',
    ));
 
    $this->validatorSchema['logo_delete'] = new sfValidatorPass();
  }
     
  protected function removeFields()
  {
    unset(
      $this['created_at'], $this['updated_at'],
      $this['token']
    );
  }
}
