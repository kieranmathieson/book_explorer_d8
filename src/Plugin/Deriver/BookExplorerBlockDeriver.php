<?php

/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 7/30/16
 * Time: 1:54 PM
 */

namespace Drupal\book_explorer\Plugin\Deriver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\book\BookManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;

class BookExplorerBlockDeriver extends DeriverBase implements ContainerDeriverInterface {
  /**
   * @var \Drupal\book\BookManagerInterface
   */
  private $bookManager;
  /**
   * @var |Drupal\Core\Entity\entityTypeManagerInterface
   */
  protected $entTypeManager;

  public function __construct(BookManagerInterface $bookManager,
                              EntityTypeManagerInterface $entTypeManager) {
    $this->bookManager = $bookManager;
    $this->entTypeManager = $entTypeManager;
  }

  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('book.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * @inheritDoc
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $books = $this->bookManager->getAllBooks();
    foreach ($books as $book) {
      $block_id = 'book_explorer_' . $book['bid'];
      $book_node = $this->entTypeManager->getStorage('node')->load($book['bid']);
//      drupal_set_message('Ders');
//      ksm($this->derivatives);
      $this->derivatives[$block_id] = $base_plugin_definition;
      $this->derivatives[$block_id]['admin_label'] = t('Book: @title', ['@title' => $book['title']]);
      $this->derivatives[$block_id]['config_dependencies']['config'] = array($book_node->getConfigDependencyName());
    }
    return $this->derivatives;
  }


}