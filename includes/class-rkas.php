<?php
defined( 'ABSPATH' ) || die(); // security access WordPress context only.

/**
 *   Main Class RKAS
 *     - Design Pattern : WordPress event-driven, Singleton, Static Factory Method
 */
class RKAS {

	const VERSION                  = '1.0.0';
	const CLASS_DBNAME             = 'rkas';
	const PAGE_MAIN                = 'rkas';
	const PAGE_ADD                 = 'rkas_add';
	const PAGE_TYPE_LIST           = 'list';
	const PAGE_TYPE_ADD            = 'add';
	const PAGE_TYPE_EDIT           = 'edit';
	const PAGE_TYPE_VIEW           = 'view';
	const ACTION                   = 'rkas_action';
	const ACTION_TYPE_LIST         = 'list';
	const ACTION_TYPE_ADD          = 'add';
	const ACTION_TYPE_EDIT         = 'edit';
	const ACTION_TYPE_DELETE_NONCE = 'delete_nonce';
	const ACTION_TYPE_DELETE       = 'delete';

	private static $instance = null;
	private $wpdb;

	private function __construct() {
	}

	public static function plugin_factory() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_event() {
		add_action( 'init', array( $this, 'init_languages' ) );
		add_action( 'admin_menu', array( $this, 'init_menu' ) );
		// use admin_print_styles to print style before WordPress style.
		add_action( 'admin_print_styles', array( $this, 'print_head_styles' ) );
		// use admin_head to print script after WordPress script.
		add_action( 'admin_head', array( $this, 'print_head_scripts' ) );
		// register ajax action.
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'action' ) );
	}

	public function init_wpdb() {
		if ( ! isset( $this->wpdb ) ) {
			global $wpdb;
			$wpdb->select( self::CLASS_DBNAME );
			$this->wpdb = $wpdb;
		}
		return $this->wpdb;
	}

	public function init_languages() {
		load_plugin_textdomain( 'rkas', false, dirname( plugin_basename( __FILE__ ) ) . '../languages' );
	}

	public function init_menu() {
		// register plugin menu.
		add_menu_page(
			__( 'RKAS', 'rkas' ),
			__( 'RKAS', 'rkas' ),
			'manage_options',
			'rkas',
			array( $this, 'html_list' ),
			'dashicons-bank',
			41
		);
		add_submenu_page(
			'rkas',
			__( 'Daftar RKAS', 'rkas' ),
			__( 'Daftar RKAS', 'rkas' ),
			'manage_options',
			'rkas',
			array( $this, 'html_list' )
		);
		add_submenu_page(
			'rkas',
			__( 'Tambah RKAS', 'rkas' ),
			__( 'Tambah RKAS', 'rkas' ),
			'manage_options',
			'rkas_add',
			array( $this, 'html_add' )
		);
	}

	public function print_head_styles() {
		$page = wp_request( 'page' );
		if ( str_contains( $page, self::PAGE_MAIN ) === false ) {
			return;
		}
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		?>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
		<link rel="stylesheet" href="<?php echo esc_url( plugins_url( '../style.css', __FILE__ ) . '?ver=' . self::VERSION ); ?>">
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
	}

	public function print_head_scripts() {
		$page = wp_request( 'page' );
		if ( str_contains( $page, self::PAGE_MAIN ) === false ) {
			return;
		}
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
		?>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
		<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
		<script>
			<?php if ( get_locale() === 'id_ID' ) { ?>
				jQuery.extend(jQuery.fn.dataTable.defaults, {
					language: {
						url: '<?php echo esc_url( plugins_url( '../datatables-id.json', __FILE__ ) ); ?>'
					}
				});
			<?php } ?>
			jQuery.extend(jQuery, {
				locale: {
					loading: '<?php echo esc_js( __( 'Memuat...', 'rkas' ) ); ?>'
				}
			});
		</script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="<?php echo esc_url( plugins_url( '../script.js', __FILE__ ) . '?ver=' . self::VERSION ); ?>"></script>
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}

	public function html_list() {
		$page_type = wp_request( 'page_type' );
		if ( self::PAGE_TYPE_EDIT === $page_type ) {
			$action       = self::ACTION;
			$action_type  = self::ACTION_TYPE_EDIT;
			$nonce_rand   = wp_rand();
			$nonce_action = wp_create_nonce( $action . $action_type . $nonce_rand );

			$wpdb = $this->init_wpdb();
			$row  = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare( 'select * from rkas where rkas_id = %s', wp_request( 'rkas_id' ) ),
				ARRAY_A
			);

			include plugin_dir_path( __FILE__ ) . '../view/rkas-form.php';
		} elseif ( self::PAGE_TYPE_VIEW === $page_type ) {
			$action       = '';
			$action_type  = '';
			$nonce_rand   = '';
			$nonce_action = '';

			$wpdb = $this->init_wpdb();
			$row  = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare( 'select * from rkas where rkas_id = %s', wp_request( 'rkas_id' ) ),
				ARRAY_A
			);

			include plugin_dir_path( __FILE__ ) . '../view/rkas-form.php';
		} else {
			include plugin_dir_path( __FILE__ ) . '../view/rkas-list.php';
		}
	}

	public function html_add() {
		$page_type    = self::PAGE_TYPE_ADD;
		$action       = self::ACTION;
		$action_type  = self::ACTION_TYPE_ADD;
		$nonce_rand   = wp_rand();
		$nonce_action = wp_create_nonce( $action . $action_type . $nonce_rand );

		include plugin_dir_path( __FILE__ ) . '../view/rkas-form.php';
	}

	public function action() {
		$action_type = wp_request( 'action_type' );
		switch ( $action_type ) {
			case self::ACTION_TYPE_LIST:
				$this->action_list();
				break;
			case self::ACTION_TYPE_ADD:
				$this->action_add();
				break;
			case self::ACTION_TYPE_EDIT:
				$this->action_edit();
				break;
			case self::ACTION_TYPE_DELETE_NONCE:
				$this->action_delete_nonce();
				break;
			case self::ACTION_TYPE_DELETE:
				$this->action_delete();
				break;
		}
	}

	public function action_list() {
		$wpdb    = $this->init_wpdb();
		$start   = intval( wp_request( 'start' ) );
		$length  = intval( wp_request( 'length' ) );
		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'select rkas_id, kegiatan, kode_rekening, urutan, uraian_kegiatan, harga_satuan, satuan_item 
				from rkas 
				where 1=%d
				order by kegiatan
				limit %d, %d',
				1,
				$start,
				$length
			),
			ARRAY_A
		);
		$count   = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare( 'select count(*) from rkas where 1=%d', 1 )
		);

		$response                    = array();
		$response['draw']            = intval( wp_request( 'draw' ) );
		$response['data']            = ! empty( $results ) ? $results : array();
		$response['recordsFiltered'] = intval( $count );
		$response['recordsTotal']    = intval( $count );
		wp_send_json( $response );
	}

	public function get_nonce_context() {
		return wp_request( 'action' ) . wp_request( 'action_type' ) . wp_request( '_wpnonce_rand' );
	}

	public function action_add() {
		if ( wp_verify_nonce( wp_request( '_wpnonce_action' ), $this->get_nonce_context() ) ) {
			$wpdb   = $this->init_wpdb();
			$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				'rkas',
				array(
					'rkas_id'         => wp_generate_uuid4(),
					'kegiatan'        => wp_request( 'kegiatan' ),
					'kode_rekening'   => wp_request( 'kode_rekening' ),
					'urutan'          => wp_request( 'urutan' ),
					'uraian_kegiatan' => wp_request( 'uraian_kegiatan' ),
					'harga_satuan'    => wp_request( 'harga_satuan' ),
					'satuan_item'     => wp_request( 'satuan_item' ),
				)
			);
			wp_send_json( array( 'result' => $result ) );
		} else {
			wp_send_json( array( 'error' => __( 'Nonce tidak valid', 'rkas' ) ), 400 );
		}
	}

	public function action_edit() {
		if ( wp_verify_nonce( wp_request( '_wpnonce_action' ), $this->get_nonce_context() ) ) {
			$wpdb   = $this->init_wpdb();
			$result = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				'rkas',
				array(
					'kegiatan'        => wp_request( 'kegiatan' ),
					'kode_rekening'   => wp_request( 'kode_rekening' ),
					'urutan'          => wp_request( 'urutan' ),
					'uraian_kegiatan' => wp_request( 'uraian_kegiatan' ),
					'harga_satuan'    => wp_request( 'harga_satuan' ),
					'satuan_item'     => wp_request( 'satuan_item' ),
				),
				array( 'rkas_id' => wp_request( 'rkas_id' ) )
			);
			wp_send_json( array( 'result' => $result ) );
		} else {
			wp_send_json( array( 'error' => __( 'Nonce tidak valid', 'rkas' ) ), 400 );
		}
	}

	public function action_delete_nonce() {
		$action       = self::ACTION;
		$action_type  = self::ACTION_TYPE_DELETE;
		$nonce_rand   = wp_rand();
		$nonce_action = wp_create_nonce( $action . $action_type . $nonce_rand );
		wp_send_json(
			array(
				'_wpnonce_rand'   => $nonce_rand,
				'_wpnonce_action' => $nonce_action,
			)
		);
	}

	public function action_delete() {
		if ( wp_verify_nonce( wp_request( '_wpnonce_action' ), $this->get_nonce_context() ) ) {
			$wpdb   = $this->init_wpdb();
			$result = $wpdb->delete( 'rkas', array( 'rkas_id' => wp_request( 'rkas_id' ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			wp_send_json( array( 'result' => $result ) );
		} else {
			wp_send_json( array( 'error' => __( 'Nonce tidak valid', 'rkas' ) ), 400 );
		}
	}
}
