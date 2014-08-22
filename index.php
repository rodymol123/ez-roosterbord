<? require_once('common.php');

function html_start($collapsed = false) {
	global $entity_type, $entity_multiple, $entity_name, $weken, $safe_week, $link_tail_wowk, $prev_week, $next_week, $no_berichten, $default_week;
	header("Content-Type: text/html; charset=UTF-8"); ?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="zermelo_zoom.ico">
<title>Roosterbord <? echo(config('SCHOOL_AFKORTING').' '.config('SCHOOLJAAR_LONG')) ?></title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/print.css" media="print">
<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function(){
<? if (binnen_school()) { ?>
	$('#q').autocomplete({
		source: "search.php?wk=<? echo($safe_week) ?>",
		minLength: 3,
		select: function(event, ui) {
			if (ui.item) $('#q').val(ui.item.id); // set autocomplete ID
			$('#search').submit();
		}
	});
<? } ?>
	$('#q').focus();
	$('#accordion').accordion({
<? if ($collapsed) { ?> 
		collapsible: true,
		active: false,
<? } ?>
		heightStyle: "content"
	});
});
//]]>
</script>
</head>
<body>
<? if (config('ENABLE_TEST_WARNING')) { ?>
<h1>DIT IS EEN TEST! Er vindt momenteel techisch onderhoud plaats
aan het roosterbord en de onderstaande data klopt dus mogelijk niet!</h1>
<? } ?>
<div id="content">
<p><div class="noprint" style="float: left"><form id="search" method="GET" name="search" accept-charset="UTF-8"><input type="submit" value="Zoek:">
<input id="q" size="<? echo(binnen_school()?40:10) ?>" name="q"><? if ($_GET['q'] != '') { if ($entity_type === '') echo(' <span class="error">Zoekterm "'.htmlenc($_GET['q']).'" niet gevonden.</span>'); else {
		echo(' of kijk in de '.make_link('', 'lijst'));
		if ($no_berichten == 1) echo(' (1 bericht)');
		else if ($no_berichten == 0) echo(' (geen berichten)');
		else echo(' ('.$no_berichten.' berichten)');
	}
} ?>
<input name="bw" type="hidden" value="<? echo($_GET['bw']) ?>">
<input name="wk" type="hidden" value="<? if ($safe_week != $default_week) { echo($safe_week); } ?>">
<? if (!$entity_multiple && ($entity_type == STAMKLAS || $entity_type == LESGROEP)) { ?>
 <a href="https://klassenboek.ovc.nl/nologin.php?week=<? echo($safe_week) ?>&amp;q=<? echo($entity_name) ?>">&gt;Klassenboek&lt;</a>
<? }  else if (!$entity_multiple && $entity_type == LEERLING) { ?>
 <a href="https://klassenboek.ovc.nl/nologin.php?week=<? echo($safe_week) ?>&amp;q=<? echo($entity_name) ?>">&gt;Klassenboek&lt;</a>
<? } else { ?>
 <a href="https://klassenboek.ovc.nl/">&gt;Klassenboek&lt;</a>
<? } ?>
</form></div>
<div class="noprint" style="float: right"><form method="GET" name="basisweek" accept-charset="UTF-8">
weeknummer:
<? echo(($prev_week !== NULL)?'<a href="?q='.urlencode($_GET['q']).$link_tail_wowk.$prev_week.'">&lt;</a>':'<del>&lt;</del>'); ?><select onchange="document.basisweek.submit()" name="wk">
<? foreach ($weken as $week) {
	echo('<option');
	if ($safe_week == $week) echo(' selected');
	echo(' value="');
	if ($default_week != $week) echo($week);
	echo('">'.$week.'</option>');
} ?>
</select><? echo(($next_week !== NULL)?'<a href="?q='.urlencode($_GET['q']).$link_tail_wowk.$next_week.'">&gt;</a>':'<del>&gt;</del>'); ?>
<!-- <input onclick="document.basisweek.submit()" type="radio" <? if ($_GET['bw'] == 'b') echo('checked ') ?>name="bw" value="b">basisrooster
<input onclick="document.basisweek.submit()" type="radio" <? if ($_GET['bw'] == 'w') echo('checked ') ?>name="bw" value="w">weekrooster -->
<select onchange="document.basisweek.submit()" name="bw">
<option <? if ($_GET['bw'] == 'b') echo('selected ') ?>value="b">basisrooster</option>
<option <? if ($_GET['bw'] == 'w') echo('selected ') ?>value="w">weekrooster</option>
<option <? if ($_GET['bw'] == 'y') echo('selected ') ?>value="y">weekrooster; alleen wijzigingen</option>
<option <? if ($_GET['bw'] == 'd') echo('selected ') ?>value="d">weekrooster; lessen die doorgaan</option>
<option <? if ($_GET['bw'] == 'x') echo('selected ') ?>value="x">basisrooster tov vorige week</option>
</select>
<input name="q" type="hidden" value="<? echo(htmlenc($_GET['q'])) ?>">
</form></div>
<? }

