<!-- prettier-ignore -->
{extends 'Templates/blank.tpl'}

{block 'style'}
{literal}
<style>
  /* https://css-tricks.com/stripes-css/ */
  .uc-container {
    background: repeating-linear-gradient(
      90deg,
      var(--yellow),
      var(--yellow) 50px,
      #001f3f 50px,
      #001f3f 100px
    );
    background-position: center;
    height: 100vh;
    background-size: cover;
    width: 100%;
  }
</style>
{/literal}
<!-- prettier-ignore -->
{/block} 

{block 'content'}
<div class="uc-container d-flex justify-content-center align-content-center">
  <div class="d-flex flex-column justify-content-center">
    <div class="card">
      <div class="card-body p-3 bg-gradient-dark border border-yellow">
        <div class="jumbotron m-0 bg-warning text-center">
          <h1 class="display-4">
            <i class="fas fa-exclamation-triangle"></i>
          </h1>
          <h1 class="display-4">
            <b>UNDER CONSTRUCTION</b>
          </h1>
          <a
            href="{$smarty.const.BASE_URL}"
            class="btn btn-flat bg-gradient-warning"
          >
            Back to Home
          </a>
        </div>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
</div>
<!-- /.login-box -->
<!-- prettier-ignore -->
{/block}
