<?php

/**
 * Demonstration of the various OAuth flows. You would typically do this
 * when an unknown user is first using your application. Instead of storing
 * the token and secret in the session you would probably store them in a
 * secure database with their logon details for your website.
 *
 * When the user next visits the site, or you wish to act on their behalf,
 * you would use those tokens and skip this entire process.
 *
 * The Sign in with Twitter flow directs users to the oauth/authenticate
 * endpoint which does not support the direct message permission. To obtain
 * direct message permissions you must use the "Authorize Application" flows.
 *
 * Instructions:
 * 1) If you don't have one already, create a Twitter application on
 *      https://dev.twitter.com/apps
 * 2) From the application details page copy the consumer key and consumer
 *      secret into the place in this code marked with (YOUR_CONSUMER_KEY
 *      and YOUR_CONSUMER_SECRET)
 * 3) Visit this page using your web browser.
 *
 * @author themattharris
 */

require '../tmhOAuth.php';
require '../tmhUtilities.php';

// To switch to production, update the file ../tmhOAuth.php line 36
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'YOUR_CONSUMER_KEY',
  'consumer_secret' => 'YOUR_CONSUMER_SECRET',
));

$here = tmhUtilities::php_self();
session_start();

function outputError($tmhOAuth) {
  echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
  tmhUtilities::pr($tmhOAuth);
}

// reset request?
if ( isset($_REQUEST['wipe'])) {
  session_destroy();
  header("Location: {$here}");

// already got some credentials stored?
} elseif ( isset($_SESSION['access_token']) ) {
	// We have all the required information to make calls to the Evernote Cloud API 
	// Learn more: dev.evernote.com
	print "<h2>Connection successful</h2><br />";
	print "<b>NoteStore URL: </b>" . $_SESSION['access_token']["edam_noteStoreUrl"];
	print "<br />";
	print "<b>Authentication token:</b> " . $_SESSION['access_token']["oauth_token"];
	print "<br /><br />";
	print "You can start using the <a href='http://dev.evernote.com'>Evernote Cloud API</a>";

// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth', ''), array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));

  if ($code == 200) {
    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    unset($_SESSION['oauth']);
    header("Location: {$here}");
  } else {
    outputError($tmhOAuth);
  }
// start the OAuth dance
} elseif ( isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize']) ) {
  $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

  $params = array(
    'oauth_callback'     => $callback
  );

  if (isset($_REQUEST['force_write'])) :
    $params['x_auth_access_type'] = 'write';
  elseif (isset($_REQUEST['force_read'])) :
    $params['x_auth_access_type'] = 'read';
  endif;

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth', ''), $params);

  if ($code == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);

    $authurl = $tmhOAuth->url("OAuth.action", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
    echo '<p>To complete the OAuth flow follow this URL: <a href="'. $authurl . '">' . $authurl . '</a></p>';
  } else {
    outputError($tmhOAuth);
  }
}

?>
<ul>
  <li><a href="?authenticate=1">Sign in with Evernote</a></li>
  <li><a href="?wipe=1">Start Over and delete stored tokens</a></li>
</ul>