function html_end() {
	$schooljaar_long = config('SCHOOLJAAR_LONG');
	$year = date('Y', $_SERVER['REQUEST_TIME']);
	if ($year > substr($schooljaar_long, 0, 4)) $rooster_copy = str_replace('/', ', ', $schooljaar_long);
	else $rooster_copy = $year;
	$version_copy = '-'.exec('git describe').' &copy; '.substr(exec('git show -s --format=%ci'), 0, 4).' Rik Snel';
?><p><div id="footer">
<div class="noprint">
Rooster &copy; <? echo($rooster_copy.' '.config('SCHOOL_VOLUIT')) ?>, all rights reserved. Favicon &copy; 1953 Konrad Jacobs, license <a href="http://creativecommons.org/licenses/by-sa/2.0/de/deed.en">CC-BY-SA-2.0-DE</a><br>
Deze webinterface, <a href="http://ez-roosterbord.nl/">ez-roosterbord</a><? echo($version_copy); ?> &lt;rik@snel.it&gt;. Powered by PHP <? echo(phpversion()); ?>.<br>
Released as <a href="http://www.gnu.org/philosophy/free-sw.html">free software</a> without warranties under <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">GNU AGPL v3</a>.<br>
Sourcecode: <code>git clone <a href="https://github.com/rsnel/ez-roosterbord/">https://github.com/rsnel/ez-roosterbord/</a></code>.
</div>
<div class="onlyprint">
Rooster &copy; <? echo($rooster_copy.' '.config('SCHOOL_VOLUIT')) ?>, all rights reserved, ez-roosterbord<? echo($version_copy) ?> is vrije software onder de GNU Affero GPL v3.
</div>
</div>
</div>
</body></html>
<? }

function entity_prevnext($name, $type) {
	if ($type != LOKAAL && $type != DOCENT && $type != STAMKLAS && $type != VAK && $type != CATEGORIE) fatal_error('unsupported prevnext');

	$prev = mdb2_single_val("SELECT MAX(entity_name) FROM entities WHERE entity_name < '%q' AND entity_active IS NOT NULL AND entity_type = $type", $name);
	$next = mdb2_single_val("SELECT MIN(entity_name) FROM entities WHERE entity_name > '%q' AND entity_active IS NOT NULL AND entity_type = $type", $name);

	if ($type == VAK) $name = substr($name, 1);

	return '<span class="noprint">'.make_link_conditional($prev, '&lt;').'</span>'.htmlenc($name).'<span class="noprint">'.make_link_conditional($next, '&gt;').'</span>';
}

$PID = getmypid();

/* een gebruiker wil kennelijk het rooster zien.... */

$min_week_id = mdb2_single_val("SELECT MIN(week_id) FROM roosters");
if ($min_week_id) {
	$res = mdb2_query("SELECT week FROM weken WHERE week_id >= $min_week_id ORDER BY week_id");
	$weken = $res->fetchCol();
	$res->free();
} else $weken = array();

/* sanitize the input */
if (!isset($_GET['bw'])) $_GET['bw'] = 'w';
else if ($_GET['bw'] != 'w' && $_GET['bw'] != 'y' && $_GET['bw'] != 'b' && $_GET['bw'] != 'd' && $_GET['bw'] != 'x') $_GET['bw'] = 'w';

$default_week = get_default_week($weken); // calculate default week

$week_not_given = 0;
if (!isset($_GET['wk']) || !in_array($_GET['wk'], $weken)) {
	$_GET['wk'] = $default_week;
	$week_not_given = 1;
}

if ($_GET['wk'] == NULL) {
	// berichten
	$berichten = mdb2_query(<<<EOQ
SELECT bericht_body, bericht_title, IFNULL(bla.entities, 'Allen') bericht_entities FROM berichten
LEFT JOIN (
	SELECT bericht_id, GROUP_CONCAT(entity_name) entities
	FROM entities2berichten
	JOIN entities ON entities.entity_id = entities2berichten.entity_id
	GROUP BY bericht_id
) AS bla ON bla.bericht_id = berichten.bericht_id
WHERE bericht_visibleuntil > {$_SERVER['REQUEST_TIME']}
AND bericht_visiblefrom <= {$_SERVER['REQUEST_TIME']}
ORDER BY bericht_update DESC
EOQ
);
?>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="zermelo_zoom.ico">
<title>Roosterbord OVC <? echo(config('SCHOOLJAAR_LONG')) ?></title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/print.css" media="print">
<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function(){
        $('#accordion').accordion({
<? if (isset($collapsed) && $collapsed) { ?> 
                collapsible: true,
                active: false,
<? } ?>
                heightStyle: "content"
        });
});
//]]>
</script>
</head>
<body>
<div id="content">
De roostermakers werken momenteel hard aan het rooster van het nieuwe schooljaar.
<? show_berichten(NULL, false, $berichten); ?>
</div>
</body>
</html>
<?
	exit;
}

$safe_week = (int)$_GET['wk'];
$week_index = array_search($safe_week, $weken);
$prev_week = NULL;
$next_week = NULL;
if ($week_index > 0) $prev_week = $weken[$week_index - 1];
if ($week_index < count($weken) - 1) $next_week = $weken[$week_index + 1];
$real_prev_week = $prev_week;
if ($prev_week == $default_week) $prev_week = '';
if ($next_week == $default_week) $next_week = '';


if ($safe_week != $_GET['wk']) fatal_error('sanity check failed');

// $_GET['bw'] is already sanitized at this point
$link_tail_wowk = '&amp;bw='.$_GET['bw'].'&amp;wk=';

if ($safe_week != $default_week) $link_tail = $link_tail_wowk.$safe_week.'">';
else $link_tail = $link_tail_wowk.'">';

if (!isset($_GET['q'])) $_GET['q'] = '';
else $_GET['q'] = trim($_GET['q']);

$qs = explode(',', $_GET['q']);
//sort($qs);

// zoek week_id van deze week, meest actuele basisrooster, bijbehorende wijzigingen
$week_info = mdb2_single_array("SELECT week_id, ma, di, wo, do, vr FROM weken WHERE week = $safe_week");
if (!$week_info) fatal_error("impossible, week $safe_week bestaat niet in tabel weken?!?!!?");
$week_id = $week_info[0];

