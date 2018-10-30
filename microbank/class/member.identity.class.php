<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/9
 * Time: 16:00
 */
class memberIdentityClass
{
    public function __construct()
    {
    }

    public static function getIdentityType()
    {
        return array(
            certificationTypeEnum::ID => "Identity Card",
            certificationTypeEnum::FAIMILYBOOK => "Family Book",
            certificationTypeEnum::PASSPORT => "Passport",
            certificationTypeEnum::RESIDENT_BOOK => "Resident Book",
            certificationTypeEnum::BIRTH_CERTIFICATE => "Birth Certificate",
//            certificationTypeEnum::WORK_CERTIFICATION => "Work Certification",
        );
    }


    /** 手工更新认证行数据为过期，慎用,外部根据情况判断是否可以更新
     * @param $cert_id
     */
    public static function updateCertFileExpired($cert_id, $user_id = 0)
    {
        // 系统自动更新是没有user的
        $user_id = intval($user_id);
        $m_user = new um_userModel();
        $user = $m_user->getRow($user_id);

        $m = new member_verify_certModel();
        $cert = $m->getRow($cert_id);
        if (!$cert) {
            return new result(false, 'Invalid cert id:' . $cert_id);
        }

        if ($cert->verify_state != certStateEnum::PASS) {
            // 没有审核通过的，就不需要处理了
            return new result(true);
        }

        if ($user && $user['user_position'] == userPositionEnum::OPERATOR) {
            $m_mfo = new member_follow_officerModel();
            $mfo = $m_mfo->getRow(array('officer_id' => $user_id, 'member_id' => $cert['member_id'], 'officer_type' => 1, 'is_active' => 1));
            if (!$mfo) {
                return new result(false, 'No follow');
            }
        }


        $cert->verify_state = certStateEnum::EXPIRED;
        $cert->update_time = Now();
        $up = $cert->update();
        if (!$up->STS) {
            return $up;
        }

        // 如果是身份证
        if ($cert->cert_type == certificationTypeEnum::ID) {
            // 更新member的状态
            $m_member = new memberModel();
            $member = $m_member->getRow(array(
                'uid' => $cert->member_id
            ));
            if (!$member) {
                return new result(false, 'Not found member:' . $cert->member_id);
            }
            $old_state = $member->member_state;
            $member_property = my_json_decode($member->member_property);
            $member_property[memberPropertyKeyEnum::ORIGINAL_STATE] = $old_state;

            $member->member_state = memberStateEnum::CHECKED;
            $member->member_property = json_encode($member_property);
            $member->update_time = Now();
            $up = $member->update();
            if (!$up->STS) {
                return $up;
            }

        }

        // 如果是资产
        $asset_class = new member_assetsClass();
        $asset_type = $asset_class->asset_type;
        if (in_array($cert->cert_type, $asset_type)) {
            // 更新资产的状态
            // 找到资产行
            $asset = (new member_assetsModel())->getRow(array(
                'cert_id' => $cert_id
            ));
            if ($asset) {
                // 还未授信的情况下可以更新
                if ($asset->asset_state < assetStateEnum::GRANTED) {
                    $asset->asset_state = assetStateEnum::INVALID;
                    $asset->update_time = Now();
                    $up = $asset->update();
                    if (!$up->STS) {
                        return $up;
                    }
                }
            }
        }

        return new result(true);
    }

