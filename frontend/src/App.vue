<template>
  <div class="wrapper">
    <aside class="l-sidebar">
      <div class="container-fluid">
        <div class="logo">
          <a class="logo__link text-decoration-none text-warning" href="#">
            <font-awesome-icon class="me-2" icon="tractor" />
            Evening production
          </a>
        </div>
        <div class="nav-sidebar mt-3">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link text-white active" href="#">
                <font-awesome-icon class="me-2" icon="house" />
                Главная
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">
                <font-awesome-icon class="me-2" icon="user-group" />
                Пользователи
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">
                <font-awesome-icon class="me-2" icon="list" />
                Задачи
              </a>
              <div class="container-fluid">
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link text-white" href="#">Новые</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-white" href="#">Выполненные</a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
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

