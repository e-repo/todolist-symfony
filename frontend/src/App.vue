<template>
  <div class="wrapper">
    <aside class="l-sidebar">
      <div class="container-fluid">
        Side bar
      </div>
    </aside>
    <div class="content">
      <header class="header">
        <ul class="nav justify-content-end bg-light border-bottom">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Active</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
          </li>
        </ul>
      </header>
      <main class="main">
        <div class="container-fluid">
          Main content
        </div>
      </main>
      <footer class="footer bg-light border-top">
        <div class="container-fluid h-100">
          <div class="footer__wrapper">
            <span>Evening production 2022</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>

export default {
  name: 'App',
}
</script>

