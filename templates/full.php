<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <body>
    <?php include "partials/header.php"; ?>
    <div role="main">
      <section class="container">
        <article class="twelve columns">
          <?=$page->content()?>
        </article>
      </section>
    </div>
    <?php include "partials/footer.php"; ?>
  </body>
</html>
