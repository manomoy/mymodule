<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Drupal\Core\Entity\Query\QueryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MyController extends ControllerBase {
  
  public function getNodeJSON($siteapikey_input=NULL, $nid_input=NULL) {
  	// Set the content type to Page
  	$type = 'page';
    if(is_numeric($nid_input)) {
      // get Site API Key as saved in system
      $config = $this->config('siteapikey.configuration');
      $siteapikey = $config->get('siteapikey');
      
      // Compare site api key from input URL with that of the system
      if($siteapikey_input == $siteapikey) {
      	// Entity Query
		$query = \Drupal::entityQuery('node');
		$query->condition('type', $type);
		$query->condition('nid', $nid_input);
		$result = $query->execute();
		if(isset($result) && !empty($result)) {
			$result_key = array_keys($result);
			$node = node_load($result_key[0]);
		}
		// If the node doesn't exist, show 403 access denied.
		if(empty($node)) {
		  throw new AccessDeniedHttpException();
		}
		// Convert the node data into JSON using Drupal's serializer service
		$serializer = \Drupal::service('serializer');
		$data = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
		echo $data; die();
      }
      else {
      	echo 'Incorrect Site API Key'; die();
      }
    }
    else {
      echo 'Please enter numeric value for Node ID'; die();
    }
  }
}
