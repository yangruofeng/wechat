/**
 * Created by tim on 6/4/2015.
 */
var ColDataType={
    varchar:"varchar",
    bool:'tinyint',
    datetime:'datetime',
    int:'int',
    decimal:'decimal',
    time:'time',
    float:'float',
    date:'date',
    timestamp:'timestamp',
    longblob:'longblob',
    text:'text',
    enums:'enum'
};
var ColEditorType={
    text:"text",
    checkbox:'checkbox',
    radio:'radio',
    file:'file',
    int:"int",
    number:'number',
    time:'time',
    textarea:'textarea',
    datetime:'datetime',
    combo:'combo',
    treeCombo:'treecombo',
    picker:'picker',
    img:'img',
    linker:'linker',
    hidden:'hidden'
};
var ColViewType={
    text:"text",
    int:'int',
    number:'number',
    percent:'percent',
    datetime:'datetime',
    checkbox:'checkbox',
    file:'file',
    select:'select',
    img:'img',
    linker:'linker',
    label:"label",
    password:"password",
    radio:"radio",
    hidden:'hidden'
};


/*********************************处理form***********************************/

/**************以后有时间了再设计Store的模式来处理数据集*******************/
/**************还要处理图片剪辑、文件上传的功能*******************/
/*************************日期处理函数***********************/
yo.dateAdd=function(_dateString,_days){
    var _d=new Date(Date.parse(_dateString));
    _d=_d.DateAdd("d",_days);
    _d=_d.DateFormat("yyyy-MM-dd");
    var _rt_str= _d.toString();

    return _rt_str;

};
function formatAmount(num){
    return (num.toFixed(2) + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
}
function formatAmountNoFix(num){
    return (num + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
}
