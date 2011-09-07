<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('prep_leaderboard')) {
	function prep_leaderboard($leaderboard) {
		$prev_score = FALSE;

		foreach ($leaderboard as $pos => &$leader) {
			if (FALSE === $prev_score) {
				$prev_score = $leader['score'];
				$leader['position'] = $pos + 1;
				continue;
			}

			if ($prev_score == $leader['score']) {
				$leader['position'] = $leaderboard[$pos - 1]['position'];

				// insert equalses
				if ($pos > 0) {
					if (FALSE === strpos($leaderboard[$pos - 1]['position'], '=')) {
						$leaderboard[$pos - 1]['position'] .= '=';
					}
				}
				$leader['position'] = $leaderboard[$pos - 1]['position'];
			}
			else {
				$leader['position'] = $pos + 1;
			}

			$prev_score = $leader['score'];
		}

		return $leaderboard;
	}
}
else {
	error_log("Can't redefine prep_leaderboard()");
}