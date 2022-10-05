<?php

namespace Drupal\fsu_strata_mercury_editor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media\MediaInterface;

/**
 * FsuStrataMercuryImagePath service.
 */
class FsuStrataMercuryImagePath {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a FsuStrataMercuryImagePath object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Return the path of the background image.
   *
   * @param \Drupal\media\MediaInterface $entity
   *   The Media entity to use.
   * @param string $image_style
   *   The ImageStyle path to get.
   *
   * @return string
   *   The file path.
   */
  public function getMediaImageStylePath(MediaInterface $entity, string $image_style) {
    $fid = $entity->getSource()->getSourceFieldValue($entity);
    /** @var \Drupal\file\FileInterface $file */
    $file = $this->entityTypeManager->getStorage('file')->load($fid);

    /** @var \Drupal\image\ImageStyleInterface $style */
    $style = $this->entityTypeManager->getStorage('image_style')->load($image_style);
    $file_path = $style->buildUrl($file->getFileUri());

    // This weird hack is to ensure that image derivatives are generated with
    // Focal Point module. Not sure why CSS url() isn't doing it, but this
    // seems to trigger it.
    file_exists($file_path);

    return $file_path;
  }

}
