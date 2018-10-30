<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 10:51
 */
class member_gradeModel extends tableModelBase
{

    public function __construct()
    {
        parent::__construct('member_grade');
    }

    public function insertGrade($param){
      $grade_code = $param['grade_code'];
      $min_score = $param['min_score'];
      $max_score = $param['max_score'];
      $grade_caption = $param['grade_caption'];
      $creator_id = $param['creator_id'];
      $creator_name = $param['creator_name'];
      $insert = $this->newRow();
        $chk_code = $this->find(array('grade_code' => $grade_code));
        if ($chk_code) {
            return new result(false, 'The Grade already exists!');
        }
      $insert->grade_code = $grade_code;
      $insert->min_score = $min_score;
      $insert->max_score = $max_score;
      $insert->grade_caption = $grade_caption;
      $insert->creator_id = $creator_id;
      $insert->creator_name = $creator_name;
      $insert->create_time = Now();
      $rt = $insert->insert();
      if ($rt->STS) {
        return new result(true, 'Add successful!');
      } else {
        return new result(false, 'Add failed--' . $rt->MSG);
      }
    }
    
    public function updateGrade($param)
    {
        $grade_id = intval($param['grade_id']);
        $grade_code = trim($param['grade_code']);
        $min_score = intval($param['min_score']);
        $max_score = intval($param['max_score']);
        $grade_caption = trim($param['grade_caption']);
        $chk_code = $this->find(array('grade_code' => $grade_code, 'uid' => array('neq', $grade_id)));
        if ($chk_code) {
            return new result(false, 'The Grade already exists!');
        }
        $row = $this->getRow($grade_id);
        $row->grade_code = $grade_code;
        $row->min_score = $min_score;
        $row->max_score = $max_score;
        $row->grade_caption = $grade_caption;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Setting successful!');
        } else {
            return new result(false, 'Setting failed--' . $rt->MSG);
        }
    }


}
