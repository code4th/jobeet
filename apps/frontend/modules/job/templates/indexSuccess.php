<div id="jobs">
  <table class="jobs">
    <?php foreach ($jobeet_jobs as $i => $job): ?>
      <tr class="<?php echo fmod($i, 2) ? 'even' : 'odd' ?>">
        <td class="location"><?php echo $job->getLocation() ?></td>
        <td class="position">
          <!-- <a href="<?php echo url_for('job/show?'.'&company='.$job->getCompany().'&location='.$job->getLocation().'&id='.$job->getId().'&position='.$job->getPosition()) ?>"> -->
          <a href="<?php echo url_for(array('sf_route' => 'job_show_user', 'sf_subject' => $job, 'unk' => 'うんこ')) ?>">


            <?php echo $job->getPosition() ?>
          </a>
        </td>
        <td class="company"><?php echo $job->getCompany() ?></td>
      </tr>
    <?php endforeach ?>
  </table>
</div>