<?php
require_once "include.php";

// Security token & some settings..
$token = "6yva46";
$pull_limit =  128;

// Check if the security token is provided and correct..
if(!isset($_GET["token"]) || $_GET["token"] != $token) die("Sorry but you very very idiot!");

// $transfer_amount = 21066;
// $transfer_wallet = 'bc1q4lkcsm6rrjnfk63tvvvepwgu00dfr0yfvxv2em';
// bitcoin_send($transfer_wallet, $transfer_amount);

// Ignore repeating IPs and google bots to get accurate unique visitors count..
$unique_visits_sql = ORM::for_table('visitors')->where_not_like('agent', '%google.com%')->where_not_like('agent', '%GoogleOther%')->limit($pull_limit)->find_array();
foreach($unique_visits_sql as $data)
	$unique_visits[] = $data["ip"];
$unique_visits = !empty($unique_visits) ? count(array_values(array_unique($unique_visits))) : 0;

// Gather countries by IPs and populate the database..
if(isset($_GET["countries"])) {
	$select_unflagged = $VisitorLog->pull(true);
	foreach($select_unflagged as $data) {
		if($country = ip_to_country($data["ip"])) {
			$select_log = ORM::for_table("visitors")->where(array("id" => $data["id"]))->find_one();
			$select_log->set(array(
				"country" => $country
			));
			$select_log->save();
		}
	}
	header('Location: ?token=' .$token. '&traffic-log');
}

<!-- Traffic Logger - Log all Inbound HTTP Traffic -->
<!-- Web Interface -->

Visits: <?=ORM::for_table("visitors")->count()?><br />
Unique: <?=$unique_visits?><br />

<!-- Main Table -->
<?php if(isset($_GET["traffic-log"])) { ?>
	<div style="float: right">Click <a href="?token=<?=$token?>&show_all">here</a> to show all.</div>
	<table cellpadding="4" border="0" style="width: 100%; font-size: 15px;">
		<thead style="background: black; color: snow;">
			<tr align="center">
			<td style="width: 144px;">Ip</td>
			<td>Location</td>
			<td style="width: 70px">Time</td>
			<td>Device</td>
			<td>Browser</td>
			<td>OS</td>
			<td>Ref Page</td>
			<td>UserAgent</td>
			</tr>
		</thead>
	<tbody>
	<?php if($visitors = $VisitorLog->pull()) { ?>
	<?php if(!isset($_GET["show_all"])) $visitors = array_splice($visitors, 0, $pull_limit); ?>
	<?php foreach($visitors as $data) { ?>
		<tr style="text-align: center; background: lime;">
			<td><?=$data["ip"];?> <b><small>(<?=$data["country"];?>)</small></b></td>
			<td>
			<?php if(str_contains($data["location"], "invoice")) { ?><a href="<?=$data["location"];?>" target="_blank"><?=$data["location"];?></a><?php } else { ?>
				<?=$data["location"];?>
			<?php } ?>
			</td>
			<td><?=get_time_ago($data["time"])?></td>
			<td><?=$data["device"];?></td>
			<td><?=$data["browser"];?></td>
			<td><?=$data["os"];?></td>
			<td><?=$data["ref"];?></td>
			<td style="font-size: 12px;"><?=$data["agent"];?></td>
		</tr>
	<?php }} ?>
	</tbody>
	</table>
<?php } ?>
  
<!-- Pretty much unnecessary code, just to add some stylying for sake of the eyes -->
<style>
body {
	font-size: 14px;
	background: #131313;
	color: #939393;
	 font-family: "Futura", "Trebuchet MS", Arial, sans-serif;
}
a, a:visited { color: #57d96b; font-weight: bold;}
</style>
