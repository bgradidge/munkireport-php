<?php $this->view('partials/head'); ?>

<?php //Initialize models needed for the table
new Machine_model;
new Reportdata_model;
new Filevault_status_model;
new Localadmin_model;
new Security_model;
?>

<div class="container">

  <div class="row">

  	<div class="col-lg-12">

		  <h3>Security report <span id="total-count" class='label label-primary'>…</span></h3>

		  <table class="table table-striped table-condensed table-bordered">
		    <thead>
		      <tr>
		      	<th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
		        <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
		        <th data-i18n="listing.username" data-colname='reportdata.long_username'></th>
		        <th data-i18n="user.local_admins" data-colname='localadmin.users'></th>
		        <th data-i18n="filevault.users" data-colname='filevault_status.filevault_users'></th>
		        <th data-i18n="type"data-colname='machine.machine_name'></th>
                <th data-i18n="storage.encryption_status" data-colname='diskreport.CoreStorageEncrypted'></th>
                <th data-i18n="security.gatekeeper" data-colname='security.gatekeeper'></th>
                <th data-i18n="security.sip" data-colname='security.sip'></th>
		      </tr>
		    </thead>
		    <tbody>
		    	<tr>
					<td data-i18n="listing.loading" colspan="7" class="dataTables_empty"></td>
				</tr>
		    </tbody>
		  </table>
    </div> <!-- /span 12 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

	$(document).on('appUpdate', function(e){

		var oTable = $('.table').DataTable();
		oTable.ajax.reload();
		return;

	});

	$(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column 
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

	    oTable = $('.table').dataTable( {
            ajax: {
                url: "<?=url('datatables/data')?>",
                type: "POST",
                data: function(d){
                    // Look for a bigger/smaller/equal statement
                    if(d.search.value.match(/^encrypted = \d$/))
                    {
                        console.log(d.search.value)

                        // Add column specific search
                        d.columns[6].search.value = d.search.value.replace(/.*(\d)$/, '= $1');
                        // Clear global search
                        d.search.value = '';
                        console.log(d.columns[6].search.value)
                        //dumpj(d.columns[6].search.value)
                    }
                    
                    // Only search on bootvolume
                    d.where = [
                        {
                            table: 'diskreport',
                            column: 'MountPoint',
                            value: '/'
                        }
                    ];

                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
		    createdRow: function( nRow, aData, iDataIndex ) {
	        	// Update name in first column to link
	        	var name=$('td:eq(0)', nRow).html();
	        	if(name == ''){name = "No Name"};
	        	var sn=$('td:eq(1)', nRow).html();
	        	var link = get_client_detail_link(name, sn, '<?php echo url(); ?>/');
	        	$('td:eq(0)', nRow).html(link);
                var enc = $('td:eq(6)', nRow).html();
                $('td:eq(6)', nRow).html(function(){
                    if( enc == 1){
                        return '<span class="label label-success">'+i18n.t('encrypted')+'</span>';
                    }
                    return '<span class="label label-danger">'+i18n.t('unencrypted')+'</span>';
                });
		    }
	    } );
	} );
</script>

<?php $this->view('partials/foot'); ?>

