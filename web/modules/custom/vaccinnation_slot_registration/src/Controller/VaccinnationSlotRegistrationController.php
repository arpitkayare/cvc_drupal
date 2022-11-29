<?php

namespace Drupal\vaccinnation_slot_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

/**
 * Returns responses for vaccinnation slot registration routes.
*/
class VaccinnationSlotRegistrationController extends ControllerBase {

  /**
   * Adds registration details into database.
   */
  public function registerUser() {
    $request = \Drupal::request();
    $tid = $request->query->get('tid');
    $ptid = $request->query->get('ptid');
    $uid = \Drupal::currentUser()->id();
    $data = [];
    if( $uid && $ptid && $tid ){
      $data = [
        [
          'uid' => $uid,
          'location' => $tid,
          'city' => $ptid
        ]
      ];
    $database = \Drupal::database();
    $query = $database->insert('vaccinnation_slot_registration_records')->fields(['uid','location','city']);
    foreach ($data as $value) {
      $query->values($value);
    }
    $query->execute();
    $response = new RedirectResponse($request->headers->get('referer'));
    $messenger = \Drupal::messenger()->addStatus('Succesfully registered for the vaccination.');
    
  }else{
    $response = new RedirectResponse($request->headers->get('referer'));
    $messenger = \Drupal::messenger()->addError('Error occused please try again later.');
  }
  return $response;
  }

  /**
   * Remove registration details into database.
   */
  function registerUserCancle(){
    $uid = \Drupal::currentUser()->id();
    $query = \Drupal::database()->delete('vaccinnation_slot_registration_records');
    $query->condition('uid', $uid );
    $query->execute();
    $response = new RedirectResponse(\Drupal::request()->headers->get('referer'));
    $messenger = \Drupal::messenger()->addStatus('Succesfully cancelled your slot.');
    return $response;
  }
  
}
