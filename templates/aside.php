<html>
  <head>
    <?php include "partials/head.php"; ?>
  </head>
  <body>
    <?php include "partials/header.php"; ?>
    <div role="main">
      <section class="container">
        <article class="eight columns">
          <nav class="breadcrumb">
            <ul>
              <li><a href="/">Home</a>
                <?php \Rootdown\Site::breadcrumb(); ?>
              </li>
            </ul>
          </nav>
          <?=$page->content()?>
        </article>
        <aside class="four columns">
          <nav class="channel">
            <?php \Rootdown\Site::channel(); ?>
          </nav>
        </aside>
      </section>
    </div>
    <?php include "partials/footer.php"; ?>
  </body>
</html>
