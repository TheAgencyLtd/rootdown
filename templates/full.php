<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <?php if($page->attribute('bodyClass')) : ?>
    <body class="<?=$page->attribute('bodyClass')?>">
  <?php else : ?>
    <body>
  <?php endif; ?>

    <?php include "partials/header.php"; ?>

    <section class="container" role="main">
      <article class="twelve columns">
        <?=$page->content()?>
      </article>
    </section>

    <?php include "partials/footer.php"; ?>

  </body>
</html>
