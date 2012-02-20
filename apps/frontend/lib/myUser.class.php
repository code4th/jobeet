<?php

class myUser extends sfBasicSecurityUser
{
    var $sortkey = array();
    public function resetJobHistory()
    {
        $this->getAttributeHolder()->remove('job_history');
    }

    
    public function addJobToHistory(JobeetJob $job)
    {
        $ids = $this->getAttribute('job_history', array()); 
//        if(!in_array($job->getId(), $ids))
        {
            array_unshift($ids, $job->getId());
            $ids = array_unique($ids);
            $this->setAttribute('job_history', array_slice($ids,0,3));
        }
    }
    private function setSortKey($ids)
    {
        $this->sortkey = array();
        $pri=0;
        foreach($ids as $id)
        {
            $this->sortkey[$id] = $pri++;
        }

     }
    private function cmp($lhs,$rhs)
    {
        return $this->sortkey[$lhs->getId()] > $this->sortkey[$rhs->getId()];
    }
    public function getJobHistory()
    {
        $ids = $this->getAttribute('job_history', array());

        if (empty($ids)) return array();

        $historys = Doctrine_Core::getTable('JobeetJob')
            ->createQuery('b')
            ->whereIn('b.id', $ids)
            ->execute();
        $this->setSortKey($ids);
        $data = $historys->getData();
        usort($data,array("myUser", "cmp"));
        $historys->setData($data);
        return $historys;

    }
}
