<?php

/**
 * JobeetJob
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    jobeet
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class JobeetJob extends BaseJobeetJob
{
  public function __toString()
  {
    return sprintf('%s at %s (%s)', $this->getPosition(), $this->getCompany(), $this->getLocation());
  }
  
  public function getCompanySlug()
  {
    return Jobeet::slugify($this->getCompany());
  }
  
  public function getPositionSlug()
  {
    return Jobeet::slugify($this->getPosition());
  }
  
  public function getLocationSlug()
  {
    return Jobeet::slugify($this->getLocation());
  }

    public function getTypeName()
    {
    $types = Doctrine_Core::getTable('JobeetJob')->getTypes();
    return $this->getType() ? $types[$this->getType()] : '';
    }

    public function isExpired()
    {
    return $this->getDaysBeforeExpires() < 0;
    }

    public function expiresSoon()
    {
    return $this->getDaysBeforeExpires() < 5;
    }

    public function getDaysBeforeExpires()
    {
    return ceil(($this->getDateTimeObject('expires_at')->format('U') - time()) / 86400);
    }
    public function publish()
    {
    $this->setIsActivated(true);
    $this->save();
    }   
    public function extend($force = false)
    {
        if (!$force && !$this->expiresSoon())
        {
        return false;
        }

        $this->setExpiresAt(date('Y-m-d', time() + 86400 * sfConfig::get('app_active_days')));
        $this->save();

        return true;
    }
  public function asArray($host)
  {
    return array(
      'category'     => $this->getJobeetCategory()->getName(),
      'type'         => $this->getType(),
      'company'      => $this->getCompany(),
      'logo'         => $this->getLogo() ? 'http://'.$host.'/uploads/jobs/'.$this->getLogo() : null,
      'url'          => $this->getUrl(),
      'position'     => $this->getPosition(),
      'location'     => $this->getLocation(),
      'description'  => $this->getDescription(),
      'how_to_apply' => $this->getHowToApply(),
      'expires_at'   => $this->getCreatedAt(),
    );
  }
    
  public function save(Doctrine_Connection $conn = null)
  {
    if ($this->isNew() && !$this->getExpiresAt())
    {
      $now = $this->getCreatedAt() ? $this->getDateTimeObject('created_at')->format('U') : time();
      $this->setExpiresAt(date('Y-m-d H:i:s', $now + 86400 * sfConfig::get('app_active_days')));
    }

    if (!$this->getToken())
    {
        $this->setToken(sha1($this->getEmail().rand(11111, 99999)));
    }
    
    $conn = $conn ? $conn : $this->getTable()->getConnection();
    $conn->beginTransaction();
    try
    {
        $ret = parent::save($conn);

        $this->updateLuceneIndex();

        $conn->commit();

        return $ret;
    }
    catch (Exception $e)
    {
        $conn->rollBack();
        throw $e;
    }
    
    
  }
    public function delete(Doctrine_Connection $conn = null)
    {
        $index = JobeetJobTable::getLuceneIndex();

        foreach ($index->find('pk:'.$this->getId()) as $hit)
        {
            $index->delete($hit->id);
        }

        return parent::delete($conn);
    }
    public function updateLuceneIndex()
    {
    $index = JobeetJobTable::getLuceneIndex();

    // 既存のエントリを削除する
    foreach ($index->find('pk:'.$this->getId()) as $hit)
    {
        $index->delete($hit->id);
    }

    // 有効期限切れおよびアクティブではない求人をインデックスに登録しない
    if ($this->isExpired() || !$this->getIsActivated())
    {
        return;
    }

    $doc = new Zend_Search_Lucene_Document();

    // 検索結果で区別できるように job の主キーを保存する
    $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $this->getId()));

    // job フィールドをインデックスに登録する
    $doc->addField(Zend_Search_Lucene_Field::UnStored('position', $this->getPosition(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('company', $this->getCompany(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('location', $this->getLocation(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $this->getDescription(), 'utf-8'));

    // 求人をインデックスに追加する
    $index->addDocument($doc);
    $index->commit();
    }  
}
 