if ($_GET['bw'] != 'x') {
	$basis = mdb2_single_assoc("SELECT file_id, basis_id, timestamp FROM roosters WHERE week_id <= $week_id AND wijz_id = 0 ORDER BY rooster_id DESC LIMIT 1");
	if (!$basis) fatal_error("impossible: toch geen basisrooster in deze week?!?!?!?");
	$wijz = mdb2_single_assoc("SELECT file_id, wijz_id, timestamp FROM roosters WHERE week_id = $week_id AND basis_id = {$basis['basis_id']} ORDER BY rooster_id DESC LIMIT 1");
	if ($basis['file_id'] == $wijz['file_id'] || !$wijz['file_id']) $wijz['file_id'] = 0;
} else {
	$wijz = mdb2_single_assoc("SELECT file_id, basis_id, timestamp FROM roosters WHERE week_id = $week_id AND wijz_id = 0 ORDER BY rooster_id DESC LIMIT 1");
	if (!$wijz) {
		//fatal_error("impossible: toch geen basisrooster in deze week?!?!?!?, HUMBUG");
		$basis = mdb2_single_assoc("SELECT file_id, basis_id, timestamp FROM roosters WHERE week_id <= $week_id AND wijz_id = 0 ORDER BY rooster_id DESC");
		$wijz = array();
		$wijz['file_id'] = 0;
	} else {
		$basis = array();
		if ($real_prev_week) {
			$old_week_id = mdb2_single_val("SELECT week_id FROM weken WHERE week = $real_prev_week LIMIT 1");
			$basis = mdb2_single_assoc("SELECT file_id, basis_id, timestamp FROM roosters WHERE week_id <= $old_week_id AND wijz_id = 0 ORDER BY rooster_id DESC");
		}
	}
	if (!$basis) {
		$basis['file_id'] = 0;
	}
}

	
//print_r($basis);
//print_r($wijz);

$result = mdb2_query("SELECT entity_type, entity_id, entity_name FROM entities WHERE entity_name = '%q'", trim($qs[0]));

if (!($target = $result->fetchRow()) ||
	($target[0] == LOKAAL && config('HIDE_ROOMS')) ||
	($target[0] == LEERLING && config('HIDE_STUDENTS'))
) { // niet gevonden
	$safe_id = '';
	$entity_type = '';
	$entity_name = '';
	$type = '';
	$res_klas = mdb2_query("SELECT entity_name FROM entities WHERE entity_type = ".STAMKLAS.' AND entity_active IS NOT NULL ORDER BY entity_name');
	$res_doc = mdb2_query("SELECT entity_name FROM entities WHERE entity_type = ".DOCENT.' AND entity_active IS NOT NULL ORDER BY entity_name');
	$res_lok = mdb2_query("SELECT entity_name FROM entities WHERE entity_type = ".LOKAAL.' AND entity_active IS NOT NULL ORDER BY entity_name');
	$res_vak = mdb2_query("SELECT entity_name FROM entities WHERE entity_type = ".VAK.' AND entity_active IS NOT NULL ORDER BY entity_name');
	$res_cat = mdb2_query("SELECT entity_name FROM entities WHERE entity_type = ".CATEGORIE.' AND entity_active IS NOT NULL ORDER BY entity_name');


	// berichten
	$berichten = mdb2_query(<<<EOQ
SELECT bericht_body, bericht_title, IFNULL(bla.entities, 'Allen') bericht_entities FROM berichten
LEFT JOIN (
	SELECT bericht_id, GROUP_CONCAT(entity_name) entities
	FROM entities2berichten
	JOIN entities ON entities.entity_id = entities2berichten.entity_id
	GROUP BY bericht_id
) AS bla ON bla.bericht_id = berichten.bericht_id
WHERE bericht_visibleuntil > {$_SERVER['REQUEST_TIME']}
AND bericht_visiblefrom <= {$_SERVER['REQUEST_TIME']}
ORDER BY bericht_update DESC
EOQ
);

	goto cont;
} else {
	$entity_type = $target[0];
	$entity_name = $target[2];

	$safe_id = (int)$target[1]; // is onze entity_id een integer?
	if ($safe_id != $target[1]) fatal_error('sanity check failed'); // nee?!?!?
	
	// berichten
	$no_berichten = mdb2_single_val(<<<EOQ
SELECT COUNT(*) FROM berichten WHERE bericht_visibleuntil > {$_SERVER['REQUEST_TIME']} AND bericht_visiblefrom <= {$_SERVER['REQUEST_TIME']}
EOQ
);
}

if ($result->fetchRow()) fatal_error('impossibe'); // meer dan een resultaat?, kan niet!

$entity_multiple = 0;

while (array_shift($qs) && count($qs)) {
	// we hebben extra dingen met komma's
	$result = mdb2_query("SELECT entity_type, entity_id, entity_name FROM entities WHERE entity_name = '%q'", trim($qs[0]));
	if (($target = $result->fetchRow()) && ($entity_type == $target[0] ||
		( $entity_type == LESGROEP && $target[0] == STAMKLAS ) ||
		( $entity_type == STAMKLAS && $target[0] == LESGROEP ))) { // gevonden
		$safe_id .= ','.$target[1];
		$entity_name .= ','.$target[2];
		$entity_multiple = 1;
	}
}

// sorteer de gekozen entities als het er meer zijn
if ($entity_multiple) {
	$tmp = explode(',', $entity_name);
	sort($tmp);
	$entity_name = implode(',', $tmp);
}

