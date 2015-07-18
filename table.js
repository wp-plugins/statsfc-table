function StatsFC_Table(key) {
	this.domain			= 'https://api.statsfc.com';
	this.referer		= '';
	this.key			= key;
	this.competition	= 'EPL';
	this.group			= '';
	this.rows			= 0;
	this.date			= '';
	this.tableType		= 'full';
	this.highlight		= '';
	this.showBadges		= false;
	this.showForm		= false;

	var $j = jQuery;

	this.display = function(placeholder) {
		if (placeholder.length == 0) {
			return;
		}

		var $placeholder = $j('#' + placeholder);

		if ($placeholder.length == 0) {
			return;
		}

		if (this.referer == null || this.referer.length == 0) {
			this.referer = window.location.hostname;
		}

		var $container = $j('<div>').addClass('sfc_table');

		// Store globals variables here so we can use it later.
		var domain		= this.domain;
		var tableType	= this.tableType;
		var highlight	= this.highlight;
		var showBadges	= (this.showBadges === true || this.showBadges === 'true');
		var showForm	= (this.showForm === true || this.showForm === 'true');

		$j.getJSON(
			domain + '/crowdscores/table.php?callback=?',
			{
				key:			this.key,
				domain:			this.referer,
				competition:	this.competition,
				group:			this.group,
				highlight:		this.highlight,
				rows:			this.rows,
				date:			this.date
			},
			function(data) {
				if (data.error) {
					$container.append(
						$j('<p>').css('text-align', 'center').append(
							$j('<a>').attr({ href: 'https://statsfc.com', title: 'Football widgets and API', target: '_blank' }).text('StatsFC.com'),
							' – ',
							data.error
						)
					);

					return;
				}

				var $table = $j('<table>');
				var $thead = $j('<thead>');
				var $tbody = $j('<tbody>');

				var $position = $j('<th>').addClass('sfc_numeric').append(
					$j('<abbr>').attr('title', 'Position').text('Pos')
				);

				var $team = $j('<th>').text('Team');

				if (showBadges) {
					$team.addClass('sfc_team');
				}

				var $played = $j('<th>').addClass('sfc_numeric').append(
					$j('<abbr>').attr('title', 'Matches played').text('P')
				);

				var $goal_difference = $j('<th>').addClass('sfc_numeric').append(
					$j('<abbr>').attr('title', 'Goal difference').text('GD')
				);

				var $points = $j('<th>').addClass('sfc_numeric').append(
					$j('<abbr>').attr('title', 'Points').text('Pts')
				);

				if (tableType == 'full') {
					$thead.append(
						$j('<tr>').append(
							$position,
							$team,
							$played,
							$j('<th>').addClass('sfc_numeric').append(
								$j('<abbr>').attr('title', 'Matches won').text('W')
							),
							$j('<th>').addClass('sfc_numeric').append(
								$j('<abbr>').attr('title', 'Matches drawn').text('D')
							),
							$j('<th>').addClass('sfc_numeric').append(
								$j('<abbr>').attr('title', 'Matches lost').text('L')
							),
							$j('<th>').addClass('sfc_numeric').append(
								$j('<abbr>').attr('title', 'Goals for').text('F')
							),
							$j('<th>').addClass('sfc_numeric').append(
								$j('<abbr>').attr('title', 'Goals against').text('A')
							),
							$goal_difference,
							$points
						)
					);
				} else {
					$thead.append(
						$j('<tr>').append(
							$position,
							$team,
							$played,
							$goal_difference,
							$points
						)
					);
				}

				if (showForm) {
					$thead.find('tr').append(
						$j('<th>').text('Form')
					);
				}

				if (data.table.length > 0) {
					$j.each(data.table, function(key, val) {
						var $row = $j('<tr>');

						if (typeof val.info == 'string' && val.info.length > 0) {
							$row.addClass('sfc_' + val.info);
						}

						if (highlight == val.team) {
							$row.addClass('sfc_highlight');
						}

						var $position			= $j('<td>').addClass('sfc_numeric').text(val.pos);
						var $team				= $j('<td>').addClass('sfc_badge_' + val.path).text(val.team);
						var $played				= $j('<td>').addClass('sfc_numeric').text(val.p);
						var $goal_difference	= $j('<td>').addClass('sfc_numeric').text(val.gd);
						var $points				= $j('<td>').addClass('sfc_numeric').text(val.pts);

						if (showBadges) {
							$team.addClass('sfc_team').css('background-image', 'url(https://api.statsfc.com/kit/' + val.path + '.svg)');
						}

						if (tableType == 'full') {
							$row.append(
								$position,
								$team,
								$played,
								$j('<td>').addClass('sfc_numeric').text(val.w),
								$j('<td>').addClass('sfc_numeric').text(val.d),
								$j('<td>').addClass('sfc_numeric').text(val.l),
								$j('<td>').addClass('sfc_numeric').text(val.gf),
								$j('<td>').addClass('sfc_numeric').text(val.ga),
								$goal_difference,
								$points
							);
						} else {
							$row.append(
								$position,
								$team,
								$played,
								$goal_difference,
								$points
							);
						}

						if (showForm) {
							var $form = $j('<td>').addClass('sfc_form');

							$j.each(val.form, function(key, match) {
								$form.append(
									$j('<span>').addClass('sfc_form sfc_' + match).text('\xA0')
								);
							});

							$row.append($form);
						}

						$tbody.append($row);
					});

					$table.append($thead, $tbody);
				}

				$container.append($table);

				if (data.customer.attribution) {
					$container.append(
						$j('<div>').attr('class', 'sfc_footer').append(
							$j('<p>').append(
								$j('<small>').append('Powered by ').append(
									$j('<a>').attr({ href: 'https://statsfc.com', title: 'StatsFC – Football widgets', target: '_blank' }).text('StatsFC.com')
								).append('. Fan data via ').append(
									$j('<a>').attr({ href: 'https://crowdscores.com', title: 'CrowdScores', target: '_blank' }).text('CrowdScores.com')
								)
							)
						)
					);
				}
			}
		);

		$j('#' + placeholder).append($container);
	};
}
