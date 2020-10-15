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
                  class="bi bi-lock-fill"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M2.5 9a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V9z"
                  />
                  <path
                    fill-rule="evenodd"
                    d="M4.5 4a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z"
                  />
                </svg>
              </h1>
              <h1 class="display-4 font-weight-bold text-center">403</h1>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-7">
              <h3 class="text-center text-sm-left">Oops! Access Denied</h3>
              <p class="lead">
                You don't have permission to access the page you asked for.
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