    /**
     * 保存新证件
     * @param $p
     * @return result
     */
    public function saveClientNewIdentity($p)
    {
        $uid = intval($p['client_id']);
        $cert_type = intval($p['cert_type']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        $image_structure = global_settingClass::getCertImageStructure();
        $stt = $image_structure[$cert_type];
        $file_keys = array_column($stt, 'file_key');

        $cert_images = array();
        foreach ($file_keys as $key) {
            $cert_file = trim($p[$key]);
            if (!$cert_file) return new result(false, 'Please upload image!');
            $cert_images[$key] = $cert_file;
        }

        if ($cert_type == certificationTypeEnum::ID) {
            $id_number = trim($p['id_number']);
            $expire_date = date('Y-m-d', strtotime($p['expire_date']));
            $gender = trim($p['gender']);
            $civil_status = trim($p['civil_status']);
            $birthday = date('Y-m-d', strtotime($p['birthday']));
            $birth_country = trim($p['birth_country']);

            $birth_province = intval($p['birth_province']);
            $birth_district = intval($p['birth_district']);
            $birth_commune = intval($p['birth_commune']);
            $birth_village = intval($p['birth_village']);
            $address = trim($p['address']);

            $address_detail_arr = array();
            if ($address) {
                $address_detail_arr[] = $address;
            }
            $m_core_tree = M('core_tree');
            if ($birth_village) {
                $birth_village_info = $m_core_tree->find(array('uid' => $birth_village));
                $address_detail_arr[] = $birth_village_info['node_text'];
            }
            if ($birth_commune) {
                $birth_commune_info = $m_core_tree->find(array('uid' => $birth_commune));
                $address_detail_arr[] = $birth_commune_info['node_text'];
            }
            if ($birth_district) {
                $birth_district_info = $m_core_tree->find(array('uid' => $birth_district));
                $address_detail_arr[] = $birth_district_info['node_text'];
            }
            if ($birth_province) {
                $birth_province_info = $m_core_tree->find(array('uid' => $birth_province));
                $address_detail_arr[] = $birth_province_info['node_text'];
            }
            $address_detail = implode(', ', $address_detail_arr);

            $kh_family_name = trim($p['kh_family_name']);
            $kh_given_name = trim($p['kh_given_name']);
            $kh_second_name = trim($p['kh_second_name']);
            $kh_third_name = trim($p['kh_third_name']);

            $en_family_name = trim($p['en_family_name']);
            $en_given_name = trim($p['en_given_name']);
            $en_second_name = trim($p['en_second_name']);
            $en_third_name = trim($p['en_third_name']);

            if (!$id_number || !$birth_country || $birth_province == 0) {
                return new result(false, 'Param Error!');
            }
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "UPDATE member_verify_cert SET verify_state=" . qstr(certStateEnum::EXPIRED)
                . " WHERE member_id = $uid AND cert_type = " . qstr($cert_type) . " AND verify_state = " . qstr(certStateEnum::PASS);
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = $cert_type;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = certSourceTypeEnum::BACK_OPERATOR;
            if ($cert_type == certificationTypeEnum::ID) {
                $new_row->cert_name = $en_family_name . ' ' . $en_given_name;
                $new_row->cert_sn = $id_number;
                $new_row->cert_addr = $address_detail;
                $new_row->cert_expire_time = $expire_date;
            }
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file(getImageUrl($img));
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            if ($cert_type == certificationTypeEnum::ID) {
                $client_info->id_sn = $id_number;
                $client_info->nationality = $birth_country;
                $client_info->id_en_name_json = my_json_encode(
                    array(
                        'en_family_name' => $en_family_name,
                        'en_given_name' => $en_given_name,
                        'en_second_name' => $en_second_name,
                        'en_third_name' => $en_third_name,
                    )
                );
                $client_info->id_kh_name_json = my_json_encode(
                    array(
                        'kh_family_name' => $kh_family_name,
                        'kh_given_name' => $kh_given_name,
                        'kh_second_name' => $kh_second_name,
                        'kh_third_name' => $kh_third_name,
                    )
                );

                $client_info->initials = strtoupper(substr($en_family_name, 0, 1));
                $client_info->display_name = $en_family_name . ' ' . $en_given_name;
                $client_info->kh_display_name = $kh_family_name . ' ' . $kh_given_name;
                $client_info->alias_name = array(
                    'en' => $client_info->display_name,
                    'kh' => $client_info->kh_display_name
                );
                $client_info->gender = $gender;
                $client_info->civil_status = $civil_status;
                $client_info->birthday = $birthday;

                $client_info->id_address1 = $birth_province;
                $client_info->id_address2 = $birth_district;
                $client_info->id_address3 = $birth_commune;
                $client_info->id_address4 = $birth_village;
                $client_info->address_detail = $address_detail;
                $client_info->address = $address;
                $client_info->id_expire_time = $expire_date;
                $client_info->update_time = Now();

                $rt_1 = $client_info->update();
                if (!$rt_1->STS) {
                    $conn->rollback();
                    showMessage('Save Failed!');
                }
            }

            $is_auto = !global_settingClass::isAllowOperatorApproveAssetsByCO();
            if ($is_auto) {
                $new_row->verify_state = certStateEnum::PASS;
                $new_row->verify_remark = "Auto Approve By System";
                $new_row->auditor_id = $p['auditor_id'];
                $new_row->auditor_name = $p['auditor_name'];
                $new_row->auditor_time = Now();
                $ret_update = $new_row->update();
                if (!$ret_update->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }

                $m_member_verify_cert = new member_verify_certModel();
                $operator = array(
                    'operator_id' => $p['auditor_id'],
                    'operator_name' => $p['auditor_name'],
                );
                $rt_5 = $m_member_verify_cert->rejectCertBySystem($uid, $cert_type, Now(), $operator);
                if (!$rt_5->STS) {
                    return $rt_5;
                }

                if ($client_info->member_state != memberStateEnum::VERIFIED && $cert_type == certificationTypeEnum::ID) {
                    $rt = memberClass::changeMemberState($uid, memberStateEnum::VERIFIED, 'Auto Verify By System', $p['auditor_id'], memberVerifyTypeEnum::ID_CARD, false);
                    if (!$rt->STS) {
                        $conn->rollback();
                        return new result(false, 'Save Failed.' . $rt->MSG);
                    }
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    /**
     * 保存身份证信息
     * @param $p
     * @return result
     */
    public function saveIdentityAuthentication($p)
    {
        $member_id = intval($p['client_id']);
        $id_number = trim($p['id_number']);
        $expire_date = date('Y-m-d', strtotime($p['expire_date']));
        $gender = intval($p['gender']);
        $civil_status = intval($p['civil_status']);
        $birthday = date('Y-m-d', strtotime($p['birthday']));
        $birth_country = trim($p['birth_country']);

        $birth_province = intval($p['birth_province']);
        $birth_district = intval($p['birth_district']);
        $birth_commune = intval($p['birth_commune']);
        $birth_village = intval($p['birth_village']);
        $address = trim($p['address']);

        $address_detail = "";
        $m_core_tree = M('core_tree');
        if ($birth_province) {
            $birth_province_info = $m_core_tree->find(array('uid' => $birth_province));
            $address_detail .= $birth_province_info['node_text'];
        }
        if ($birth_district) {
            $birth_district_info = $m_core_tree->find(array('uid' => $birth_district));
            $address_detail .= ', ' . $birth_district_info['node_text'];
        }
        if ($birth_commune) {
            $birth_commune_info = $m_core_tree->find(array('uid' => $birth_commune));
            $address_detail .= ', ' . $birth_commune_info['node_text'];
        }
        if ($birth_village) {
            $birth_village_info = $m_core_tree->find(array('uid' => $birth_village));
            $address_detail .= ', ' . $birth_village_info['node_text'];
        }
        if ($address) {
            $address_detail .= ', ' . $address;
        }

        $kh_family_name = trim($p['kh_family_name']);
        $kh_given_name = trim($p['kh_given_name']);
        $kh_second_name = trim($p['kh_second_name']);
        $kh_third_name = trim($p['kh_third_name']);

        $en_family_name = trim($p['en_family_name']);
        $en_given_name = trim($p['en_given_name']);
        $en_second_name = trim($p['en_second_name']);
        $en_third_name = trim($p['en_third_name']);

        $id_front = trim($p['id_front']);
        $id_back = trim($p['id_back']);
        $id_handheld = trim($p['id_handheld']);

        $m_client_member = M('client_member');
        $row = $m_client_member->getRow($member_id);
        if (!$row) {
            showMessage('Invalid Id!');
        }

        if (!$id_number || !$birth_country || !$id_front || !$id_back || !$id_handheld || $birth_province == 0) {
            showMessage('Param Error!');
        }

        // 检查是否被他人认证过
        $sql = "select * from member_verify_cert where member_id!='$member_id' and cert_type='" . certificationTypeEnum::ID . "'
        and cert_sn='$id_number' order by uid desc";
        $other = $m_client_member->reader->getRow($sql);
        if ($other) {
            return new result(false, 'ID has been certificated', null, errorCodesEnum::ID_SN_HAS_CERTIFICATED);
        }

        $row->id_sn = $id_number;
        $row->nationality = $birth_country;
        $row->id_en_name_json = my_json_encode(
            array(
                'en_family_name' => $en_family_name,
                'en_given_name' => $en_given_name,
                'en_second_name' => $en_second_name,
                'en_third_name' => $en_third_name,
            )
        );
        $row->id_kh_name_json = my_json_encode(
            array(
                'kh_family_name' => $kh_family_name,
                'kh_given_name' => $kh_given_name,
                'kh_second_name' => $kh_second_name,
                'kh_third_name' => $kh_third_name,
            )
        );

        $row->initials = strtoupper(substr($en_family_name, 0, 1));
        $row->display_name = $en_family_name . ' ' . $en_given_name;
        $row->kh_display_name = $kh_family_name . ' ' . $kh_given_name;
        $row->alias_name = array(
            'en' => $row->display_name,
            'kh' => $row->kh_display_name
        );

        $row->gender = $gender;
        $row->civil_status = $civil_status;
        $row->birthday = $birthday;

        $row->id_address1 = $birth_province;
        $row->id_address2 = $birth_district;
        $row->id_address3 = $birth_commune;
        $row->id_address4 = $birth_village;
        $row->address_detail = $address_detail;
        $row->address = $address;
        $row->id_expire_time = $expire_date;
        $row->update_time = Now();

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $sql = "UPDATE member_verify_cert SET verify_state='" . certStateEnum::EXPIRED . "' WHERE member_id='" . $member_id . "'
                 AND cert_type='" . certificationTypeEnum::ID . "' AND verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                showMessage('Update history cert fail');
            }

            $rt_1 = $row->update();
            if (!$rt_1->STS) {
                $conn->rollback();
                showMessage('Save Failed!');
            }

            $m_member_verify_cert = M('member_verify_cert');
            $m_member_verify_cert_image = M('member_verify_cert_image');
            $row_cert = $m_member_verify_cert->newRow();
            $row_cert->member_id = $member_id;
            $row_cert->cert_type = certificationTypeEnum::ID;
            $row_cert->cert_name = $row->display_name;
            $row_cert->cert_sn = $id_number;
            $row_cert->cert_addr = $address_detail;
            $row_cert->cert_expire_time = $expire_date;
            $row_cert->source_type = 1;
            $row_cert->verify_state = certStateEnum::CREATE;
            $row_cert->create_time = Now();
            $rt_2 = $row_cert->insert();
            if (!$rt_2->STS) {
                $conn->rollback();
                showMessage('Save Failed!');
            }

            $cert_images = array(
                certImageKeyEnum::ID_HANDHELD => $id_handheld,
                certImageKeyEnum::ID_FRONT => $id_front,
                certImageKeyEnum::ID_BACK => $id_back,
            );

            foreach ($cert_images as $key => $img) {
                $row_cert_img = $m_member_verify_cert_image->newRow();
                $row_cert_img->cert_id = $rt_2->AUTO_ID;
                $row_cert_img->image_key = $key;
                $row_cert_img->image_url = $img;
                $row_cert_img->image_sha = sha1_file(getImageUrl($img));
                $rt_3 = $row_cert_img->insert();
                if (!$rt_3->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }
            }

            $is_auto = !global_settingClass::isAllowOperatorApproveAssetsByCO();
            if ($is_auto) {
                $row_cert->verify_state = certStateEnum::PASS;
                $row_cert->verify_remark = "Auto Approve By System";
                $row_cert->auditor_id = $p['auditor_id'];
                $row_cert->auditor_name = $p['auditor_name'];
                $row_cert->auditor_time = Now();
                $ret_update = $row_cert->update();
                if (!$ret_update->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }

                if ($row->member_state != memberStateEnum::VERIFIED) {
                    $row->member_state = memberStateEnum::VERIFIED;
                    $row->update_time = Now();
                    $ret_update = $row->update();
                    if (!$ret_update->STS) {
                        $conn->rollback();
                        return new result(false, 'Save Failed!');
                    }
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $member_id), false, ENTRY_COUNTER_SITE_URL)));
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 保存户口本
     * @param $p
     * @return result
     */
    public function saveFamilyBookAuthentication($p)
    {
        $uid = intval($p['client_id']);
        $family_book_front = trim($p['family_book_front']);
        $family_book_back = trim($p['family_book_back']);
        $family_book_household = trim($p['family_book_household']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        if (!$family_book_front || !$family_book_back || !$family_book_household) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $uid . "'
                 and cert_type='" . certificationTypeEnum::FAIMILYBOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = certificationTypeEnum::FAIMILYBOOK;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = array(
                certImageKeyEnum::FAMILY_BOOK_FRONT => $family_book_front,
                certImageKeyEnum::FAMILY_BOOK_BACK => $family_book_back,
                certImageKeyEnum::FAMILY_BOOK_HOUSEHOLD => $family_book_household,
            );

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file(getImageUrl($img));
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $is_auto = !global_settingClass::isAllowOperatorApproveAssetsByCO();
            if ($is_auto) {
                $new_row->verify_state = certStateEnum::PASS;
                $new_row->verify_remark = "Auto Approve By System";
                $new_row->auditor_id = $p['auditor_id'];
                $new_row->auditor_name = $p['auditor_name'];
                $new_row->auditor_time = Now();
                $ret_update = $new_row->update();
                if (!$ret_update->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }


    /**
     * 保存居住证信息
     * @param $p
     * @return result
     */
    public function saveResidentBookAuthentication($p)
    {
        $uid = intval($p['client_id']);
        $resident_book_front = trim($p['resident_book_front']);
        $resident_book_back = trim($p['resident_book_back']);

        $m_client_member = M('client_member');
        $client_info = $m_client_member->getRow(array('uid' => $uid));
        if (!$client_info) {
            return new result(false, 'No eligible clients!');
        }

        if (!$resident_book_front || !$resident_book_back) {
            return new result(false, 'Param Error!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            //更新原来通过的为过期状态
            $sql = "update member_verify_cert set verify_state='" . certStateEnum::EXPIRED . "' where member_id='" . $uid . "'
                 and cert_type='" . certificationTypeEnum::RESIDENT_BOOK . "' and verify_state='" . certStateEnum::PASS . "'  ";
            $up = $m_client_member->conn->execute($sql);
            if (!$up->STS) {
                $conn->rollback();
                return new result(false, 'Update history cert fail');
            }

            $m_cert = new member_verify_certModel();
            $m_image = new member_verify_cert_imageModel();

            $new_row = $m_cert->newRow();
            $new_row->member_id = $uid;
            $new_row->cert_type = certificationTypeEnum::RESIDENT_BOOK;
            $new_row->verify_state = certStateEnum::PASS;
            $new_row->source_type = 1;
            $new_row->create_time = Now();
            $insert = $new_row->insert();
            if (!$insert->STS) {
                $conn->rollback();
                return new result(false, 'Save Failed');
            }

            $cert_images = array(
                certImageKeyEnum::RESIDENT_BOOK_FRONT => $resident_book_front,
                certImageKeyEnum::RESIDENT_BOOK_BACK => $resident_book_back,
            );

            foreach ($cert_images as $key => $img) {
                $row = $m_image->newRow();
                $row->cert_id = $new_row->uid;
                $row->image_key = $key;
                $row->image_url = $img;
                $row->image_sha = sha1_file(getImageUrl($img));
                $insert = $row->insert();
                if (!$insert->STS) {
                    $conn->rollback();
                    return new result(false, 'Add cert image fail');
                }
            }

            $is_auto = !global_settingClass::isAllowOperatorApproveAssetsByCO();
            if ($is_auto) {
                $new_row->verify_state = certStateEnum::PASS;
                $new_row->verify_remark = "Auto Approve By System";
                $new_row->auditor_id = $p['auditor_id'];
                $new_row->auditor_name = $p['auditor_name'];
                $new_row->auditor_time = Now();
                $ret_update = $new_row->update();
                if (!$ret_update->STS) {
                    $conn->rollback();
                    return new result(false, 'Save Failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Save Successful!', array('url' => getUrl('member', 'documentCollection', array('client_id' => $uid), false, ENTRY_COUNTER_SITE_URL)));

        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    public static function getIdInfoByMemberId($member_id)
    {
        $member_id = intval($member_id);
        $m_client_member = M('client_member');
        $client_info = $m_client_member->find(array('uid' => $member_id));
        if (!$client_info['id_sn']) {
            return array();
        }

        $id_info = array(
            'id_en_name' => implode(' ', my_json_decode($client_info['id_en_name_json'])),
            'id_kh_name' => implode(' ', my_json_decode($client_info['id_kh_name_json'])),
            'id_sn' => $client_info['id_sn'],
            'nationality' => $client_info['nationality'],
            'id_expire_time' => $client_info['id_expire_time'],
            'phone_id' => $client_info['phone_id'],
            'member_icon' => $client_info['member_icon'],
        );

        $address_detail = $client_info['address'];
        $m_core_tree = M('core_tree');
        if ($client_info['id_address4']) {
            $node_info = $m_core_tree->find(array('uid' => $client_info['id_address4']));
            $address_detail .= ($address_detail ? ', ' : '') . $node_info['node_text'];
        }
        if ($client_info['id_address3']) {
            $node_info = $m_core_tree->find(array('uid' => $client_info['id_address3']));
            $address_detail .= ($address_detail ? ', ' : '') . $node_info['node_text'];
        }
        if ($client_info['id_address2']) {
            $node_info = $m_core_tree->find(array('uid' => $client_info['id_address2']));
            $address_detail .= ($address_detail ? ', ' : '') . $node_info['node_text'];
        }
        if ($client_info['id_address1']) {
            $node_info = $m_core_tree->find(array('uid' => $client_info['id_address1']));
            $address_detail .= ($address_detail ? ', ' : '') . $node_info['node_text'];
        }

        $id_info['address_detail'] = $address_detail;
        return $id_info;
    }

    public static function getMemberVerifiedCert($member_id)
    {
        $r = new ormReader();
        $member_id = intval($member_id);
        $verify_type = memberIdentityClass::getIdentityType();
        $verify_type_keys = array_keys($verify_type);
        $verify_type_str = "(" . implode(',', $verify_type_keys) . ")";
        $sql = "SELECT * FROM member_verify_cert WHERE uid IN (SELECT MAX(uid) FROM member_verify_cert WHERE member_id = $member_id AND cert_type IN $verify_type_str AND verify_state = " . certStateEnum::PASS . " GROUP BY cert_type)";
        $cert_list = $r->getRows($sql);
        foreach ($cert_list as $key => $cert) {
            $sql = "SELECT * FROM member_verify_cert_image WHERE cert_id = " . intval($cert['uid']);
            $image_list = $r->getRows($sql);
            $cert_list[$key]['image_list'] = $image_list;
        }
        $cert_list = resetArrayKey($cert_list, 'cert_type');
        return $cert_list;
    }

}