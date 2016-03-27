<?php
require_once('vendor/autoload.php');

if (!PRODUCTION)
{
  error_reporting(E_ALL);
  ini_set('display_errors','On');
}

function phoxy_conf()
{
  $ret = phoxy_default_conf();
  $ret["api_xss_prevent"] = PRODUCTION;

  return $ret;
}

function default_addons()
{
  $ret =
  [
    "cache" => PRODUCTION ? ['global' => '10m'] : "no",
    "result" => "canvas",
  ];
  return $ret;
}

include('phoxy/phoxy_return_worker.php');
phoxy_return_worker::$add_hook_cb = function($that)
{
  global $USER_SENSITIVE;

  if ($USER_SENSITIVE)
    $that->obj['cache'] = 'no';
};

phpsql\OneLineConfig(conf()->db->connection_string);

//instagram api
use MetzWeb\Instagram\Instagram;
$instagram = new Instagram(array(
    'apiKey' => conf()->instagram_api->apiKey,
    'apiSecret' => conf()->instagram_api->apiSecret,
    'apiCallback' => conf()->instagram_api->apiCallback // must point to success.php
));
function instagram()
{
  global $instagram;
  return $instagram;
}
include('phoxy/load.php');
