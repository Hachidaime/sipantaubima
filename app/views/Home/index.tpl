<!-- prettier-ignore -->
{extends file='Templates/mainlayout.tpl'}

{block 'content'}
<div class="triangle">
  <div class="container d-flex flex-column flex-md-row">
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/login">
          <img
            src="{$smarty.const.IMG_LINK1}?t={$smarty.now}"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Pembangunan<br />Jalan & Jembatan</div>
    </div>
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/uc">
          <img
            src="{$smarty.const.IMG_LINK2}?t={$smarty.now}"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Pemeliharaan<br />Talud & Jembatan</div>
    </div>
    <div class="flex-fill px-2 py-5">
      <div class="text-center">
        <a href="{$smarty.const.BASE_URL}/uc">
          <img
            src="{$smarty.const.IMG_LINK3}?t={$smarty.now}"
            class="rounded-circle"
            alt="..."
          />
        </a>
      </div>
      <div class="text-center py-3">Penerangan<br />Jalan Umum</div>
    </div>
  </div>
</div>

<div
  id="carouselExampleIndicators"
  class="carousel slide mt-5 container"
  data-ride="carousel"
>
  <ol class="carousel-indicators">
    <li
      data-target="#carouselExampleIndicators"
      data-slide-to="0"
      class="active"
    ></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img
        src="{$smarty.const.IMG_SLIDE1}?t={$smarty.now}"
        class="d-block w-100"
        alt="..."
      />
    </div>
    <div class="carousel-item">
      <img
        src="{$smarty.const.IMG_SLIDE2}?t={$smarty.now}"
        class="d-block w-100"
        alt="..."
      />
    </div>
    <div class="carousel-item">
      <img
        src="{$smarty.const.IMG_SLIDE3}?t={$smarty.now}"
        class="d-block w-100"
        alt="..."
      />
    </div>
  </div>
  <a
    class="carousel-control-prev"
    href="#carouselExampleIndicators"
    role="button"
    data-slide="prev"
  >
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a
    class="carousel-control-next"
    href="#carouselExampleIndicators"
    role="button"
    data-slide="next"
  >
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
{/block}
