<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <body class="home">

    <?php include "partials/header.php"; ?>

    <section class="hero"></section>

    <section class="container" role="main">
      <article class="twelve columns">
        <?=$page->content()?>
      </article>
    </section>

    <?php include "partials/footer.php"; ?>

  </body>
</html>
