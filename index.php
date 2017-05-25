<?php
include_once('includes/header.inc.php');
?>
<div class="row">
    <div class="col-lg-12">
        <ol class='breadcrumb'>
            <li><a href='index.php'>Home</a></li>
            <li class='active'>Deployment</li>
        </ol>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <?php
            if ($_GET['form'] == 'add' || $_GET['form'] == 'edit') {
                if ($_GET['form'] == 'edit'){
                    //Get values from database
                } else {
                    //Values = post values
                }

                //Check if customer number or name already exist before doing an update or insert
                ?>
                <form id="form-addeditsite" action="">
                    <h1 class='page-header'><?php echo ucwords($_GET['form']);?> Site</h1>
                    <div class="form-group">
                        <label>Customer Number</label>
                        <input class="form-control" value="" maxlength="100" placeholder="8675309" id="cust_number" name="cust_number" type="text" autocomplete="off" autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Site Name</label>
                        <input class="form-control" value="" maxlength="100" placeholder="handleypl" id="site_name" name="site_name" type="text" autocomplete="off" required>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit" onclick="javascript:window.location.reload()" id="addeditform">Save</button>
                </form>
                <?php
            } else {
                ?>
                <h1 class='page-header'>Active Sites</h1>
                <button type='button' data-toggle='tooltip' title='Add a New Site' class='btn btn-primary' onclick="showMyModal('New Site', 'index.php?modal=true&form=add')"><i class='fa fa-fw fa-plus'></i> Add a New Site</button>
                <br/><br/>
                <table class="table table-bordered table-hover table-striped table-responsive dataTable" id="dataTable">
                    <thead>
                    <tr>
                        <th>Customer #</th>
                        <th>Site Name</th>
                        <th>URL</th>
                        <th>Version</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><a href="https://intranet.tlcdelivers.com/TLCWebLSN/customer.asp?Cust_ID=230251" target="_blank">Print Services</a></td>
                        <td>Print Services</td>
                        <td><a href='#' title='Edit'>Print Services</a></td>
                        <td>Print Services</td>
                        <td class='col-xs-2'>
                            <button type='button' data-toggle='tooltip' title='Edit' class='btn btn-primary' onclick="showMyModal('Edit Site', 'index.php?modal=true&form=edit')"><i class='fa fa-fw fa-edit'></i></button>
                            <button type='button' data-toggle='tooltip' title='Delete' class='btn btn-danger' onclick="window.location.href='index.php?loc_id=1=54&deletetitle=Print Services'"><i class='fa fa-fw fa-trash'></i></button>
                        </td>
                    </tr>
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
            "order": [[0, "desc"]],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }]
        });
        $('#addeditform').click(function() {
            window.parent.location.reload();
        });
        $('#webslideDialog').on('hidden.bs.modal', function () {
            //window.location.reload(true);
            window.location.href='index.php';
        });
    });
</script>
<?php
include_once('includes/footer.inc.php');
?>


