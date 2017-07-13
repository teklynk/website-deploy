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

<!--modal window with form-->
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

            $sqlSite = mysqli_query($db_conn, "SELECT * FROM sites WHERE id=".$_GET['id']." ");
            $rowSite = mysqli_fetch_array($sqlSite);

            if (!empty($_POST)) {

                $searchArr = array(" ", "-", "'");
                $replaceArr = array("_", "_", "");

                $siteName = str_replace($searchArr, $replaceArr, safeCleanStr(strtolower($_POST['site_name'])));
                $custNumber = safeCleanStr($_POST['cust_number']);
                $customerId = safeCleanStr($_POST['customer_id']);
                $custSid = safeCleanStr($_POST['cust_sid']);
                $customerSid = safeCleanStr($_POST['customer_sid']);
                $formAction = strtolower($_POST['form_action']);
                $rowSiteName = strtolower($_POST['row_site_name']);

                $sqlCheckSite = mysqli_query($db_conn, "SELECT * FROM sites WHERE name='" . $siteName . "' OR customerid='" . $custNumber . "' ");
                $rowCheckSite = mysqli_num_rows($sqlCheckSite);

                if ($rowCheckSite > 0) {
                    //redirect to error message
                    header("Location: index.php?error=edit&type=1");
                    echo "<script>window.location.href='index.php?error=edit&type=1';</script>";
                    die();
                }


                if (!empty($_POST['loc_id'])) {
                    //Edit
                    if ($rowSiteName != $siteName || $custNumber != $customerId || $custSid != $customerSid){
                        //update data on submit
                        $siteUpdate = "UPDATE sites SET customerid='" . $custNumber . "', name='" . $siteName . "', sid='" . $custSid . "', version='', date='" . date("Y-m-d H:i:s") . "' WHERE id=" . $_POST['loc_id'] . " ";
                        mysqli_query($db_conn, $siteUpdate);

                        //Rename the site folder
                        renameDir($ysmSitesDir . "/" . $rowSiteName, $ysmSitesDir . "/" . $siteName);
                    } else {
                        //redirect to error message
                        header("Location: index.php?error=edit&type=1");
                        echo "<script>window.location.href='index.php?error=edit&type=1';</script>";
                    }
                } elseif (!empty($_POST['delete_id'])) {
                    //Delete
                    if (file_exists($ysmSitesDir . "/" . $rowSiteName)){
                        //delete site from database
                        $siteDelete = "DELETE FROM sites WHERE id=" . $_POST['delete_id'] . " ";
                        mysqli_query($db_conn, $siteDelete);

                        //create a sql dump
                        $backupSQLFile = $ysmArchiveDir."/ysm_database_backup_".$customerId."_".date("Y-m-d").".sql";
                        $backupCreateFile = fopen($backupSQLFile, "w") or die("Unable to open file!");
                        fclose($backupCreateFile);

                        echo exec("mysqldump --user=".$db_username." --password=".$db_password." --host=".$db_servername." ysm_".$customerId." > ".$backupSQLFile.";");

                        sleep(1); //wait

                        //Create a zip in the archive folder
                        zipFile($ysmSitesDir . "/" . $rowSiteName, $ysmArchiveDir . "/ysm_site_backup_".$customerId."_".date("Y-m-d").".zip", true);

                        sleep(1); //wait

                        //Drop database
                        $dropSQLDB = "DROP DATABASE ysm_" . $customerId . " ";
                        mysqli_query($db_conn, $dropSQLDB);

                        //Delete the site folder
                        rrmdir($ysmSitesDir . "/" . $rowSiteName);
                    } else {
                        //redirect to error message
                        header("Location: index.php?error=delete&type=1");
                        echo "<script>window.location.href='index.php?error=delete&type=1';</script>";
                    }
                } elseif (!empty($_POST['backup_id'])) {
                    //Backup
                    if (file_exists($ysmSitesDir . "/" . $rowSiteName)){

                        //create a sql dump
                        $backupSQLFile = $ysmArchiveDir."/ysm_database_backup_".$customerId."_".date("Y-m-d").".sql";
                        $backupCreateFile = fopen($backupSQLFile, "w") or die("Unable to open file!");
                        fclose($backupCreateFile);

                        echo exec("mysqldump --user=".$db_username." --password=".$db_password." --host=".$db_servername." ysm_".$customerId." > ".$backupSQLFile.";");

                        sleep(1); //wait

                        //Create a zip in the archive folder
                        zipFile($ysmSitesDir . "/" . $rowSiteName, $ysmArchiveDir . "/ysm_site_backup_".$customerId."_".date("Y-m-d").".zip", true);

                    } else {
                        //redirect to error message
                        header("Location: index.php?error=delete&type=1");
                        echo "<script>window.location.href='index.php?error=delete&type=1';</script>";
                    }
                } else {
                    //Add
                    //insert data on submit
                    if ($rowSiteName != $siteName || $custNumber != $customerId || $custSid != $customerSid){
                        $siteInsert = "INSERT INTO sites (customerid, name, sid, version, date) VALUES ('" . $custNumber . "', '" . $siteName . "', '" . $custSid . "', '', '" . date("Y-m-d H:i:s") . "')";
                        mysqli_query($db_conn, $siteInsert);

                        //Run Jenkins Build from URL
                        $jenkinsUrl = $buildServer."&form=".$formAction."&site_name=".$siteName."&cust_number=".$custNumber."&cust_sid=".$custSid;

                        $ch = curl_init($jenkinsUrl);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($ch);
                        curl_close($ch);
                    } else {
                        //redirect to error message
                        header("Location: index.php?error=add&type=1");
                        echo "<script>window.location.href='index.php?error=add&type=1';</script>";
                    }
                }
            }

            if ($_GET['form'] == 'delete' || $_GET['form'] == 'backup'){
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

                    echo "<input type='hidden' id='row_site_name' name='row_site_name' value='".$rowSite['name']."'/>";
                    echo "<input type='hidden' id='form_action' name='form_action' value='".$_GET['form']."'/>";
                    echo "<input type='hidden' id='customer_id' name='customer_id' value='".$rowSite['customerid']."'/>";
                    echo "<input type='hidden' id='customer_sid' name='customer_sid' value='".$rowSite['sid']."'/>";

                    if ($_GET['form'] == 'delete') {
                        echo "<input type='hidden' id='delete_id' name='delete_id' value='".$_GET['id']."'/>";
                        echo "<button class='btn btn-danger' type='submit' id='deletesubmit' name='deletesubmit'><i class='fa fa-trash'></i> Delete</button>";
                    } elseif ($_GET['form'] == 'backup') {
                        echo  "<input type='hidden' id='backup_id' name='backup_id' value='".$_GET['id']."'/>";
                        echo "<button class='btn btn-primary' type='submit' id='addeditsubmit' name='addeditsubmit'><i class='fa fa-refresh'></i> Backup</button>";
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

<?php
//Set error messages
if ($_GET['error']=='delete' && $_GET['type'] == '1'){
    $pageMsg = "<div class='alert alert-danger'>Site directory does not exist.<button type='button' class='close' data-dismiss='alert' onclick=\"window.location.href='index.php'\">×</button></div>";
} elseif ($_GET['error']=='add' && $_GET['type'] == '1') {
    $pageMsg = "<div class='alert alert-danger'>Site name or customer id already exists.<button type='button' class='close' data-dismiss='alert' onclick=\"window.location.href='index.php'\">×</button></div>";
} elseif ($_GET['error']=='edit' && $_GET['type'] == '1') {
    $pageMsg = "<div class='alert alert-danger'>Site name or customer id already exists.<button type='button' class='close' data-dismiss='alert' onclick=\"window.location.href='index.php'\">×</button></div>";
}
?>

<!--main page container -->
<div class="container">
    <div class="card">
        <div class="card-body">
            <h1 class="page-header">Active Sites</h1>
            <?php echo $pageMsg; ?>
            <button type="button" title="Add a New Site" class="btn btn-primary" id="add" onClick="window.location='index.php?add=true&form=add&id=';"><i class="fa fa-fw fa-plus"></i> Add a New Site</button>
            <br/><br/>
            <table class="table table-bordered table-hover table-striped table-responsive dataTable" id="dataTable">
                <thead>
                    <tr>
                        <th>Customer #</th>
                        <th>Site Name</th>
                        <th>SID</th>
                        <th class="no-sort">Admin URL</th>
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
                            <td><a href="<?php echo $ysmServer . '/' . $rowSiteList['name']; ?>" target="_blank"><?php echo $rowSiteList['name']; ?></a></td>
                            <td><?php echo $rowSiteList['sid']; ?></td>
                            <td><a href="<?php echo $ysmServer . '/' . $rowSiteList['name']; ?>/admin" target="_blank">Admin Link</a></td>
                            <td><?php echo $rowSiteList['date']; ?></td>
                            <td class="col-xs-2">
                                <button type="button" title="Edit" class="btn btn-primary" id="edit" onClick="window.location='index.php?edit=true&form=edit&id=<?php echo $rowSiteList['id']; ?>';"><i class='fa fa-fw fa-edit'></i></button>
                                <button type="button" title="Backup" class="btn btn-default" id="backup" onClick="window.location='index.php?backup=true&form=backup&id=<?php echo $rowSiteList['id']; ?>';"><i class='fa fa-fw fa-refresh'></i></button>
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

<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').dataTable({
            "iDisplayLength": 25,
            "order": [[5, "desc"]],
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
        if (url.indexOf('?edit=true') != -1 || url.indexOf('?delete=true') != -1 || url.indexOf('?add=true') != -1 || url.indexOf('?backup=true') != -1) {
            setTimeout(function(){
                $('#myModal').modal('show');
            }, 250);
        }
    });
</script>
<?php
include_once('includes/footer.inc.php');
?>


