
<div class="container row">

    <div class="col-sm-8">
        <form action="" method="post" role="form" class="form form-horizontal">
            <input type="hidden" name="act" value="test_mysql">
            <input type="hidden" name="op" value="insertDataByTransaction">
            <input type="hidden" name="form_submit" value="ok">
            <table class="table table-bordered">
                <tr>
                    <td>Name</td>
                    <td>
                        <input class="form-control" type="text" name="name" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        Age
                    </td>
                    <td>
                        <input class="form-control" type="number" name="age">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" class="btn btn-danger" value="submit">
                    </td>
                </tr>
            </table>
        </form>
    </div>

</div>