if (isset($_GET['export'])) {
	if ($entity_type != 0) fatal_error('not implemented');
	if ($week_not_given) fatal_error('no week specified');
	header('Content-Type: text/plain');
	header('Content-Disposition: inline; filename=export.txt;');
	$wz = '';
	if ($_GET['bw'] == 'b') $file_id = $basis['file_id'];
	else if ($_GET['bw'] == 'y' && $wijz['file_id']) {
		$file_id = $wijz['file_id'];
		$wz = ', '.$wijz['wijz_id'].' file_rev_wijz';
	} else fatal_error('impossible mission wijz_file_id='.$wijz['file_id']);
	$res = mdb2_query(<<<EOQ
SELECT {$basis['basis_id']} file_rev_basis$wz, zermelo_id, dag, uur, vakken, docenten, lokalen, lesgroepen, notitie
FROM files2lessen
JOIN lessen ON lessen.les_id = files2lessen.les_id
WHERE file_id = $file_id
EOQ
);
	$cols = $res->getColumnNames(1);
	echo(implode("\t", $cols)."\n");
	while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		if (config('HIDE_ROOMS')) $row['lokalen'] = '';
		echo(implode("\t", $row)."\n");
	}
	exit;
}

/* basis_id, prefix, postfix, own, dag, uur, vak, wijz_id */
define('BASIS_ID', 0);
define('LESGROEPEN', 1);
define('VAKKEN', 2);
define('DOCENTEN', 3);
define('LOKALEN', 4);
define('DAG', 5);
define('UUR', 6);
define('NOTITIE', 7);
define('WIJZ_ID', 8);
define('BASIS_ID2', 9);
define('LESGROEPEN2', 10);
define('VAKKEN2', 11);
define('DOCENTEN2', 12);
define('LOKALEN2', 13);
define('DAG2', 14);
define('UUR2', 15);
define('NOTITIE2', 16);
define('VIS', 17);

function rquery_inner($entity_ids, $id1, $id2, $left, $wijz) {
	//echo("entity_ids = $entity_ids, id1 = $id1, id2 = $id2, left = $left, wijz = $wijz");
	if ($entity_ids === '') $entity_ids = 'NULL';
	else if (!$entity_ids) return<<<EOQ
SELECT DISTINCT f2l.les_id f_id, l2f.les_id s_id, f2l.zermelo_id f_zid, l2f.zermelo_id s_zid, 1 vis, $wijz wijz
FROM files2lessen AS f2l
{$left}JOIN files2lessen AS l2f ON f2l.zermelo_id = l2f.zermelo_id AND l2f.file_id = $id2
WHERE f2l.file_id = $id1
EOQ;
	return <<<EOQ
SELECT DISTINCT f2l.les_id f_id, l2f.les_id s_id, f2l.zermelo_id f_zid, l2f.zermelo_id s_zid, CASE WHEN l2e.entity_id > 0 THEN 1 ELSE 0 END AS vis, $wijz wijz
FROM files2lessen AS f2l
JOIN entities2lessen AS e2l ON e2l.les_id = f2l.les_id
{$left}JOIN files2lessen AS l2f ON f2l.zermelo_id = l2f.zermelo_id AND l2f.file_id = $id2
LEFT JOIN entities2lessen AS l2e ON l2f.les_id = l2e.les_id AND l2e.entity_id IN ($entity_ids)
WHERE f2l.file_id = $id1 AND e2l.entity_id IN ($entity_ids)
EOQ;
}

function rquery($entity_ids, $id1, $id2, $left) {
	return rquery_inner($entity_ids, $id1, $id2, $left, 0).
		"\nUNION ALL\n".rquery_inner($entity_ids, $id2, $id1, 'LEFT ', 1);
}

$multiple_sort = '';

