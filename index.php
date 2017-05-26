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

<div class="container">
    <div class="card">
        <div class="card-body">
            <?php
            if ($_GET['form'] == 'add' || $_GET['form'] == 'edit') {

                $sqlSite = mysqli_query($db_conn, "SELECT * FROM sites WHERE id=" . $_GET['id'] . " ");
                $rowSite = mysqli_fetch_array($sqlSite);

                if (!empty($_POST)) {

                    if ($_POST['loc_id'] <> ''){
                        //update data on submit
                        $siteUpdate = "UPDATE sites SET customerid='" . safeCleanStr($_POST['cust_number']) . "', name='" . safeCleanStr($_POST['site_name']) . "', date='" . date("Y-m-d H:i:s") . "' WHERE id=" . $_POST['loc_id'] . " ";
                        mysqli_query($db_conn, $siteUpdate);
                    } else {
                        //insert data on submit
                        $siteInsert = "INSERT INTO sites (customerid, name, version, date) VALUES ('" . safeCleanStr($_POST['cust_number']) . "', '" . safeCleanStr($_POST['site_name']) . "', '" . safeCleanStr('') . "', '" . date("Y-m-d H:i:s") . "')";
                        mysqli_query($db_conn, $siteInsert);
                    }

                }
                ?>
                <form id="addeditform" method="post" action="">
                    <h1 class='page-header'><?php echo ucwords($_GET['form']); ?> Site</h1>
                    <div class="form-group">
                        <label>Customer Number</label>
                        <input class="form-control" value="<?php echo $rowSite['customerid']; ?>" maxlength="100" placeholder="8675309" id="cust_number" name="cust_number" type="text" autocomplete="off" autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Site Name</label>
                        <input class="form-control" value="<?php echo $rowSite['name']; ?>" maxlength="100" placeholder="handleypl" id="site_name" name="site_name" type="text" autocomplete="off" required>
                    </div>
                    <input type="hidden" name="loc_id" value="<?php echo $rowSite['id']; ?>"/>
                    <button class="btn btn-lg btn-primary btn-block" type="submit" onclick="javascript:window.location.reload()" id="addeditsubmit" name="addeditsubmit">Save</button>
                </form>
                <?php
            } else {
                ?>
                <h1 class="page-header">Active Sites</h1>
                <button type="button" data-toggle="tooltip" title="Add a New Site" class="btn btn-primary" onclick="showMyModal('New Site', 'index.php?modal=true&form=add')"><i class="fa fa-fw fa-plus"></i> Add a New Site</button>
                <br/><br/>
                <table class="table table-bordered table-hover table-striped table-responsive dataTable" id="dataTable">
                    <thead>
                        <tr>
                            <th>Customer #</th>
                            <th>Site Name</th>
                            <th class="no-sort">Admin URL</th>
                            <th>Version</th>
                            <th>Date</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        $sqlSite = mysqli_query($db_conn, "SELECT * FROM sites");
                        while ($rowSite = mysqli_fetch_array($sqlSite)) {

                            ?>
                            <tr>
                                <td><a href="https://intranet.tlcdelivers.com/TLCWebLSN/customer.asp?Cust_ID=<?php echo $rowSite['customerid']; ?>" target="_blank"><?php echo $rowSite['customerid']; ?></a></td>
                                <td><?php echo $rowSite['name']; ?></td>
                                <td><a href="#" target="_blank">Admin Link</a></td>
                                <td><?php echo $rowSite['version']; ?></td>
                                <td><?php echo $rowSite['date']; ?></td>
                                <td class="col-xs-2">
                                    <button type="button" data-toggle="tooltip" title="Edit" class="btn btn-primary" onclick="showMyModal('Edit Site', 'index.php?modal=true&form=edit&id=<?php echo $rowSite['id']; ?>')"><i class='fa fa-fw fa-edit'></i></button>
                                    <button type="button" data-toggle="tooltip" title="Delete" class="btn btn-danger" onclick="window.location.href='index.php?deletetitle=<?php echo $rowSite['name']; ?>&id=<?php echo $rowSite['id']; ?>'"><i class='fa fa-fw fa-trash'></i></button>
                                </td>
                            </tr>
                            <?php

                        }

                        ?>

                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<!--modal window-->
<div class="modal fade" id="webslideDialog" class="modal hide fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </a>
                <h4 class="modal-title">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <iframe id="myModalFile" src="" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">&nbsp;</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
        $('#addeditsubmit').click(function() {
            setTimeout(function(){
                window.parent.location.reload();
            }, 500);
        });
        $('#webslideDialog').on('hidden.bs.modal', function() {
            setTimeout(function(){
                window.location.href='index.php';
            }, 500);
        });
    });
</script>
<?php
include_once('includes/footer.inc.php');
?>


