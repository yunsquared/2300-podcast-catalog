<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  <div id="header">
    <title><?php if (isset($title)) { echo $title . "-";} ?>Podcast Catalog</title>

    <link rel="stylesheet" type="text/css" href="styles/theme.css" media="all" />


    <?php if ( isset($scripts) ) {
    foreach ($scripts as $script) {
      echo "<script src=\"" . $script . "\" type=\"text/javascript\"></script>\n";
    }
  } ?>
  </div>

</head>
