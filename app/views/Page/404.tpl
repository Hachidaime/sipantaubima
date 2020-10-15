<!-- prettier-ignore -->
{extends 'Templates/blank.tpl'}

{block 'content'}
<div class="wrapper my-5">
  <!-- Main content -->
  <section class="content">
    <div class="container">
      <div class="jumbotron jumbotron-fluid m-0 rounded-0 bg-white">
        <div class="container">
          <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-5">
              <h1 class="display-1 text-center">
                <svg
                  width="1em"
                  height="1em"
                  viewBox="0 0 16 16"
                  class="bi bi-compass"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8 15.016a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13zm0 1a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z"
                  />
                  <path
                    d="M6 1a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H7a1 1 0 0 1-1-1zm.94 6.44l4.95-2.83-2.83 4.95-4.95 2.83 2.83-4.95z"
                  />
                </svg>
              </h1>
              <h1 class="display-4 font-weight-bold text-center">404</h1>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-7">
              <h3 class="text-center text-sm-left">Oops! Page Not Found</h3>
              <p class="lead">
                The Web server cannot find the page you asked for. Please check
                the URL to ensure that the path is correct.
              </p>
              <p>
                Please contact the server's administrator if this problem
                persists.
              </p>
              <a
                href="{$smarty.const.BASE_URL}"
                class="btn btn-flat bg-gradient-warning"
                >Back to Home</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{/block}
