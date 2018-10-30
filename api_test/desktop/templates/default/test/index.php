<?php $doc = $output['doc']; ?>
<style>
    .required {
        color: red;
    }

    table {
        border-spacing: 0;
        border-collapse: collapse;
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }
    td,
    th {
        padding: 0;
    }

    table td,table th{
        padding: 5px 0;
    }



    .table-hover tr:hover{
        background-color: #ccc;
    }

    .pl20{
        padding-left: 20px;
    }

</style>
<p><a href="?act=doc&op=list#<?php echo $_GET['api'] ?>">Back To List</a></p>
<h1><?php echo $doc->name; ?></h1>
<h2>Description</h2>
<div><?php echo $doc->description; ?></div>
<hr/>
<h2>Url</h2>
<div><?php echo $doc->url; ?></div>
<hr />
<h2>Parameters</h2>
<div>
    <p><a href="?act=doc&op=list#<?php echo $_GET['api'] ?>">Back To List</a></p>
<table class="table-hover" action="<?php echo $doc->url; ?>" style="width: 100%" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th>Field</th>
        <th style="width: 80%" class="pl20">Description</th>
        <th>Required</th>
        <th>Sample Value</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($doc->parameters as $p) { ?>
        <tr>
            <td><?php echo $p->name; ?></td>
            <td class="pl20"><?php echo $p->description; ?></td>
            <td class="<?php echo $p->required ? "required" : ""; ?>"><?php echo $p->required ? "是" : "否"; ?></td>
            <td><input type="text" name="<?php echo $p->name; ?>" value="<?php echo $p->sample_value; ?>" /></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td><input type="button" name="test" value="Test" /></td>
    </tr>
    <tr>
        <td>Url</td>
        <td name="url" colspan="3"></td>
    </tr>
    <tr>
        <td>Response</td>
        <td name="response" colspan="3"></td>
    </tr>
    </tfoot>
</table>
    <p><a href="?act=doc&op=list#<?php echo $_GET['api'] ?>">Back To List</a></p>
</div>
<hr />
<h2>Return</h2>
<?php if (!$doc->return) { ?>
    <div>无</div>
<?php } else { ?>
    <table>
        <thead>
        <tr>
            <th>Field</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php
        function renderJsonStructArray($arr, $level)
        {
            foreach ($arr as $k => $v) {
                if (preg_match('/^\@\w+$/', $k)) continue;
                $k = str_replace('@@', '@', $k);
                ?>

                <tr>
                    <td><?php echo str_repeat('&nbsp;&nbsp;', $level) . $k ?></td>
                    <td><?php echo is_array($v) ? $v['@description'] : $v ?></td>
                </tr>

                <?php
                if (is_array($v)) {
                    renderJsonStructArray($v, $level + 1);
                }
            }
        }
        renderJsonStructArray($doc->return, 0);
        ?>
        </tbody>
    </table>
<?php } ?>
<hr/>
<h2>Test Cases</h2>

<script type="text/javascript" src="<?php echo CURRENT_RESOURCE_SITE_URL; ?>/js/test.js?1"></script>