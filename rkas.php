<?php
/**
 * @package Rencana Kegiatan Dan Anggaran Sekolah
 * @version 1.0.0
 *
 * Plugin Name: Rencana Kegiatan Dan Anggaran Sekolah
 * Plugin URI: https://lebihcerdas.id/
 * Description: RKAS - Rencana Kegiatan Dan Anggaran Sekolah Plugin
 * Author: Lebih Cerdas
 * Version: 1.0.0
 * Author URI: https://lebihcerdas.id/
 */

defined( 'ABSPATH' ) || die(); // security access WordPress context only.

require 'includes/function.php';
require 'includes/class-rkas.php';

RKAS::plugin_factory()->register_event();
