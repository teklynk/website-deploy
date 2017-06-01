<?php
define('inc_access', TRUE);

include_once('includes/header.inc.php');

?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li><a href="index.php">Home</a></li>
            <li class="active">Deployment</li>
        </ol>
    </div>
</div>
<!--modal window-->
<div id="myModal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </a>
                <h4 class="modal-title">&nbsp;</h4>
            </div>
            <?php
            $sqlSite = mysqli_query($db_conn, "SELECT * FROM sites WHERE id=" . $_GET['id'] . " ");
            $rowSite = mysqli_fetch_array($sqlSite);
            $rowSiteName = $rowSite['name'];

            echo $ysmSitesDir . "/" . $rowSiteName;

            if (!empty($_POST)) {

                $searchArr = array(" ", "-", "'");
                $replaceArr = array("_", "_", "");

                $siteName = str_replace($searchArr, $replaceArr, safeCleanStr(strtolower($_POST['site_name'])));
                $custNumber = safeCleanStr(urlencode($_POST['cust_number']));
                $custSid = safeCleanStr(urlencode($_POST['cust_sid']));
                $formAction = strtolower(urlencode($_POST['form_action']));

                if (!empty($_POST['loc_id'])) {

                    //Edit
                    if ($rowSiteName != $siteName){
                        //update data on submit
                        $siteUpdate = "UPDATE sites SET customerid='" . $custNumber . "', name='" . $siteName . "', sid='" . $custSid . "', version='', date='" . date("Y-m-d H:i:s") . "' WHERE id=" . $_POST['loc_id'] . " ";
                        mysqli_query($db_conn, $siteUpdate);

                        renameWithNestedMkdir($ysmSitesDir . "/" . $rowSiteName, $ysmSitesDir . "/" . $siteName) OR die('Could not create directory');
                    }

                } elseif (!empty($_POST['delete_id'])) {

                    //Delete
                    //delete site
                    $siteDelete = "DELETE FROM sites WHERE id=" . $_POST['delete_id'] . " ";
                    mysqli_query($db_conn, $siteDelete);

                    //Archive/Move site

                } else {

                    //Add
                    //insert data on submit
                    $siteInsert = "INSERT INTO sites (customerid, name, sid, version, date) VALUES ('" . $custNumber . "', '" . $siteName . "', '" . $custSid . "', '', '" . date("Y-m-d H:i:s") . "')";
                    mysqli_query($db_conn, $siteInsert);

                    //Run Jenkins Build from URL
                    $jenkinsUrl = $buildServer."&form=".$formAction."&site_name=".$siteName."&cust_number=".$custNumber."&cust_sid=".$custSid;

                    $ch = curl_init($jenkinsUrl);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //$data = curl_exec($ch);
                    curl_close($ch);
                }

            }

            if ($_GET['form'] == 'delete'){
                $disableInputs = 'disabled';
            } else {
                $disableInputs = '';
            }
            ?>
            <div class="modal-body">
                <form name="addeditform" id="addeditform" method="post" action="index.php?">
                    <h1 class="page-header"><?php echo ucwords($_GET['form']); ?> Site</h1>
                    <div class="form-group">
                        <label>Customer Number</label>
                        <input class="form-control" <?php echo $disableInputs; ?> value="<?php echo $rowSite['customerid']; ?>" maxlength="100" placeholder="8675309" id="cust_number" name="cust_number" type="text" autocomplete="off" autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Customer SID</label>
                        <input class="form-control" <?php echo $disableInputs; ?> value="<?php echo $rowSite['sid']; ?>" maxlength="12" placeholder="L1D1" id="cust_sid" name="cust_sid" type="text" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label>Site Name</label>
                        <input class="form-control" <?php echo $disableInputs; ?> value="<?php echo $rowSite['name']; ?>" maxlength="100" placeholder="handleypl" id="site_name" name="site_name" type="text" autocomplete="off" required>
                    </div>
                    <?php

                    echo "<input type='hidden' id='form_action' name='form_action' value='".$_GET['form']."'/>";

                    if ($_GET['form'] == 'delete'){
                        echo "<input type='hidden' id='delete_id' name='delete_id' value='".$_GET['id']."'/>";
                        echo "<button class='btn btn-danger' type='submit' id='deletesubmit' name='deletesubmit'><i class='fa fa-trash'></i> Delete</button>";
                    } else {
                        echo  "<input type='hidden' id='loc_id' name='loc_id' value='".$_GET['id']."'/>";
                        echo "<button class='btn btn-primary' type='submit' id='addeditsubmit' name='addeditsubmit'><i class='fa fa-save'></i> Save</button>";
                    }
                    ?>
                    <button class="btn btn-default" id="cancel" data-dismiss="modal">Cancel</button>
                </form>
            </div>
            <div class="modal-footer">&nbsp;</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="page-header">Active Sites</h1>
            <button type="button" title="Add a New Site" class="btn btn-primary" id="add" onClick="window.location='index.php?add=true&form=add&id=';"><i class="fa fa-fw fa-plus"></i> Add a New Site</button>
            <br/><br/>
            <table class="table table-bordered table-hover table-striped table-responsive dataTable" id="dataTable">
                <thead>
                    <tr>
                        <th>Customer #</th>
                        <th>Site Name</th>
                        <th>SID</th>
                        <th class="no-sort">Admin URL</th>
                        <th>Version</th>
                        <th>Date</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sqlSiteList = mysqli_query($db_conn, "SELECT * FROM sites");
                    while ($rowSiteList = mysqli_fetch_array($sqlSiteList)) {

                        ?>
                        <tr>
                            <td><a href="<?php echo $customerLinkStr . $rowSiteList['customerid']; ?>" target="_blank"><?php echo $rowSiteList['customerid']; ?></a></td>
                            <td><?php echo $rowSiteList['name']; ?></td>
                            <td><?php echo $rowSiteList['sid']; ?></td>
                            <td><a href="<?php echo $ysmServer . '/' . $rowSiteList['name']; ?>/admin" target="_blank">Admin Link</a></td>
                            <td><?php echo $rowSiteList['version']; ?></td>
                            <td><?php echo $rowSiteList['date']; ?></td>
                            <td class="col-xs-2">
                                <button type="button" title="Edit" class="btn btn-primary" id="edit" onClick="window.location='index.php?edit=true&form=edit&id=<?php echo $rowSiteList['id']; ?>';"><i class='fa fa-fw fa-edit'></i></button>
                                <button type="button" title="Delete" class="btn btn-danger" id="delete" onClick="window.location='index.php?delete=true&form=delete&id=<?php echo $rowSiteList['id']; ?>';"><i class='fa fa-fw fa-trash'></i></button>
                            </td>
                        </tr>
                        <?php

                    }

                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .modal-sm {
        width: 25%;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').dataTable({
            "iDisplayLength": 25,
            "order": [[4, "desc"]],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }]
        });

        $('#myModal').on('hidden.bs.modal', function() {
            setTimeout(function(){
                window.location.href='index.php';
            }, 250);
        });

        var url = window.location.href;
        if (url.indexOf('?edit=true') != -1 || url.indexOf('?delete=true') != -1 || url.indexOf('?add=true') != -1 ) {
            setTimeout(function(){
                $('#myModal').modal('show');
            }, 250);
        }
    });
</script>
<?php
include_once('includes/footer.inc.php');
?>