switch ($entity_type) {
case LESGROEP:
case STAMKLAS:
case CATEGORIE:
	// we maken voor deze lesgroep(en)/stamklas(sen) ook een selectbox met leerlingen
	$options_name = ', leerlingen: ';
	$options = '<option selected value="'.htmlenc($_GET['q']).'"></option>';
	if (binnen_school()) {
		$result2 = mdb2_query(<<<EOT
SELECT entities.entity_name leerlingnummer, name, GROUP_CONCAT(stamklassen.entity_name), surname, firstname, prefix
FROM grp2ppl
JOIN entities ON entity_id = ppl_id
JOIN names ON names.entity_id = entities.entity_id
JOIN grp2ppl AS grp2ppl_back ON grp2ppl_back.ppl_id = grp2ppl.ppl_id AND grp2ppl_back.file_id_basis = grp2ppl.file_id_basis
JOIN entities AS stamklassen ON grp2ppl_back.lesgroep_id = stamklassen.entity_id AND stamklassen.entity_type = %i
WHERE grp2ppl.file_id_basis = {$basis['file_id']} AND grp2ppl.lesgroep_id IN ( $safe_id )
GROUP BY entities.entity_id
ORDER BY surname, firstname, prefix
EOT
, STAMKLAS);
		while ($row = $result2->fetchRow(MDB2_FETCHMODE_ORDERED)) {
			$options .= '<option value="'.htmlenc($row[0]).'">'.htmlenc($row[1].' ('.$row[2].'/'.$row[0].')').'</option>'."\n";
		}
	} else {
		$query2 = <<<EOT
SELECT entity_name
FROM grp2ppl
JOIN entities ON entity_id = ppl_id
WHERE file_id_basis = {$basis['file_id']} AND grp2ppl.lesgroep_id IN ( $safe_id )
ORDER BY CAST(entity_name AS UNSIGNED INTEGER)
EOT;
		$result2 = mdb2_query($query2);
		while ($row = $result2->fetchRow()) {
			$options .= '<option class="llnrs">'.htmlenc($row[0]).'</option>'."\n";
		}
	}

	if ($entity_multiple) {
		$type = 'groepen '.split_links($entity_name); 
		$multiple_sort = ', f_lesgroepen';
	}
	else if ($entity_type == LESGROEP) $type = 'lesgroep '.htmlenc($entity_name);
	else if ($entity_type == STAMKLAS) $type = 'klas '.entity_prevnext($entity_name, STAMKLAS);
	else $type = 'categorie '.entity_prevnext($entity_name, CATEGORIE);
	

	$result2 = mdb2_query(<<<EOQ
SELECT lesgroep_id FROM grp2grp
WHERE file_id_basis = {$basis['file_id']} AND lesgroep2_id IN ( $safe_id )
EOQ
);
	// van groepen met leerlingen laten we de roosters van alle gerelateerde groepen
	// ook zien, van een lege groep alleen het rooster van de groep zelf
	$entity_ids = $result2->fetchCol();
	if (count($entity_ids) && !config('HIDE_STUDENTS')) $safe_id = implode(',', $entity_ids);
	break;
case LEERLING:
	if ($entity_multiple) {
		$multiple_sort = ', f_lesgroepen';
		$type .= 'onderstaande leerlingen';
		$result2 = mdb2_query("SELECT entity_id FROM grp2ppl JOIN entities ON entity_id = lesgroep_id WHERE ppl_id IN ( $safe_id ) AND file_id_basis = {$basis['file_id']}");
		while ($row = $result2->fetchRow()) $entity_ids[] = $row[0];
		
		if (binnen_school()) {
			$lln = mdb2_query(<<<EOT
SELECT DISTINCT entities.entity_id, CONCAT(name, ' (<a href="?q=', entity_name, '$link_tail', entity_name, '</a>)') leerlingnummer, surname, firstname, prefix
FROM names
JOIN entities ON names.entity_id = entities.entity_id
WHERE names.entity_id IN ( $safe_id )
ORDER BY surname, firstname, prefix
EOT
);
		} else {
			$query2 = <<<EOT
SELECT DISTINCT entity_id, entity_name AS tmp
FROM entities 
WHERE entity_id IN ( $safe_id )
ORDER BY CAST(entity_name AS UNSIGNED INTEGER)
EOT;
			$lln = mdb2_query($query2);
		}

		$subscript = '<table id="leerlinglijst">';
		while (($row = $lln->fetchRow())) {
			$subscript .= '<tr>';
			$subscript .= '<td>'.$row[1].'</td>';
			$grps = mdb2_query("SELECT entity_name FROM grp2ppl JOIN entities ON entities.entity_id = grp2ppl.lesgroep_id WHERE ppl_id = {$row[0]} AND file_id_basis = {$basis['file_id']} ORDER BY entity_type DESC, entity_name");
			while (($row2 = $grps->fetchRow())) {
				$subscript .= '<td>'.make_link($row2[0]).'</td>';
			}
			$subscript .= '</tr>';
		}
		$subscript .= '</table>';

	} else {
		if (binnen_school()) $type = mdb2_single_val("SELECT name FROM names WHERE entity_id IN ( $safe_id )");
		else $type = 'leerling '.htmlenc($entity_name);

		// we maken voor deze leerling ook een lijst met lesgroepen
		$result2 = mdb2_query("SELECT entity_id, entity_name FROM grp2ppl JOIN entities ON entity_id = lesgroep_id WHERE ppl_id IN ( $safe_id ) AND file_id_basis = {$basis['file_id']} -- AND entity_type != ".CATEGORIE);
		$type .= ', groepen: '; $entity_ids = array();
		while ($row = $result2->fetchRow()) {
			$entity_ids[] = $row[0];
			$type .= ' '.make_link($row[1]);
		}
	}

	$safe_id = implode(',', $entity_ids);
	break;
case VAK:
	if ($entity_multiple) $type = 'vakken '.split_links($entity_name);
	else $type = 'vak '.entity_prevnext($entity_name, VAK);
	break;
case DOCENT:
	if ($entity_multiple) {
		$type = 'docenten '.split_links($entity_name);
		$multiple_sort = ', f_docenten';
	}
	else $type = 'docent '.entity_prevnext($entity_name, DOCENT);
	break;
case LOKAAL:
	if ($entity_multiple) {
		$type = 'lokalen '.split_links($entity_name);
		$multiple_sort = ', f_lokalen';
	}
	else $type = 'lokaal '.entity_prevnext($entity_name, LOKAAL);
	break;
case 0:
	$type = '*';
	if ($_GET['bw'] == 'b' || $_GET['bw'] == 'y')
		$type .= ' <a href="?bw='.$_GET['bw'].'&wk='.$safe_week.'&q=*&export">[export]</a>';
	$safe_id = NULL;
	break;
default:
	fatal_error('onmogelijk type');
}

//$basis['file_id'] = 7;
//$wijz['file_id'] = 8;

$subquery = rquery($safe_id, $basis['file_id'], ($_GET['bw'] == 'b')?0:$wijz['file_id'], ($_GET['bw'] == 'y')?'':'LEFT ');

//echo($subquery);
//$res_test = mdb2_query($subquery);
//mdb2_res_table($res_test);

$result = mdb2_query(<<<EOQ
SELECT f_zid, f.lesgroepen AS f_lesgroepen, f.vakken AS f_vakken,
	f.docenten AS f_docenten, f.lokalen AS f_lokalen,
	f.dag AS f_dag, f.uur AS f_uur, f.notitie AS f_notitie, wijz,
	s_zid, s.lesgroepen AS s_lesgroepen, s.vakken AS s_vakken,
	s.docenten AS s_docenten, s.lokalen AS s_lokalen,
	s.dag AS s_dag, s.uur AS s_uur, s.notitie AS s_notitie, vis
FROM ( $subquery ) AS sub
JOIN lessen AS f ON f.les_id = f_id
LEFT JOIN lessen AS s ON s.les_id = s_id
WHERE f.lesgroepen IS NOT NULL AND f.dag != 0 AND f.uur != 0
ORDER BY f_uur, f_dag$multiple_sort, f_vakken, f_zid, s_dag DESC
EOQ
);
if (($entity_type == LESGROEP || $entity_type == STAMKLAS || $entity_type == CATEGORIE || $entity_type == LEERLING) && $safe_id) { 
	// berichten
	$berichten = mdb2_query(<<<EOQ
SELECT bericht_body, bericht_title, IFNULL(bla.entities, 'Allen') bericht_entities FROM berichten
LEFT JOIN (
	SELECT bericht_id, GROUP_CONCAT(entity_name) entities
	FROM entities2berichten
	JOIN entities ON entities.entity_id = entities2berichten.entity_id
	GROUP BY bericht_id
) AS bla ON bla.bericht_id = berichten.bericht_id
JOIN (
	SELECT DISTINCT berichten.bericht_id
	FROM berichten
	LEFT JOIN entities2berichten ON entities2berichten.bericht_id = berichten.bericht_id
	WHERE entity_id IN ( $safe_id ) OR entity_id IS NULL
) AS bla2 ON bla2.bericht_id = berichten.bericht_id
WHERE bericht_visibleuntil > {$_SERVER['REQUEST_TIME']}
AND bericht_visiblefrom <= {$_SERVER['REQUEST_TIME']}
ORDER BY bericht_update DESC
EOQ
);
}

