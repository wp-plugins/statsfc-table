<?php
/*
Plugin Name: StatsFC Table
Plugin URI: https://statsfc.com/developers
Description: StatsFC League Table
Version: 1.0.4
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

define('STATSFC_TABLE_ID',		'StatsFC_Table');
define('STATSFC_TABLE_NAME',	'StatsFC Table');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Table extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(STATSFC_TABLE_ID, STATSFC_TABLE_NAME, array('description' => 'StatsFC League Table'));
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$defaults = array(
			'title'			=> __('League Table', STATSFC_TABLE_ID),
			'api_key'		=> __('', STATSFC_TABLE_ID),
			'type'			=> __('', STATSFC_TABLE_ID),
			'highlight'		=> __('', STATSFC_TABLE_ID),
			'default_css'	=> __('', STATSFC_TABLE_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$type			= strip_tags($instance['type']);
		$highlight		= strip_tags($instance['highlight']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('Title', STATSFC_TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('API key', STATSFC_TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
			</label>
		</p>
		<p>
			<?php _e('Type', STATSFC_TABLE_ID); ?>:
			<label><input name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="full"<?php echo ($type == 'full' ? ' checked' : ''); ?>> Full</label>
			<label><input name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="mini"<?php echo ($type == 'mini' ? ' checked' : ''); ?>> Mini</label>
		</p>
		<p>
			<label>
				<?php _e('Highlight', STATSFC_TABLE_ID); ?>:
				<?php
				$data = file_get_contents('https://api.statsfc.com/premier-league/teams.json?key=' . (! empty($api_key) ? $api_key : 'free'));

				try {
					if (empty($data)) {
						throw new Exception('There was an error connecting to the StatsFC API');
					}

					$json = json_decode($data);
					if (isset($json->error)) {
						throw new Exception($json->error);
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>">
						<option></option>
						<?php
						foreach ($json as $team) {
							echo '<option value="' . esc_attr($team->name) . '"' . ($team->name == $highlight ? ' selected' : '') . '>' . esc_attr($team->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>" type="text" value="<?php echo esc_attr($highlight); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', STATSFC_TABLE_ID); ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
			</label>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['title']			= strip_tags($new_instance['title']);
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

		$title			= apply_filters('widget_title', $instance['title']);
		$api_key		= $instance['api_key'];
		$type			= $instance['type'];
		$highlight		= $instance['highlight'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		$data = file_get_contents('https://api.statsfc.com/premier-league/table.json?key=' . $api_key);

		try {
			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);
			if (isset($json->error)) {
				throw new Exception($json->error);
				return;
			}

			if ($default_css) {
				wp_register_style(STATSFC_TABLE_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_TABLE_ID . '-css');
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

				<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
			</div>
		<?php
		} catch (Exception $e) {
			echo '<p class="statsfc_error">' . esc_attr($e->getMessage()) .'</p>' . PHP_EOL;
		}

		echo $after_widget;
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_TABLE_ID . '");'));
?>