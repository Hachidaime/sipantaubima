<!-- prettier-ignore -->
{extends 'Templates/mainlayout.tpl'}

{block 'style'}
{literal}
<style>
  /* https://css-tricks.com/stripes-css/ */
  .uc-container {
    background: repeating-linear-gradient(
      90deg,
      var(--yellow),
      var(--yellow) 50px,
      var(--dark) 50px,
      var(--dark) 100px
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
    <div class="card rounded-0">
      <div class="card-body p-3 bg-gradient-dark">
        <div class="jumbotron m-0 bg-light text-center">
          <h1 class="display-4">
            <i class="fas fa-tools"></i><br />UNDER CONSTRUCTION
          </h1>
          <a href="{$smarty.const.BASE_URL}" class="btn bg-gradient-yellow">
            Back
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