//mdb2_res_table($result);
cont:

function make_link($target, $text = NULL) {
	global $link_tail;
	return '<a href="?q='.urlencode($target).$link_tail.($text?$text:htmlenc($target)).'</a>';
}

function make_link_conditional($target, $text) {
	if ($target) return make_link($target, $text); 
	else return '<del>'.$text.'</del>';
}

function split_links($target) {
	return implode(', ', array_map('make_link', explode(',', $target)));
}

function print_diff($row) {
	if ($row[DAG] != $row[DAG2] || $row[UUR] != $row[UUR2]) $output[] = print_dag($row[DAG2]).$row[UUR2];
	if ($row[LESGROEPEN] != $row[LESGROEPEN2] && $row[LESGROEPEN2] != '') $output[] = make_link($row[LESGROEPEN2]);
	if ($row[VAKKEN] != $row[VAKKEN2] && $row[VAKKEN2] != '') {
		if ($row[VAKKEN2] != '' && !preg_match("/\.{$row[VAKKEN2]}[0-9]?\$/", $row[LESGROEPEN2])) $output[] = htmlenc($row[VAKKEN2]);
	}
	if ($row[DOCENTEN] != $row[DOCENTEN2]) {
		if ($row[DOCENTEN2] != '') $output[] = make_link($row[DOCENTEN2]);
		else $output[] = '<span class="unknown">DOC?</span>';
	}	
	if ($row[LOKALEN] != $row[LOKALEN2]) {
       		if ($row[LOKALEN2] != '') $output[] = make_link($row[LOKALEN2]);
		else $output[] = '<span class="unknown">LOK?</span>';
	}
	return implode('/', $output);
}

function cleanup_row(&$row) {
	// als er een stamklas met onzinnig voorvoegsel is, dan schrappen we dat voorvoegsel
	if (config('HIDE_ROOMS')) {
		$row[LOKALEN] = '';
		if ($_GET['bw'] != 'b') $row[LOKALEN2] = '';
	}
}
	
function enccommabr($string) {
	return implode('<br>', explode(',', htmlenc($string)));
}

function add(&$info, $name, $void = '') {
	global $entity_name;
	if ($name == $entity_name) return;
	if ($name == '') {
		if ($void) $info[] = $void;
	} else $info[] = make_link($name, enccommabr($name));

}

function add_lv(&$info, $lesgroepen, $vak) {
	global $entity_type, $entity_multiple;
	if ($entity_type == LEERLING && !$entity_multiple) {
		if ($vak != '') $info[] = enccommabr($vak);
	} else {
		add($info, $lesgroepen);
		// we laten het vak alleen zien als het niet in de naam van de lesgroep zit
		if ($vak != '' && !preg_match("/{$vak}[0-9]?\$/", $lesgroepen)) $info[] = enccommabr($vak);
	}
}

function show_berichten($entity_type, $entity_multiple, $berichten) { ?>
<div <? if ($entity_type) { ?>class="noprint" <? } ?>id="accordion">
<?
	if ($entity_type == LESGROEP || $entity_type == STAMKLAS || $entity_type == CATEGORIE)
		$add = ' voor leerlingen uit deze groep'.($entity_multiple?'en':'');
	else if ($entity_type == LEERLING)
		$add = ' voor deze leerling'.($entity_multiple?'en':'');
	else $add = '';

	if ($berichten) { // in sommige gevallen bestaat deze variabele niet (ll zit niet in een groep)

	$bericht = $berichten->fetchRow(MDB2_FETCHMODE_ASSOC);

	if (!$bericht) echo('<h3><a href="#">Er zijn geen actuele berichten van de roostermakers'.$add.'.</a></h3>');
	else {
		do {
			echo('<h3><a href="#">'.$bericht['bericht_title'].' ('.$bericht['bericht_entities'].')</a></h3><div class="berichtbody">');
			echo($bericht['bericht_body'].'</div>');
		} while ($bericht = $berichten->fetchRow(MDB2_FETCHMODE_ASSOC));
	}

	}
?>
</div>
<?
}

