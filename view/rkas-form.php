<?php defined( 'ABSPATH' ) || die(); // security access WordPress context only. ?>

<div class="wrap">
	<?php if ( 'edit' === $page_type ) { ?>
		<h2><?php echo esc_html__( 'Ubah RKAS - Rencana Kegiatan Dan Anggaran Sekolah', 'rkas' ); ?></h2>
	<?php } elseif ( 'view' === $page_type ) { ?>
		<h2><?php echo esc_html__( 'Data RKAS - Rencana Kegiatan Dan Anggaran Sekolah', 'rkas' ); ?></h2>
	<?php } else { ?>
		<h2><?php echo esc_html__( 'Tambah RKAS - Rencana Kegiatan Dan Anggaran Sekolah', 'rkas' ); ?></h2>
	<?php } ?>

	<form method="post" action="admin-post.php" name="frmRkas" class="validate" novalidate="novalidate">
		<input type="hidden" name="action" value="<?php echo esc_html( $action ); ?>" />
		<input type="hidden" name="action_type" value="<?php echo esc_html( $action_type ); ?>" />
		<input type="hidden" name="rkas_id" value="" />
		<input type="hidden" name="_wpnonce_rand" value="<?php echo esc_html( $nonce_rand ); ?>" />
		<input type="hidden" name="_wpnonce_action" value="<?php echo esc_html( $nonce_action ); ?>" />
		<table class="form-table" role="presentation">
		<tr class="form-field form-required">
			<th scope="row"><label for="nama"><?php echo esc_html__( 'Kegiatan', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="kegiatan" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="last_sync"><?php echo esc_html__( 'Kode Rekening', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="kode_rekening" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="kepsek"><?php echo esc_html__( 'Urutan', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="urutan" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="akreditasi"><?php echo esc_html__( 'Uraian Kegiatan', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="uraian_kegiatan" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="kurikulum"><?php echo esc_html__( 'Harga Satuan', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="harga_satuan" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="kurikulum"><?php echo esc_html__( 'Satuan Item', 'rkas' ); ?> <span class="description"><?php echo esc_html__( '(wajib)', 'rkas' ); ?></span></label></th>
			<td><input name="satuan_item" value="" type="text" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" /></td>
		</tr>
		</table>

		</table>

		<p>
			<?php if ( 'edit' === $page_type ) { ?>
				<button type="button" name="btn-save" class="btn btn-primary btn-sm" onclick="btnSaveClick(this)"><?php echo esc_html__( 'Simpan', 'rkas' ); ?></button>
				<button type="button" name="btn-cancel" class="btn btn-secondary btn-sm" onclick="btnCancelClick()"><?php echo esc_html__( 'Batal', 'rkas' ); ?></button>
			<?php } elseif ( 'view' === $page_type ) { ?>
				<button type="button" name="btn-cancel" class="btn btn-secondary btn-sm" onclick="btnCancelClick()"><?php echo esc_html__( 'Kembali', 'rkas' ); ?></button>
			<?php } else { ?>
				<button type="button" name="btn-submit" class="btn btn-primary btn-sm" onclick="btnSubmitClick(this)"><?php echo esc_html__( 'Submit', 'rkas' ); ?></button>
			<?php } ?>
		</p>
	</form>

</div>
<script type="text/javascript">

	var page_type = '<?php echo esc_html( $page_type ); ?>';

	jQuery(document).ready(function($){
		if (page_type == 'add') {
			// sample data here ...
		}
		if (page_type == 'edit' || page_type == 'view') {
			<?php
			if ( isset( $row ) ) {
				echo 'var row = ' . wp_json_encode( $row ) . ';';}
			?>
		}
		if (typeof row == 'object') {
			$('input[name="rkas_id"]').val(row.rkas_id);
			$('input[name="kegiatan"]').val(row.kegiatan);
			$('input[name="kode_rekening"]').val(row.kode_rekening);
			$('input[name="urutan"]').val(row.urutan);
			$('input[name="uraian_kegiatan"]').val(row.uraian_kegiatan);
			$('input[name="harga_satuan"]').val(row.harga_satuan);
			$('input[name="satuan_item"]').val(row.satuan_item);
		}
		if (page_type == 'view') {
			$('.form-field td input').attr('readonly', true);
		}
	});

	function btnSubmitClick(btn) {
		spinner_btn_show(btn);
		var postData = new FormData(jQuery('form[name="frmRkas"]')[0]);
		jQuery.post({
			url: 'admin-ajax.php',
			data: postData,
			processData: false,
			contentType: false
		}).done(function(data){
			//console.info(data);
			if (data.result > 0) {
				Swal.fire({
					text: "<?php echo esc_html__( 'Data berhasil disimpan.', 'rkas' ); ?>",
					icon: "success"
				}).finally(() =>{
					location.href = 'admin.php?page=rkas';
				});
			} else {
				toast_show('danger', '<?php echo esc_html__( 'Data gagal disubmit.', 'rkas' ); ?>');
			}
		}).fail(function(response){
			toast_show('danger', response.responseJSON.error);
		}).always(function(){
			spinner_btn_hide(btn);
		});
	}

	function btnSaveClick(btn) {
		spinner_btn_show(btn);
		var postData = new FormData(jQuery('form[name="frmRkas"]')[0]);
		jQuery.post({
			url: 'admin-ajax.php',
			data: postData,
			processData: false,
			contentType: false
		}).done(function(data){
			//console.info(data);
			if (data.result > 0) {
				Swal.fire({
					text: "<?php echo esc_html__( 'Data berhasil disimpan.', 'rkas' ); ?>",
					icon: "success"
				}).finally(() =>{
					location.href = 'admin.php?page=rkas';
				});
			} else {
				toast_show('warning', '<?php echo esc_html__( 'Tidak ada perubahan data.', 'rkas' ); ?>');
			}
		}).fail(function(response){
			toast_show('danger', response.responseJSON.error);
		}).always(function(){
			spinner_btn_hide(btn);
		});
	}

	function btnCancelClick() {
		location.href = 'admin.php?page=rkas';
	}

</script>
