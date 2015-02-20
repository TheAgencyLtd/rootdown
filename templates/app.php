<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <body>

    <?php include "partials/header.php"; ?>

    <section class="container" role="main">
      <article class="eight columns">
        <nav class="breadcrumb">
          <ul>
            <li><a href="/">Home</a>
              <?php $page->breadcrumb(); ?>
            </li>
          </ul>
        </nav>
        <?=$page->content()?>
        <pre><?php var_dump($page->data()); ?></pre>
      </article>
      <aside class="four columns">
        <nav class="channel">
          <?php $page->channel(); ?>
        </nav>
      </aside>
    </section>

    <?php include "partials/footer.php"; ?>

  </body>
</html>