html_start($entity_type !== ''); ?><div class="clear" style="padding-top: 1px;">
<?
if ($entity_type === '') { 
	show_berichten($entity_type, false, $berichten);
	echo('<p>Selecteer hieronder een klas, docent of lokaal.');
	echo('<p>Klassen:');
	while ($row = $res_klas->fetchRow()) echo(' '.make_link($row[0]));
	echo('<p>Docenten:');
	while ($row = $res_doc->fetchRow()) echo(' '.make_link($row[0]));
	if (!config('HIDE_ROOMS')) {
		echo('<p>Lokalen:');
		while ($row = $res_lok->fetchRow()) echo(' '.make_link($row[0]));
	}
	echo('<p>Vakken:');
	while ($row = $res_vak->fetchRow()) echo(' '.make_link($row[0], htmlenc(substr($row[0], 1))));
	echo('<p>Categorie&euml;n:');
	while ($row = $res_cat->fetchRow()) echo(' '.make_link($row[0]));
	echo('<p>'."\n");
} else {

if ($entity_type == STAMKLAS || $entity_type == LESGROEP || $entity_type == LEERLING) { 
	show_berichten($entity_type, $entity_multiple, $berichten);
}
?>

<p>
<? if ($entity_type == LESGROEP || $entity_type == STAMKLAS || $entity_type == CATEGORIE) { ?>
<form method="GET" name="leerling" accept-charset="UTF-8">
<input type="hidden" name="bw" value="<? echo($_GET['bw']) ?>">
<input type="hidden" name="wk" value="<? echo($safe_week) ?>">
<? }

if ($_GET['q']) {
	if ($_GET['bw'] == 'b') echo('Basisrooster');
	else if ($_GET['bw'] == 'y') echo('Weekrooster, alleen wijzigingen');
	else if ($_GET['bw'] == 'd') echo('Weekrooster, lessen die doorgaan');
	else if ($_GET['bw'] == 'x') echo('Basisroosterwijzigingen tov vorige (les)week');
	else echo('Weekrooster');
?> van <? echo($type);
}

echo('<span class="onlyprint"> in week '.$safe_week.'.</span>');

if (($entity_type == LESGROEP || $entity_type == STAMKLAS || $entity_type == CATEGORIE) && !config('HIDE_STUDENTS')) {
	echo('<span class="noprint">'.$options_name) ?><select onChange="document.leerling.submit();" name="q">
		<? 	echo($options) ?></select></span></form><?
} else if ($entity_type == LEERLING && $entity_multiple) {
	echo($subscript);
} else {
	echo("\n");
} 
$dubbel = array(); // in deze array houden we bij welke zermelo_ids
		   // al aan de beurt geweest zijn, zodat 'verplaatsing + uitval'
		   // alleen 'verplaatsing' wordt

if (isset($_GET['debug'])) mdb2_res_table($result);
$row = $result->fetchRow();

if ($safe_week < 30) {
	        $year = substr(config('SCHOOLJAAR_LONG'), 5);
		} else {
        $year = substr(config('SCHOOLJAAR_LONG'), 0, 4);
}
$day_in_week = strtotime(sprintf("$year-01-04 + %d weeks", $safe_week - 1));
$thismonday = $day_in_week - ((date('w', $day_in_week) + 6)%7)*24*60*60;
?>
<p><table id="rooster">
<tr><th></th>
<th>ma <? echo date("j-n", $thismonday)         ?></th>
<th>di <? echo date("j-n", $thismonday + 86400) ?></th>
<th>wo <? echo date("j-n", $thismonday +172800) ?></th>
<th>do <? echo date("j-n", $thismonday +259200) ?></th>
<th>vr <? echo date("j-n", $thismonday +345600) ?></th>
</tr>
<? for ($i = 1; $i <= 9; $i++) {
	echo('<tr class="spacer"><td>'.$i.'</td>'."\n");
	for ($j = 1; $j <= 5; $j++) {
		echo('<td>');
		while ($row && $row[DAG] == $j && $row[UUR] == $i) {
			cleanup_row($row);
			$extra = ''; $comment = '';
			
			if ($row[WIJZ_ID]) { // deze les is: extra/nieuw, lokaalreservering, (fake)verplaatstvan of gewijzigd
				if (!$row[DAG2]) { // bij deze les hoort geen oude les, dus: extra, reservering of fakeverplaatstvan
					if ($row[VAKKEN] == 'lok') {
						$row[VAKKEN] = '';
						$extra = ' lokaalreservering';
						if ($row[NOTITIE]) $comment = '(<span class="onlyprint">lokaalreservering: </span>'.htmlenc($row[NOTITIE]).')';
						else $comment = '(lokaalreservering)';
					} else if (preg_match('/^van /', $row[NOTITIE])) {
						$extra = ' verplaatstvan';
						$comment = '('.htmlenc($row[NOTITIE]).')';
					} else {
						$extra = ' extra';
						if ($_GET['bw'] == 'x') {
							$comment = ' (nieuw';
							if ($row[NOTITIE] != '') $comment = '(<span class="onlyprint">nieuw: </span>'.htmlenc($row[NOTITIE]);
						} else {
							$comment = ' (extra';
							if ($row[NOTITIE] != '') $comment = '(<span class="onlyprint">extra: </span>'.htmlenc($row[NOTITIE]);
						}
						$comment .= ')';
					}
				} else { // bij deze les hoort een oude les, dus gewijzigd of verplaatstvan
					// staat de les op hetzelfde uur en is de oude les zichtbaar in dit rooster?
					if ($row[UUR] == $row[UUR2] && $row[DAG] == $row[DAG2] && $row[VIS]) {
						if ($row[LESGROEPEN] != $row[LESGROEPEN2] ||
								$row[VAKKEN] != $row[VAKKEN2] ||
								$row[DOCENTEN] != $row[DOCENTEN2] ||
								$row[LOKALEN] != $row[LOKALEN2]) {
							$extra = ' gewijzigd';
							$comment = '(was '.print_diff($row);
							if ($row[NOTITIE] != '') $comment .= ', '.htmlenc($row[NOTITIE]);
							$comment .= ')';
						}
					} else {
						$extra = ' verplaatstvan';
						$comment = '(van '.print_diff($row);
						if ($row[NOTITIE] != '') $comment .= ', '.htmlenc($row[NOTITIE]);
						$comment .= ')';
					}
				}
			} else if ($row[BASIS_ID2] || ($_GET['bw'] == 'x') && $wijz['file_id']) { // dit is uitval,vrijstelling,(fake)verplaatstnaar,gewijzigd 
				if (!$row[DAG2]) { // bij deze les hoort geen nieuwe les, dus uitval/vrijstelling/fakeverplaatstnaar
					// is deze les al aan de orde geweest bij een verplaatsing?
					// zo ja, dan skippen we deze les
					if (isset($dubbel[$row[BASIS_ID]])) {
						$row = $result->fetchRow();
						continue;
					} else if ($_GET['bw'] == 'd') { // verberg vervallen lessen
						$row = $result->fetchRow();
						continue;
					} else if (preg_match('/^naar /', $row[NOTITIE2])) {
						$extra = ' verplaatstnaar';
						$comment = '('.htmlenc($row[NOTITIE2]).')';
					} else if (preg_match('/^vrij( (.*))?$/', $row[NOTITIE2], $matches)) {
						$extra = ' vrijstelling';
						if ($matches[2] != '') $comment = '(<span class="onlyprint">vrijstelling: </span>'.htmlenc($matches[2]).')';
						else $comment = '(vrijstelling)';
					} else {
						$extra = ' uitval';
						if ($_GET['bw'] == 'x') {
							$comment = ' (oud';
							if ($row[NOTITIE2] != '') $comment = '(<span class="onlyprint">oud: </span>'.htmlenc($row[NOTITIE2]);
						} else {
							$comment = ' (uitval';
							if ($row[NOTITIE2] != '') $comment = '(<span class="onlyprint">uitval: </span>'.htmlenc($row[NOTITIE2]);
						}
						$comment .= ')';
					}
				} else { // bij deze les hoort een nieuwe les dus gewijzigd of verplaatstnaar
					$dubbel[$row[BASIS_ID]] = 1;
					// staat de nieuwe les op dezelfde plek en is deze zichtbaar in dit rooster?
					if ($row[DAG] == $row[DAG2] && $row[UUR] == $row[UUR2] && $row[VIS]) {
						$row = $result->fetchRow();
						continue;
					} else if ($_GET['bw'] == 'd') { // verberg verplaatste lessen
						$row = $result->fetchRow();
						continue;
					} else {
						$extra = ' verplaatstnaar';
						$comment = '(naar '.print_diff($row);
						if ($row[NOTITIE2] != '') $comment .= ', '.htmlenc($row[NOTITIE2]);
						$comment .= ')';
					}
				}
			} else if (!$week_info[$j] && $_GET['bw'] != 'b' && $_GET['bw'] != 'x') { // deze dag valt uit
				$extra = ' vrijstelling';
				$comment = '(vrijstelling)';
			} else { // dit is een gewone les
				if ($row[NOTITIE]) $comment = ' ('.$row[NOTITIE].')';
			}

			$info = array();
			add_lv($info, $row[LESGROEPEN], $row[VAKKEN]);
			add($info, $row[DOCENTEN], ($row[WIJZ_ID] && $row[DOCENTEN2])?'<span class="unknown">DOC?</span>':'');
			add($info, $row[LOKALEN], ($row[WIJZ_ID] && $row[LOKALEN2])?'<span class="unknown">LOK?</span>':'');

			echo('<div class="les'.$extra.'">');
			if (count($info)) echo('<table><tr><td>'.implode('</td><td>/</td><td>', $info).'</td></tr></table>');
			if ($comment) echo('<div class="comment">'.$comment.'</div>');
			echo('<div class="clear"></div></div>');

			$row = $result->fetchRow();
		}
		echo('</td>'."\n");
	}
	echo('</tr>'."\n");
} 
?></table>
<div class="noprint small">Kleurcodes:
<? if ($_GET['bw'] == 'x') { ?>
<span class="legenda uitval">&nbsp;</span>&nbsp;oud,
<? } else { ?>
<span class="legenda uitval">&nbsp;</span>&nbsp;uitval,
<? } ?>
<span class="legenda gewijzigd">&nbsp;</span>&nbsp;gewijzigd,
<? if ($_GET['bw'] == 'x') { ?>
<span class="legenda extra">&nbsp;</span>&nbsp;nieuw,
<? } else { ?>
<span class="legenda extra">&nbsp;</span>&nbsp;extra,
<? } ?>
<span class="legenda verplaatstvan">&nbsp;</span>&nbsp;verplaatst van,
<span class="legenda verplaatstnaar">&nbsp;</span>&nbsp;verplaatst naar,
<span class="legenda vrijstelling">&nbsp;</span>&nbsp;vrijstelling,
<span class="legenda lokaalreservering">&nbsp;</span>&nbsp;lokaalreservering.
</div>
</div><? } ?>
<p>
<? if ($_GET['bw'] == 'x') { ?>
<span id="updateinfo">
<? if ($basis['file_id']) { ?>
Oud basisrooster <? echo(print_rev($basis['timestamp'], $basis['basis_id'])); ?>
<? } else { ?>
Er is geen oud basisrooster<? } ?>
<? if ($wijz['file_id']) { ?>, nieuw basisrooster <? echo(print_rev($wijz['timestamp'], $wijz['basis_id'])); } else { ?>, er is nog geen nieuw basisrooster voor deze week<? } ?>.
<? } else { ?>
<span id="updateinfo">Update basisrooster <? echo(print_rev($basis['timestamp'], $basis['basis_id'])); ?>
<? if ($wijz['file_id']) { ?>, wijzigingen <? echo(print_rev($wijz['timestamp'], $wijz['wijz_id'])); } else { ?>, er zijn geen roosterwijzigingen ingelezen voor deze week<? } ?>.
<? } ?>
<span class="onlyprint">Kijk op <? echo(get_baselink()); ?> voor het actuele rooster.</span>
</span>

<? html_end(); ?>
