<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <body class="home">

    <?php include "partials/header.php"; ?>

    <section class="container" role="main">
      <article class="twelve columns">
        <?=$page->content()?>
      </article>
    </section>

    <section class="container" role="main">
      <nav>
        <ul>
          <?php foreach($page->pagelist('/docs') as $page) : ?>
            <li>
              <h3><?=$page->title()?></h3>
              <p><?=$page->description()?></p>
              <p><a href="<?=$page->path()?>">Read more ...</a></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>

    <?php include "partials/footer.php"; ?>

  </body>
</html>
