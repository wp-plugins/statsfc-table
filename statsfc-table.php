<?php
/*
Plugin Name: StatsFC Table
Plugin URI: https://statsfc.com/developers
Description: StatsFC League Table
Version: 1.0
Author: Will Woodward
Author URI: http://willjw.co.uk
License: GPL2
*/

/*  Copyright 2013  Will Woodward  (email : will@willjw.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('TABLE_ID',		'StatsFC_Table');
define('TABLE_NAME',	'StatsFC Table');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Table extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(TABLE_ID, TABLE_NAME, array('description' => 'StatsFC League Table'));
	}

	public function form($instance) {
		$defaults = array(
			'api_key'		=> __('', TABLE_ID),
			'type'			=> __('', TABLE_ID),
			'highlight'		=> __('', TABLE_ID),
			'default_css'	=> __('', TABLE_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$api_key		= strip_tags($instance['api_key']);
		$type			= strip_tags($instance['type']);
		$highlight		= strip_tags($instance['highlight']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('API key', TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
			</label>
		</p>
		<p>
			<?php _e('Type', TABLE_ID); ?>:
			<label><input name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="full"<?php echo ($type == 'full' ? ' checked' : ''); ?>> Full</label>
			<label><input name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="mini"<?php echo ($type == 'mini' ? ' checked' : ''); ?>> Mini</label>
		</p>
		<p>
			<label>
				<?php _e('Highlight', TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>" type="text" value="<?php echo esc_attr($highlight); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', TABLE_ID); ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
			</label>
		</p>
	<?php
	}

	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['type']			= strip_tags($new_instance['type']);
		$instance['highlight']		= strip_tags($new_instance['highlight']);
		$instance['default_css']	= strip_tags($new_instance['default_css']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
		extract($args);

		$api_key		= $instance['api_key'];
		$type			= $instance['type'];
		$highlight		= $instance['highlight'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		$data = file_get_contents('https://api.statsfc.com/premier-league/table.json?key=' . $api_key);

		if (empty($data)) {
			echo '<p class="statsfc_error">There was an error connecting to the StatsFC API</p>';
			return;
		}

		$json = json_decode($data);
		if (isset($json->error)) {
			echo '<p class="statsfc_error">' . esc_attr($json->error) . '</p>';
			return;
		}

		if ($default_css) {
			wp_register_style('prefix-css', plugins_url('c/all.css', __FILE__));
			wp_enqueue_style('prefix-css');
		}
		?>
		<div class="statsfc_table">
			<table>
				<thead>
					<tr>
						<th class="statsfc_numeric"></th>
						<th>Team</th>
						<th class="statsfc_numeric">P</th>
						<?php
						if ($type == 'full') {
						?>
							<th class="statsfc_numeric">W</th>
							<th class="statsfc_numeric">D</th>
							<th class="statsfc_numeric">L</th>
							<th class="statsfc_numeric">GF</th>
							<th class="statsfc_numeric">GA</th>
						<?php
						}
						?>
						<th class="statsfc_numeric">GD</th>
						<th class="statsfc_numeric">Pts</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($json as $row) {
						$classes = array();

						if (! empty($row->info)) {
							$classes[] = 'statsfc_' . esc_attr($row->info);
						}

						if (! empty($highlight) && $highlight == $row->team) {
							$classes[] = 'statsfc_highlight';
						}
						?>
						<tr<?php echo (! empty($classes) ? ' class="' . implode(' ', $classes) . '"' : ''); ?>>
							<td class="statsfc_numeric"><?php echo esc_attr($row->position); ?></td>
							<td class="statsfc_team statsfc_badge_<?php echo str_replace(' ', '', strtolower($row->team)); ?>"><?php echo esc_attr($type == 'full' ? $row->team : $row->teamshort); ?></td>
							<td class="statsfc_numeric"><?php echo esc_attr($row->played); ?></td>
							<?php
							if ($type == 'full') {
							?>
								<td class="statsfc_numeric"><?php echo esc_attr($row->won); ?></td>
								<td class="statsfc_numeric"><?php echo esc_attr($row->drawn); ?></td>
								<td class="statsfc_numeric"><?php echo esc_attr($row->lost); ?></td>
								<td class="statsfc_numeric"><?php echo esc_attr($row->for); ?></td>
								<td class="statsfc_numeric"><?php echo esc_attr($row->against); ?></td>
							<?php
							}
							?>
							<td class="statsfc_numeric"><?php echo esc_attr($row->for - $row->against); ?></td>
							<td class="statsfc_numeric"><?php echo esc_attr($row->points); ?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>

			<p class="statsfc_footer"><small>Powered by <a href="https://statsfc.com" target="_blank">StatsFC.com</a></small></p>
		</div>
		<?php
		echo $after_widget;
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . TABLE_ID . '");'));
?>