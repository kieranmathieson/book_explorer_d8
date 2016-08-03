<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 7/24/16
 * Time: 1:24 PM
 */

namespace Drupal\book_explorer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\book\BookManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides blocks for book tree menus.
 *
 * @Block(
 *   id = "book_explorer_book_tree",
 *   admin_label = @Translation("Book explorer"),
 *   category = @Translation("Other"),
 *   deriver = "Drupal\book_explorer\Plugin\Deriver\BookExplorerBlockDeriver"
 * )
 */
class BookExplorerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var BookManagerInterface
   */
  private $bookManager;

  /**
   * @var |Drupal\Core\Entity\entityTypeManagerInterface
   */
  protected $entTypeManager;

  /**
   * BookExplorerBlock constructor.
   */
  function __construct(
      array $configuration, $plugin_id, $plugin_definition,
      BookManagerInterface $bookManager,
      EntityTypeManagerInterface $entTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->bookManager = $bookManager;
    $this->entTypeManager = $entTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('book.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Builds and returns the renderable array for this block plugin.
   * Array contains a tree for a book. Block id (machine name) is
   * book_explorer_BID.
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {
    //Get an id from the deriver declared for this block.
    $block_id = $this->getDerivativeId();
    //Extract the book id.
    $pieces = explode('_', $block_id);
    $bid = $pieces[ sizeof($pieces) - 1 ];
    //Load and render the book tree.
    $structure = $this->bookManager->bookTreeAllData($bid);
    $output = $this->bookManager->bookTreeOutput($structure);
    $output['#attached'] = [
      'library' =>  [
        'book_explorer/menu-tree'
      ],
    ];
    return [
      $output
    ];

  }

}