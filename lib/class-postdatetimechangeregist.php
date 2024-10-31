<?php
/**
 * Post Date Time Change
 *
 * @package    Post Date Time Change
 * @subpackage Post Date Time Change Regist registered in the database
/*
	Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$postdatetimechangeregist = new PostDateTimeChangeRegist();

/** ==================================================
 * Register Database
 */
class PostDateTimeChangeRegist {

	/** ==================================================
	 * Construct
	 *
	 * @since 5.00
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'update_notice' ) );

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 4.02
	 */
	public function register_settings() {

		/* Old option 5.06 -> New option 5.07 */
		if ( get_option( 'postdatetimechange_settings_' . get_current_user_id() ) ) {
			update_option( 'postdatetimechange', get_option( 'postdatetimechange_settings_' . get_current_user_id() ) );
			delete_option( 'postdatetimechange_settings_' . get_current_user_id() );
			update_user_option( get_current_user_id(), 'postdatetimechange', get_option( 'postdatetimechange' ) );
		}

		if ( ! get_option( 'postdatetimechange' ) ) {
			$postdatetimechange_tbl = array(
				'method' => 'posted',
				'write' => 'date_modified',
				'picker' => 1,
			);
			update_option( 'postdatetimechange', $postdatetimechange_tbl );
		}
		if ( ! get_user_option( 'postdatetimechange', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'postdatetimechange', get_option( 'postdatetimechange' ) );
		}

	}

	/** ==================================================
	 * Update notice
	 *
	 * @since 5.08
	 */
	public function update_notice() {

		$screen = get_current_screen();
		if ( is_object( $screen ) && 'dashboard' == $screen->id ||
				is_object( $screen ) && 'settings_page_postdatetimechange' == $screen->id ) {
			if ( class_exists( 'BulkDatetimeChange' ) ) {
				$bulkdatetimechange_url = admin_url( 'admin.php?page=bulkdatetimechange' );
			} else {
				if ( is_multisite() ) {
					$bulkdatetimechange_url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=bulk-datetime-change' );
				} else {
					$bulkdatetimechange_url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=bulk-datetime-change' );
				}
			}
			$bulkdatetimechange_link = '<strong><a style="text-decoration: none;" href="' . $bulkdatetimechange_url . '">Bulk Datetime Change</a></strong>';
			$bulk_html = '<div>' . __( 'Bulk change date/time for posts.', 'post-date-time-change' ) . ' : ' . $bulkdatetimechange_link . '</div>';
			$postdatetimechange_url = admin_url( 'options-general.php?page=postdatetimechange' );
			$postdatetimechange_link = '<strong><a style="text-decoration: none;" href="' . $postdatetimechange_url . '">Post Date Time Change</a></strong>';
			/* translators: Plugin Link */
			$post_html = '<div>' . sprintf( __( '%1$s will be closed eventually with no more maintenance. The following plugin is successor. Please switch.', 'post-date-time-change' ), $postdatetimechange_link ) . '</div>';
			?>
			<div class="notice notice-warning is-dismissible"><ul><li>
			<?php
			echo wp_kses_post( $post_html );
			echo wp_kses_post( $bulk_html );
			?>
			</li></ul></div>
			<?php
		}

	}

}


