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

    <section class="container" role="main">
      <nav>
        <ul>
          <?php foreach($page->pagelist('/docs') as $page) : ?>
            <li>
              <h2><?=$page->title()?></h2>
              <p><?=$page->description()?></p>
              <a href="<?=$page->path()?>">Read more ...</a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>

    <?php include "partials/footer.php"; ?>

  </body>
</html>
