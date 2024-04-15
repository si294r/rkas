<?php defined( 'ABSPATH' ) || die(); // security access WordPress context only. ?>

<div class="wrap">
	<h2><?php echo esc_html__( 'Daftar RKAS - Rencana Kegiatan Dan Anggaran Sekolah', 'rkas' ); ?></h2>

	<table id="tbl-rkas" class="display">
		<thead>
			<tr>
				<th><?php echo esc_html__( 'Kegiatan', 'rkas' ); ?></th>
				<th><?php echo esc_html__( 'Kode Rekening', 'rkas' ); ?></th>
				<th><?php echo esc_html__( 'Urutan', 'rkas' ); ?></th>
				<th><?php echo esc_html__( 'Uraian Kegiatan', 'rkas' ); ?></th>
				<th><?php echo esc_html__( 'Harga Satuan', 'rkas' ); ?></th>
				<th><?php echo esc_html__( 'Satuan Item', 'rkas' ); ?></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<script type="text/javascript">

	jQuery(document).ready(function($){

	if ($('#tbl-rkas').length > 0) {
		//console.info('load datatable');
		var dt = $('#tbl-rkas').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: 'admin-ajax.php',
				type: 'POST',
				data: {
					action: 'rkas_action',
					action_type: 'list'
				},
				cache:false,
			},
			columns: [
				{ 
					data: 'kegiatan',
					render: function(data, type, row) {
						var html = data;
						html += '<div>';
						html += '<span class="dt-option dashicons dashicons-media-default" title="<?php echo esc_html__( 'Lihat Detail', 'rkas' ); ?>" onclick="dtOptionDetail(\''+row.rkas_id+'\')"></span>';
						html += '&nbsp;';
						html += '<span class="dt-option dashicons dashicons-edit" title="<?php echo esc_html__( 'Ubah', 'rkas' ); ?>" onclick="dtOptionEdit(\''+row.rkas_id+'\')"></span>';
						html += '&nbsp;';
						html += '<span class="dt-option dashicons dashicons-trash" title="<?php echo esc_html__( 'Hapus', 'rkas' ); ?>" onclick="dtOptionDelete(\''+row.rkas_id+'\')"></span>';
						html += '</div>';
						return html;
					}
				},
				{ data: 'kode_rekening' },
				{ data: 'urutan' },
				{ data: 'uraian_kegiatan' },
				{ data: 'harga_satuan' },
				{ data: 'satuan_item' },
			],
			drawCallback: function () {
				$('.dt-option').tooltip();
			}
		});
	}
	});

	function dtOptionDetail(rkas_id) {
		location.href = 'admin.php?page=rkas&page_type=view&rkas_id='+rkas_id
	}
	function dtOptionEdit(rkas_id) {
		location.href = 'admin.php?page=rkas&page_type=edit&rkas_id='+rkas_id
	}
	function dtOptionDelete(rkas_id) {
		var ids = jQuery('#tbl-rkas').dataTable().api().data().filter(function(item){ return item.rkas_id == rkas_id});
		// console.info(ids);
		var msgDelete = ids[0].kegiatan;
		jQuery.post('admin-ajax.php', { 
			action: 'rkas_action',
			action_type: 'delete_nonce'
		}).done(function(nonce){
			Swal.fire({
				// title: "Apakah Anda Yakin?",
				html: `<?php echo esc_html__( 'RKAS \'${msgDelete}\' akan dihapus! Apakah Anda Yakin?', 'rkas' ); ?>`,
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "<?php echo esc_html__( 'Ya, Hapus!', 'rkas' ); ?>",
				cancelButtonText: "<?php echo esc_html__( 'Batal', 'rkas' ); ?>"
			}).then((result) => {
				if (result.isConfirmed) {
					jQuery.post('admin-ajax.php', { 
						action: 'rkas_action',
						action_type: 'delete', 
						_wpnonce_rand: nonce._wpnonce_rand,
						_wpnonce_action: nonce._wpnonce_action,
						rkas_id: rkas_id
					}).done(function(data){
						//console.info(data);
						if (data.result > 0) {
							toast_show('success', `<?php echo esc_html__( 'RKAS \'${msgDelete}\' sudah dihapus.', 'rkas' ); ?>`);
							jQuery('#tbl-rkas').dataTable().api().draw();
						} else {
							toast_show('danger', '<?php echo esc_html__( 'Data gagal dihapus.', 'rkas' ); ?>');
						}
					}).fail(function(response){
						toast_show('danger', response.responseJSON.error);
					});
				}
			});
		});
	}

</script>
