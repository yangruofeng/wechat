<div>
    <button class="btn btn-default" onclick="btn_test_alert();">
        Alert Info
    </button>
    <button class="btn btn-default" onclick="btn_test_alert2();">
        Alert  Success
    </button>
    <button class="btn btn-default" onclick="btn_test_alert3();">
        Alert  Error
    </button>
    <button class="btn btn-default" onclick="btn_test_alert4();">
        Alert  With Callback
    </button>
    <button class="btn btn-default" onclick="btn_test_prompt();">
        prompt
    </button>
    <button class="btn btn-default" onclick="btn_test_confirm();">
        confirm
    </button>


</div>
<script>
    function btn_test_alert(){
        alert("Test Message");
    }
    function btn_test_alert2(){
        alert("Test Message",1);
    }
    function btn_test_alert3(){
        alert("Test Message",2);
    }
    function btn_test_alert4(){
        alert("Test Message",2,function(){
            console.log("call back");
        });
    }
    function btn_test_prompt(){
        yo.dialog.prompt("TEST","Input Comment",function(_value){
            console.log(_value);
        });
    }
    function btn_test_confirm(){
        yo.confirm("AAA","are you sure to delete",function(_r){
            console.log(_r);
        })
    }
</script>