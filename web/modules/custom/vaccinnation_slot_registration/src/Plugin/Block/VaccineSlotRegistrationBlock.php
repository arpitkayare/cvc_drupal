<?php

namespace Drupal\vaccinnation_slot_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;

/**
 * Provides an slot registration button block.
 *
 * @Block(
 *   id = "vaccinnation_slot_registration_button",
 *   admin_label = @Translation("Vaccine slot registration button block"),
 *   category = @Translation("custom")
 * )
 */
class VaccineSlotRegistrationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $uid = \Drupal::currentUser()->id();
    $termObj = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $query = \Drupal::database()->select('vaccinnation_slot_registration_records', 'vs');
    $query->fields('vs', ['uid','location','city']);
    $query->condition('vs.uid', $uid ,'=');
    $user_data = $query->execute()->fetchAssoc();
    $url = Url::fromRoute('vaccinnation_slot_registration.cancle_slot', [], ['absolute' => TRUE]);
    if($user_data){
      $centreLocation = $termObj->load($user_data['location'])->getName();
      $centreCity = $termObj->load($user_data['city'])->getName();
      $slotDetails = '<div id="booked-slot-details"><strong class="slot-details">Already Registered for <br>'.$centreLocation.' '.$centreCity.'</strong></div>';
      $build['markup'] = [
        '#type' => 'markup',
        '#markup' => $this->t($slotDetails)
      ];
      $build['link'] = [
        '#title' => $this->t('Cancle booking'),
        '#type' => 'link',
        '#url' => $url,
        '#attributes' => [
          'class' => ['button', 'user-slot-cancle']
        ]
      ];
    }else{
      $node = \Drupal::routeMatch()->getParameter('node');
      if($node){
        $termId = $node->get('field_location')->getValue()[0]['target_id'];
        $pTermId = $termObj->load($termId)->parent->target_id;
      }
      $url = Url::fromRoute('vaccinnation_slot_registration.slot_register', ['tid' => $termId, 'ptid' => $pTermId], ['absolute' => TRUE]);
      $build['link'] = [
        '#title' => $this->t('Register'),
        '#type' => 'link',
        '#url' => $url,
        '#attributes' => [
          'class' => ['button', 'user-slot-register']
        ]
      ];
    }
    $build['#cache']['max-age' ] = 0;
    return $build;
  }
}

