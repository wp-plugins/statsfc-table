<?php
/*
Plugin Name: StatsFC Table
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC League Table
Version: 1.6.1
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
	public $isShortcode = false;

	private static $defaults = array(
		'title'			=> '',
		'key'			=> '',
		'competition'	=> '',
		'date'			=> '',
		'type'			=> 'full',
		'highlight'		=> '',
		'show_form'		=> '',
		'default_css'	=> ''
	);

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
		$instance		= wp_parse_args((array) $instance, self::$defaults);
		$title			= strip_tags($instance['title']);
		$key			= strip_tags($instance['key']);
		$competition	= strip_tags($instance['competition']);
		$date			= strip_tags($instance['date']);
		$type			= strip_tags($instance['type']);
		$highlight		= strip_tags($instance['highlight']);
		$show_form		= strip_tags($instance['show_form']);
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
				<?php _e('Key', STATSFC_TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Competition', STATSFC_TABLE_ID); ?>:
				<?php
				try {
					$data = $this->_fetchData('https://api.statsfc.com/crowdscores/competitions.php?type=League');

					if (empty($data)) {
						throw new Exception;
					}

					$json = json_decode($data);

					if (isset($json->error)) {
						throw new Exception;
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('competition'); ?>">
						<option></option>
						<?php
						foreach ($json as $comp) {
							echo '<option value="' . esc_attr($comp->key) . '"' . ($comp->key == $competition ? ' selected' : '') . '>' . esc_attr($comp->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('competition'); ?>" type="text" value="<?php echo esc_attr($competition); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Date (YYYY-MM-DD)', STATSFC_TABLE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo esc_attr($date); ?>" placeholder="YYYY-MM-DD">
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
				<input class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>" type="text" value="<?php echo esc_attr($highlight); ?>" placeholder="E.g., Liverpool, Swansea City">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Show team form?', STATSFC_TABLE_ID); ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('show_form'); ?>"<?php echo ($show_form == 'on' ? ' checked' : ''); ?>>
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
		$instance['key']			= strip_tags($new_instance['key']);
		$instance['competition']	= strip_tags($new_instance['competition']);
		$instance['date']			= strip_tags($new_instance['date']);
		$instance['type']			= strip_tags($new_instance['type']);
		$instance['highlight']		= strip_tags($new_instance['highlight']);
		$instance['show_form']		= strip_tags($new_instance['show_form']);
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
		$key			= $instance['key'];
		$competition	= $instance['competition'];
		$date			= $instance['date'];
		$type			= $instance['type'];
		$highlight		= $instance['highlight'];
		$show_form		= $instance['show_form'];
		$default_css	= $instance['default_css'];

		$html  = $before_widget;
		$html .= $before_title . $title . $after_title;

		try {
			$data = $this->_fetchData('https://api.statsfc.com/crowdscores/table.php?key=' . urlencode($key) . '&competition=' . urlencode($competition) . '&date=' . urlencode($date));

			if (empty($data)) {
				throw new Exception('There was an error connecting to StatsFC.com');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			if ($default_css) {
				wp_register_style(STATSFC_TABLE_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_TABLE_ID . '-css');
			}

			$won		= '';
			$drawn		= '';
			$lost		= '';
			$for		= '';
			$against	= '';
			$form		= '';

			if ($type == 'full') {
				$won		= '<th class="statsfc_numeric">W</th>';
				$drawn		= '<th class="statsfc_numeric">D</th>';
				$lost		= '<th class="statsfc_numeric">L</th>';
				$for		= '<th class="statsfc_numeric">GF</th>';
				$against	= '<th class="statsfc_numeric">GA</th>';
			}

			if ($show_form) {
				$form = '<th>Form</td>';
			}

			$html .= <<< HTML
			<div class="statsfc_table">
				<table>
					<thead>
						<tr>
							<th class="statsfc_numeric"></th>
							<th>Team</th>
							<th class="statsfc_numeric">P</th>
							{$won}
							{$drawn}
							{$lost}
							{$for}
							{$against}
							<th class="statsfc_numeric">GD</th>
							<th class="statsfc_numeric">Pts</th>
							{$form}
						</tr>
					</thead>
					<tbody>
HTML;

			foreach ($json->table as $row) {
				$classes = array();

				if (! empty($row->info)) {
					$classes[] = 'statsfc_' . esc_attr($row->info);
				}

				if (! empty($highlight) && $highlight == $row->team) {
					$classes[] = 'statsfc_highlight';
				}

				$class		= (! empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '');
				$position	= esc_attr($row->pos);
				$badge		= ($default_css ? ' style="background-image: url(//api.statsfc.com/kit/' . esc_attr($row->path) . '.png);"' : '');
				$team		= esc_attr($row->team);
				$played		= esc_attr($row->p);
				$won		= '';
				$drawn		= '';
				$lost		= '';
				$for		= '';
				$against	= '';
				$difference	= esc_attr($row->gf - $row->ga);
				$points		= esc_attr($row->pts);
				$form		= '';

				if ($type == 'full') {
					$won		= '<td class="statsfc_numeric">' . esc_attr($row->w) . '</td>';
					$drawn		= '<td class="statsfc_numeric">' . esc_attr($row->d) . '</td>';
					$lost		= '<td class="statsfc_numeric">' . esc_attr($row->l) . '</td>';
					$for		= '<td class="statsfc_numeric">' . esc_attr($row->gf) . '</td>';
					$against	= '<td class="statsfc_numeric">' . esc_attr($row->ga) . '</td>';
				}

				if ($show_form) {
					$form .= '<td class="statsfc_form">';

					foreach ($row->form as $result) {
						$form .= '<span class="statsfc_form statsfc_' . $result . '">&nbsp;</span>';
					}

					$form .= '</td>';
				}

				$html .= <<< HTML
				<tr{$class}>
					<td class="statsfc_numeric">{$pos}</td>
					<td class="statsfc_team"{$badge}>{$team}</td>
					<td class="statsfc_numeric">{$played}</td>
					{$won}
					{$drawn}
					{$lost}
					{$for}
					{$against}
					<td class="statsfc_numeric">{$difference}</td>
					<td class="statsfc_numeric">{$points}</td>
					{$form}
				</tr>
HTML;
			}

			$html .= <<< HTML
					</tbody>
				</table>

				<p class="statsfc_footer"><small>Powered by StatsFC.com. Fan data via CrowdScores.com</small></p>
			</div>
HTML;
		} catch (Exception $e) {
			$html .= '<p style="text-align: center;">StatsFC.com – ' . esc_attr($e->getMessage()) . '</p>' . PHP_EOL;
		}

		$html .= $after_widget;

		if ($this->isShortcode) {
			return $html;
		} else {
			echo $html;
		}
	}

	private function _fetchData($url) {
		if (function_exists('curl_exec')) {
			return $this->_curlRequest($url);
		} else {
			return $this->_fopenRequest($url);
		}
	}

	private function _curlRequest($url) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 5,
			CURLOPT_URL				=> $url
		));

		$data = curl_exec($ch);
		if (empty($data)) {
			$data = $this->_fopenRequest($url);
		}

		curl_close($ch);

		return $data;
	}

	private function _fopenRequest($url) {
		return file_get_contents($url);
	}

	public static function shortcode($atts) {
		$args = shortcode_atts(self::$defaults, $atts);

		$widget					= new self;
		$widget->isShortcode	= true;

		return $widget->widget(array(), $args);
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_TABLE_ID . '");'));
add_shortcode('statsfc-table', STATSFC_TABLE_ID . '::shortcode');
