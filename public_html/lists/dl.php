<?php
ob_start();
$er = error_reporting(0); # some ppl have warnings on
if ($_SERVER["ConfigFile"] && is_file($_SERVER["ConfigFile"])) {
	print '<!-- using '.$_SERVER["ConfigFile"].'-->'."\n";
  include $_SERVER["ConfigFile"];
} elseif ($_ENV["CONFIG"] && is_file($_ENV["CONFIG"])) {
	print '<!-- using '.$_ENV["CONFIG"].'-->'."\n";
  include $_ENV["CONFIG"];
} elseif (is_file("config/config.php")) {
	print '<!-- using config/config.php -->'."\n";
  include "config/config.php";
} else {
	print "Error, cannot find config file\n";
  exit;
}
error_reporting($er);

$id = sprintf('%d',$_GET["id"]);

$data = Sql_Fetch_Row_Query("select filename,mimetype,remotefile,description,size from {$tables["attachment"]} where id = $id");
if (is_file($attachment_repository. "/".$data[0])) {
	$file = $attachment_repository. "/".$data[0];
} elseif (is_file($data[2]) && filesize($data[2])) {
  $file = $data[2];
} else {
	$file = "";
}

if ($file && is_file($file)) {
  ob_end_clean();
  if ($data[1]) {
    header("Content-type: $data[1]");
  } else {
    header("Content-type: application/octetstream");
  }

  list($fname,$ext) = explode(".",basename($data[2]));
  header ('Content-Disposition: attachment; filename="'.basename($data[2]).'"');
  if ($data[4])
  	$size = $data[4];
  else
  	$size = filesize($file);

  if ($size) {
	  header ("Content-Length: " . $size);
    $fsize = $size;
  }
  else
  	$fsize = 4096;
  $fp = fopen($file,"r");
  while ($buffer = fread($fp,$fsize)) {
    print $buffer;
  flush();
  }
  fclose ($fp);
	exit;
} else {
  FileNotFound();
}

