<meta name="description" content="<?= $storeData['description'] ?>">
  <meta name="keywords" content="<?= $storeData['keywords'] ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <!-- Google -->
  <meta name="google" content="nositelinkssearchbox" />
  <meta name="google" content="notranslate" />

  <!-- Facebook -->
  <meta property="og:url" content="<?= ROOT ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars(TITLE) ?>" />
  <meta property="og:description" content="<?= $storeData['description'] ?>" />
  <meta property="og:image" content="<?php
                                      if (empty($storeData['image'])) {
                                        echo ROOT . "assets/images/logo.png";
                                      } else {
                                        echo ROOT . "assets/images/" . $storeData['image'];
                                      }
                                      ?>" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@gfydog">
  <meta name="twitter:title" content="<?= htmlspecialchars(TITLE) ?>">
  <meta name="twitter:description" content="<?= $storeData['description'] ?>">
  <meta name="twitter:image" content="<?php
                                      if (empty($storeData['image'])) {
                                        echo ROOT . "assets/images/logo.png";
                                      } else {
                                        echo ROOT . "assets/images/" . $storeData['image'];
                                      }
                                      ?>"/>

  <link rel="icon" type="image/png" href="<?php
                                          if (empty($storeData['icon'])) {
                                            echo ROOT . "assets/images/logo.png";
                                          } else {
                                            echo ROOT . "assets/images/" . $storeData['icon'];
                                          }
                                          ?>" /> 