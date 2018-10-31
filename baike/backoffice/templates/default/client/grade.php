<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Grade</h3>
          <ul class="tab-base">
            <li><a class="current"><span>List</span></a></li>
            <li><a href="<?php echo getUrl('client', 'addGrade', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
          </ul>
      </div>
  </div>
  <div class="container">
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Grade Code';?></td>
            <td><?php echo 'Grade Caption';?></td>
            <td><?php echo 'Min Score';?></td>
            <td><?php echo 'Max Score';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Member Count';?></td>
            <td><?php echo 'Limit Setting';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
          <?php $list = $output['list'];?>
        <?php foreach($list as $row){ ?>
            <tr>
              <td>
                  <?php echo $row['grade_code'] ?>
              </td>
              <td>
                  <?php echo $row['grade_caption'] ?>
              </td>
              <td>
                  <?php echo $row['min_score'] ?>
              </td>
              <td>
                  <?php echo $row['max_score']; ?>
              </td>
              <td>
                <?php echo timeFormat($row['create_time']); ?>
              </td>
              <td>
                <?php echo timeFormat($row['create_time']); ?>
              </td>
              <td>
                  <div class="custom-btn-group">
                    <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('client', 'addGrade', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                        <span><i class="fa fa-vcard-o"></i>Setting</span>
                    </a>
                  </div>
              </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
  </div>
</div>
<script>

</script>
