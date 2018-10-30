<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
   span {
        display: inline-block;
    }
   .down_line {
       height: 18px;
       line-height: 9px;
       border-bottom: 1px dashed black;
       padding: 5px;
       margin: 0px 1px;
   }

    p{
        margin:5px 10px;
    }
    .tips{
        margin-top: 10px;
    }
</style>
<?php include(template("print_form/inc.print.header"));?>
<div style="margin: 10px">
    <div>
        <p style="font-size: 18px;font-weight: 600;text-align: center;margin-bottom: 10px">Credit Agreement</p>
        <p>This Agreement is made on the day of<span class="down_line" style="width: 110px"><?php echo date('Y-m-d', time())?></span>it is  agreed by and among the parties as follows:</p>
        <p>1-First Party : <span class="down_line" style="width: 610px"><?php echo 'SAMRITHISAK MICROFINANCE LIMITED'?></span></p>
        <p>Address : <span class="down_line" style="width: 635px"><?php echo $output['branch_address']?></span></p>
        <p>Branch : <span class="down_line" style="width: 640px"><?php echo $output['branch_info']['branch_name']?></span></p>
        <p>(Hereinafter known as “Lender”) and </p>
        <p>2-Second Party : <span class="down_line" style="width: 595px"><?php echo $output['client_info']['display_name']?></span></p>
        <p>Address : <span class="down_line" style="width: 635px"><?php echo $output['member_full_address']?></span></p>
        <p>Contract Phone : <span class="down_line" style="width: 595px"><?php echo $output['client_info']['phone_id']?></span></p>
        <p>(Hereinafter known as “Borrower”) </p>
        <p>Borrower and Lender shall collectively be known herein as “the Parties”. In determining the rights and duties of the Parties under the Credit Agreement, the entire document must be read as a whole.</p>
    </div>
    <div style="margin-top: 10px">
        <p style="font-size: 16px;font-weight:400;text-align: center">CREDIT TERMS</p>
        <p style="padding-left: 20px">The Borrower and Lender, hereby further set forth their rights and obligations to one another under the Credit Agreement and agreed to be legal bound as follows:</p>
        <p>1.Maximum line of credit.</p>
        <p style="padding-left: 20px">The parties agree that the maximum Line of Credit extended hereunder shall not exceed the maximum principal sum of <br/> $<span class="down_line" style="width: 150px"><?php echo ncPriceFormat($output['grant_info']['max_credit'])?></span></p>
        <p>2.Expiry-Date Of Credit</p>
        <p style="padding-left: 20px">The Expiry Date of this agreement is<span class="down_line" style="width: 150px"><?php echo $output['credit_info']['expire_time']?></span>, It can only be borrowed before this time</p>
        <p>3.Loan Fee</p>
        <p style="padding-left: 20px">The borrower agrees to pay the cost of this Agreement<span class="down_line" style="width: 150px"><?php echo $output['contract_info']['fee']?></span></p>
        <p>4.Coborrower & guarantee</p>
        <?php if($output['relative_list']){ ?>
            <?php foreach ($output['relative_list'] as $value) { ?>
                <p style="padding-left: 20px">
                    <span><?php echo 'Name : '; ?></span>
                    <span class="down_line" style="width: 130px;"><?php echo $value['name'] ?></span>
                    <span><?php echo 'ID No.'; ?></span>
                    <span class="down_line" style="width: 145px;"><?php echo $value['id_sn'] ?></span>
                    <span><?php echo 'Relation Ship'; ?></span>
                    <span class="down_line" style="width: 65px;"><?php echo $value['relation_name'] ?></span>
                    <span><?php echo 'Relation Type'; ?></span>
                    <span class="down_line" style="width: 80px;"><?php echo $value['relation_type'] ?></span>
                </p>
            <?php }?>
        <?php }else{ ?>
            <p style="padding-left: 20px">No Records </p>
        <?php  } ?>
        <p>5.Payment Method</p>
        <p style="padding-left: 20px" class="col-sm-12">
            <?php foreach ($output['product_info'] as $value){ ?>
                <input type="checkbox" class="col-sm-4" style="margin-left: 10px"> <?php echo $value['sub_product_name']?>
            <?php }?>
        </p>
        <p style="margin-top: 20px">6.ប្រការ២: ​អំពីលក្ខខណ្ឌពិសេស </p>
        <p class="tips">២.១- : ភាគី(ខ) អះអាងថាទ្រព្យសម្បត្តិ ដែលបង្កើតហ៊ីប៉ូតែកនេះ ពិតជាកម្មសិទ្ធិស្របច្បាប់របស់ខ្លួន ឬក្រុមហ៊ុន ដោយពុំមានពាក់ព័ន្ធនឹងបញ្ហាអ្វី​ឬ​ជន​ណាមួយដែលធ្វើឲ្យបាត់បង់កាតព្វកិច្ចជា​កម្មសិទ្ធិករឡើយ  បើផ្ទុយពីនេះ ខ្លួន ឬក្រុមហ៊ុន ហ៊ានទទួលខុសត្រូវចំពោះមុខច្បាប់ ។</p>
        <p class="tips">២.២- : ភាគី(ខ) សន្យាដោយស័្មគ្រចិត្តថា ធានាមិនចាត់វិធានការណាមួយ ដើម្បីអនុវត្តសិទ្ធិលើទ្រព្យ បង្កើតហ៊ីប៉ូតែក ដោយធ្វើការដោះដូរ ធ្វើ​      ​អំណោយ   លក់ ផ្ទេរ ដាក់ជាភាគហ៊ុន ដាក់បញ្ចាំ ដាក់ធានា  ឬ បង្កើតហ៊ីប៉ូតែកឲ្យជនណាផ្សេងទៀតឡើយ  ហើយសន្យាថា ថែរក្សា ជួសជុល គ្រប់គ្រងឲ្យបានគង់វង្សល្អ រហូតសងបំណុលរួច ទើបភាគី(ខ) មានសិទ្ធិពេញលេញលើកម្មសិទ្ធិឡើងវិញ ។</p>
        <p class="tips">២.៣- : ក្នុងករណីភាគី(ខ) ​ឫ អ្នកខី្ចប្រាក់ គ្មានលទ្ធភាពសងបំណុលតាម កិច្ចសន្យាខ្ចីប្រាក់ណាមួយ ភាគី(ខ) សុខ​ចិត្តយល់ព្រម​ឲ្យ​ភាគី(ក)​ប្តឹងទៅ​​អាជ្ញាធរ​សមត្ថកិច្ច ឬ តុលាការរឹបអូសទ្រព្យសម្បត្តិ ដែលបង្កើតហ៊ីប៉ូតែកលក់ឡៃឡុង ដើម្បីទូទាត់សងគ្រប់បំណុលទាំងអស់  រួម​​​​​​​​​ទាំ​ង​​​​​ប្រាក់ដើម ​ការ​​​​ប្រាក់  និង ប្រា​ក់​ពិន័យ ឲ្យបានគ្រប់ចំនួន  ។  ករណីទឹកប្រាក់ទូទាត់សង មិនទាន់គ្រប់ចំនួន  ភាគី(ខ) ត្រូវបន្ត​កាតព្វ​​កិច្ច សងបំណុល​ដែលនៅសល់បន្ថែមទៀត ​រហូតទាល់តែគ្រប់ចំនួន ។</p>
        <p class="tips">២.៤- : ក្នុងករណីទ្រព្យ ដែលបង្កើតហ៊ីប៉ូតែកត្រូវបានលក់ ឬ បង្កើតហ៊ីប៉ូតែកបំណុលផ្សេងទៀត ដោយប្រការណាមួយ ភាគី(ខ) យល់ព្រម​ទូទាត់​សង​គ្រប់​​បំណុលទាំងអស់ ដែលដាក់ហ៊ីប៉ូតែក/ដាក់បញ្ចាំដោយទ្រព្យសម្បត្តិនោះឲ្យភាគី(ក) រួមទាំងប្រាក់ដើម ការប្រាក់ និង ប្រាក់ពិន័យ​ទាំងអស់​ឲ្យបាន​គ្រប់ចំនួនដោយឥតលក្ខខណ្ឌ​ ។</p>
        <p class="tips">ប្រការ៣:  ​​​​អំពីលក្ខខណ្ឌអវសាន</p>
        <p class="tips">៣.១-:  កិច្ចសន្យាបង្កើតហ៊ីប៉ូតែកនេះ មានអនុភាពអនុវត្តបន្ត រហូតនៅពេលភាគី(ខ) ចុះកិច្ចសន្យាខ្ចីប្រាក់ ឬ កិច្ចសន្យាណាមួយ ដើម្បីប្រើប្រាស់ ឥណទានជាមួយភាគី(ក)បន្ត  ឬ បន្ថែមដែល​ពាក់ព័ន្ធជាមួយកិច្ចសន្យាបង្កើតហ៊ីប៉ូតែកនេះ ។</p>
        <p class="tips">៣.២-:  ភាគី(ក) និង ភាគី(ខ) ​សន្យាគោរពយ៉ាងម៉ឹងម៉ាត់​តាមរាល់ប្រការ​នៃ ​ខសន្យា​នានា​ខាងលើ ។ ក្នុងករណីមានការអនុវត្តន៍ផ្ទុយ‍ ឬ ដោយ​រំលោភលើ​លក្ខខណ្ឌណា​មួយ​នៃកិច្ចសន្យានេះ ភាគីដែលល្មើសត្រូវទទួល​ខុស​ត្រូវ​​​ចំពោះមុខច្បាប់​ជា​​ធរ​​មា​ន ។ រាល់សោហ៊ុយ​ចំណា​យ​​ក្នុង​​​ការដោះស្រាយលើវិវាទ ជាបន្ទុករបស់ភាគីរំលោភបំពានលើកិច្ចសន្យា ។ កិច្ចសន្យានេះមានការព្រមព្រៀងពិតប្រាកដ និងគ្មាន​ការ​បង្ខិត​ប​​ង្ខំ ហើយមានប្រសិទ្ធភាពចាប់ពីថ្ងៃចុះហត្ថលេខា និង ផ្តិតមេដៃនេះតទៅ ។</p>
        <p style="margin: 10px"><span>Signature</span> </p>
        <p style="margin: 0px 0px 20px 20px;"><span>Lender : <?php echo 'SAMRITHISAK MICROFINANCE LIMITED'?></span><span style="margin-left: 200px">Borrower : <?php echo $output['client_info']['display_name']?></span></p>

    </div>

</div>
